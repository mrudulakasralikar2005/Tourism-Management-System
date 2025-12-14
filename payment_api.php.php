<?php
include 'db_connect.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tourism_demo";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

$name    = $data["name"] ?? "";
$card    = $data["card"] ?? "";
$expiry  = $data["expiry"] ?? "";
$cvv     = $data["cvv"] ?? "";
$email   = $data["email"] ?? "";   // USER EMAIL
$phone   = $data["phone"] ?? "";   // USER PHONE
$amount  = $data["amount"] ?? "";

// Validate fields
if (!$name || !$card || !$expiry || !$cvv || !$email || !$phone || !$amount) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

// Generate Payment ID
$payment_id = "PAY" . rand(100000, 999999);

// Save in DB
$sql = "INSERT INTO demo_payments (name, card_number, expiry_date, cvv, email, phone, amount, payment_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssis", $name, $card, $expiry, $cvv, $email, $phone, $amount, $payment_id);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to save payment"]);
    exit();
}

// ---------------------------------
// 1️⃣ SEND EMAIL RECEIPT TO USER
// ---------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$email_status = false;

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yourgmail@gmail.com';   // YOUR Gmail (sender)
    $mail->Password   = 'your-app-password';     // YOUR App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // IMPORTANT:
    // Sender = YOU (your Gmail)
    // Receiver = USER (their email)
    $mail->setFrom('yourgmail@gmail.com', 'Travely Payments'); 
    $mail->addAddress($email);   // RECEIPT will go to USER email

    $mail->isHTML(true);
    $mail->Subject = "Your Payment Receipt – ₹$amount";
    $mail->Body = "
        <h2>Payment Successful</h2>
        <p>Dear $name,</p>
        <p>Your payment of ₹<b>$amount</b> has been successfully processed.</p>
        <p><b>Payment ID:</b> $payment_id</p>
        <br>
        <p>Thank you for choosing Travely.</p>
    ";

    $mail->send();
    $email_status = true;

} catch (Exception $e) {
    $email_status = false;
}

// ---------------------------------
// 2️⃣ SEND SMS RECEIPT TO USER
// ---------------------------------
$api_key = "YOUR_FAST2SMS_API_KEY";  // Add your Fast2SMS API key

$message = "Hi $name, your payment of Rs $amount is successful. Payment ID: $payment_id - Travely";

$fields = [
    "sender_id" => "TXTIND",
    "message" => $message,
    "language" => "english",
    "route" => "v3",
    "numbers" => $phone
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($fields),
    CURLOPT_HTTPHEADER => [
        "authorization: $api_key",
        "content-type: application/json"
    ],
]);

$sms_response = curl_exec($curl);
curl_close($curl);

// ---------------------------------
// 3️⃣ Final response
// ---------------------------------
echo json_encode([
    "status" => "success",
    "message" => "Payment Successful",
    "payment_id" => $payment_id,
    "email_sent" => $email_status,
    "sms_sent" => true
]);

$stmt->close();
$conn->close();
?>
