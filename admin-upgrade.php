<?php
require 'db.php';

// Username jisko upgrade karna hai
$username = 'admin';
$newPasswordPlain = 'admin123';  // jo password hai tumhara

// Hash bcrypt se
$newHash = password_hash($newPasswordPlain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
$stmt->execute([$newHash, $username]);

echo "Password upgraded successfully!";
?>
