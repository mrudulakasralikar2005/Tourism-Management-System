<?php
$host = "localhost";
$user = "root";
$pass = ""; // your db password
$db = "tourism_auth"; // your db name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Basic server-side validation
if(strlen($name) < 3 || !preg_match("/^[A-Z]/", $name)) {
  echo "invalid_name"; exit;
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "invalid_email"; exit;
}
if(strlen($subject) < 3) {
  echo "invalid_subject"; exit;
}
if(strlen($message) < 10) {
  echo "invalid_message"; exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
  echo "success";
} else {
  echo "error";
}

$stmt->close();
$conn->close();
?>
