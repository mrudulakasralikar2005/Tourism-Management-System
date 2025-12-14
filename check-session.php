<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION["user"])) {
  echo json_encode([
    "loggedIn" => true,
    "fullname" => $_SESSION["user"]["fullname"]
  ]);
} else {
  echo json_encode(["loggedIn" => false]);
}
?>
