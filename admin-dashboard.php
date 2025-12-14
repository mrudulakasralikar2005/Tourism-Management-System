<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
</head>
<body>
  <h1>Swagat, <?php echo htmlspecialchars($_SESSION['admin']); ?></h1>
  <a href="logout.php">Logout</a>
  
</body>
</html>
