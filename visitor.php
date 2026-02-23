<?php
function get_ip() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$ipsFile = $dataDir . '/ips.json';
$dailyFile = $dataDir . '/daily.json';
$ip = get_ip();
$today = date('Y-m-d');
$ips = ['count'=>0,'ips'=>[]];
$daily = ['date'=>$today,'count'=>0,'ips'=>[]];
if (file_exists($ipsFile)) {
    $content = file_get_contents($ipsFile);
    $j = json_decode($content, true);
    if (is_array($j)) $ips = array_merge($ips, $j);
}
if (file_exists($dailyFile)) {
    $content = file_get_contents($dailyFile);
    $j = json_decode($content, true);
    if (is_array($j)) $daily = array_merge($daily, $j);
}
if (!isset($daily['date']) || $daily['date'] !== $today) {
    $daily = ['date'=>$today,'count'=>0,'ips'=>[]];
}
$changed = false;
if (!array_key_exists($ip, $daily['ips'])) {
    $daily['ips'][$ip] = true;
    $daily['count'] = count($daily['ips']);
    $changed = true;
}
if (!array_key_exists($ip, $ips['ips'])) {
    $ips['ips'][$ip] = date('c');
    $ips['count'] = count($ips['ips']);
    $changed = true;
}
if ($changed) {
    file_put_contents($ipsFile, json_encode($ips, JSON_PRETTY_PRINT), LOCK_EX);
    file_put_contents($dailyFile, json_encode($daily, JSON_PRETTY_PRINT), LOCK_EX);
}
header('Content-Type: application/json');
echo json_encode(['daily'=>$daily['count'],'all'=>$ips['count']]);