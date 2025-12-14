<?php
require 'db.php';

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (
            empty($_POST['fullname']) ||
            empty($_POST['email']) ||
            empty($_POST['password'])
        ) {
            echo "error";
            exit;
        }

        $fullname = trim($_POST["fullname"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "exist";
            exit;
        }

        // Insert user
        $stmt = $conn->prepare(
            "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)"
        );

        if ($stmt->execute([$fullname, $email, $hashedPassword])) {
            echo "success";
        } else {
            echo "error";
        }
    }
} catch (PDOException $e) {
    echo "error";
}
?>
