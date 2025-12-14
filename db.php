<?php
$host = "localhost";
$dbname = "tourism_auth";
$username = "root";
$password = "";

$conn = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $username,
    $password
);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
