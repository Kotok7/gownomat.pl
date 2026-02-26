<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

function err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

$TURNSTILE_SECRET = getenv('TURNSTILE_SECRET');
$DISCORD_WEBHOOK  = getenv('DISCORD_WEBHOOK_URL');

if (!$TURNSTILE_SECRET || !$DISCORD_WEBHOOK) {
    err('Server misconfiguration', 500);
}

$token = trim($_POST['cf_turnstile_response'] ?? '');
if ($token === '') err('Missing Turnstile token');

$ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
$postFields = [
    'secret'   => $TURNSTILE_SECRET,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
];

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => http_build_query($postFields),
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
if ($response === false) {
    curl_close($ch);
    err('Turnstile verification service error', 502);
}
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
if (empty($data) || empty($data['success'])) {
    err('Turnstile verification failed', 403);
}

$type = trim($_POST['type'] ?? 'message');

if ($type === 'order') {
    $name    = trim($_POST['name'] ?? '-');
    $email   = trim($_POST['email'] ?? '-');
    $address = trim($_POST['address'] ?? '-');
    $delivery = trim($_POST['delivery'] ?? '-');
    $delivery_ltc = trim($_POST['delivery_ltc'] ?? '-');
    $order_raw = $_POST['order'] ?? '[]';

    $name    = preg_replace('/[\x00-\x1F\x7F]/u', '', mb_substr($name, 0, 128));
    $email   = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '-';
    $address = preg_replace('/[\x00-\x1F\x7F]/u', '', mb_substr($address, 0, 1024));

    $order_arr = json_decode($order_raw, true);
    $order_text = '';
    if (is_array($order_arr) && count($order_arr)) {
        foreach ($order_arr as $it) {
            $iname = isset($it['name']) ? preg_replace('/[\x00-\x1F\x7F]/u', '', mb_substr($it['name'],0,128)) : 'item';
            $ikw = isset($it['kg']) ? $it['kg'] : '-';
            $iprice = isset($it['price']) ? $it['price'] : '-';
            $order_text .= "{$iname} — {$ikw} kg — {$iprice} LTC\n";
        }
    } else {
        $order_text = 'Brak pozycji';
    }

    $fields = [
        ['name' => 'Klient', 'value' => $name, 'inline' => true],
        ['name' => 'Email', 'value' => $email, 'inline' => true],
        ['name' => 'Adres', 'value' => mb_substr($address,0,1024)],
        ['name' => 'Dostawa', 'value' => $delivery . ' (' . $delivery_ltc . ' LTC)'],
        ['name' => 'Zamówienie', 'value' => mb_substr($order_text,0,1024)]
    ];

    $embedTitle = 'Nowe ZAMÓWIENIE z gownomat.pl';
} else {
    $n = trim($_POST['name'] ?? '');
    $m = trim($_POST['message'] ?? '');

    if ($m === '') err('Message required');

    $n = preg_replace('/[\x00-\x1F\x7F]/u', '', mb_substr($n, 0, 64));
    $m = preg_replace('/[\x00-\x1F\x7F]/u', '', mb_substr($m, 0, 1900));

    $fields = [
        ['name' => 'Nick', 'value' => $n ?: '-', 'inline' => true],
        ['name' => 'Wiadomość', 'value' => mb_substr($m, 0, 1024)]
    ];

    $embedTitle = 'Nowa wiadomość z gownomat.pl';
}

$payload = [
    'username' => 'GOWNOMAT BOT',
    'embeds' => [[
        'title' => $embedTitle,
        'fields' => $fields,
        'timestamp' => date('c')
    ]]
];

$ch = curl_init($DISCORD_WEBHOOK);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10
]);

$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($resp === false || $code < 200 || $code >= 300) {
    err('Discord webhook failed' . ($curlErr ? ': ' . $curlErr : ''), 500);
}

echo json_encode(['ok' => true]);
exit;