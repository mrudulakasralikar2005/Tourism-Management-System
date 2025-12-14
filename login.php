<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"] ?? '';
  $password = $_POST["password"] ?? '';

  if (empty($email) || empty($password)) {
    echo "missing_fields";
    exit;
  }

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user"] = [
      "id" => $user["id"],
      "fullname" => $user["fullname"],
      "email" => $user["email"]
    ];
    echo "success";
  } else {
    echo "invalid";
  }
}
?>
