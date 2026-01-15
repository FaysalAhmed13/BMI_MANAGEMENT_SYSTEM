<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/auth.php";

$conn = db();
if (!empty($_COOKIE['remember_token'])) {
  $tok = $_COOKIE['remember_token'];
  $stmt = $conn->prepare("DELETE FROM auth_tokens WHERE token_hash = SHA2(?,256)");
  $stmt->bind_param("s", $tok);
  $stmt->execute();
}
session_unset();
session_destroy();
setcookie('remember_token', '', time()-3600, '/', '', false, true);
header("Location: " . BASE_URL . "/login.php");
exit;
?>