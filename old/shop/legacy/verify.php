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

$token   = trim($_POST['cf_turnstile_response'] ?? '');
$name    = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($token === '') err('Missing Turnstile token');
if ($message === '') err('Message required');

if (mb_strlen($name) > 64) $name = mb_substr($name, 0, 64);
if (mb_strlen($message) > 1900) $message = mb_substr($message, 0, 1900);

$message = preg_replace('/[\x00-\x1F\x7F]/u', '', $message);
$name    = preg_replace('/[\x00-\x1F\x7F]/u', '', $name);

$ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'secret'   => $TURNSTILE_SECRET,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
    ])
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (empty($data['success'])) err('Turnstile verification failed', 403);

$payload = [
    'username' => 'Gównomat messages',
    'embeds' => [[
        'title' => 'Nowa wiadomość z GÓWNOMATU (gownomat.pl)',
        'fields' => [
            ['name' => 'Nick', 'value' => $name ?: '-', 'inline' => true],
            ['name' => 'Wiadomość', 'value' => mb_substr($message, 0, 1024)]
        ],
        'timestamp' => date('c')
    ]]
];

$ch = curl_init($DISCORD_WEBHOOK);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true
]);

curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code < 200 || $code >= 300) err('Discord webhook failed', 500);

echo json_encode(['ok' => true]);