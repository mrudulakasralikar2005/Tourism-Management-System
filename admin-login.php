<?php

session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "no_post";
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo "missing_fields";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "no_admin_found";
    exit;
}

$stored = $admin['password'];

// Agar stored hash purana MD5 ho (length 32), to MD5 se verify karo, fir upgrade karo
if (strlen($stored) === 32) {
    if (md5($password) === $stored) {
        // Upgrade to bcrypt
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $upd->execute([$newHash, $username]);

        $_SESSION['admin'] = $admin['username'];
        echo "success";
    } else {
        echo "invalid";
    }
} else {
    // Assume bcrypt
    if (password_verify($password, $stored)) {
        $_SESSION['admin'] = $admin['username'];
        echo "success";
    } else {
        echo "invalid";
    }
}
?>
