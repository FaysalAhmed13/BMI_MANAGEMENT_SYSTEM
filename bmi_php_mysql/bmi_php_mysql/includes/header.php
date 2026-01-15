<?php
require_once __DIR__ . '/../config/app.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$logged = !empty($_SESSION['user_id']);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars($title ?? 'BMI System'); ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
  <div class="container">
    <div class="nav">
      <div class="brand">
        <div class="badge">B</div>
        <div>
          <div style="font-weight:900">BMI System</div>
          <div class="muted" style="font-size:12px">PHP + MySQL • Cookies & Sessions</div>
        </div>
      </div>
      <div class="navlinks">
        <a class="btn small" href="<?php echo BASE_URL; ?>/index.php">Home</a>
        <?php if (!$logged): ?>
          <a class="btn small" href="<?php echo BASE_URL; ?>/login.php">Login</a>
          <a class="btn small primary" href="<?php echo BASE_URL; ?>/register.php">Sign Up</a>
        <?php else: ?>
          <a class="btn small blue" href="<?php echo BASE_URL; ?>/dashboard/index.php">Dashboard</a>
          <a class="btn small danger" href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
        <?php endif; ?>
      </div>
    </div>
