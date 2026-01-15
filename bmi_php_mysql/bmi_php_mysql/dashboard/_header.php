<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/helpers.php";
require_login();
$me = current_user();
session_enforce_timeout();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo e($title ?? 'Dashboard'); ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
  <div class="container">
    <div class="nav">
      <div class="brand">
        <div class="badge">D</div>
        <div>
          <div style="font-weight:900">Dashboard</div>
          <div class="muted" style="font-size:12px"><?php echo e($me['name']); ?> • <?php echo e($me['user_type']); ?></div>
        </div>
      </div>
      <div class="navlinks">
        <a class="btn small" href="<?php echo BASE_URL; ?>/index.php">Public Home</a>
        <a class="btn small danger" href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
      </div>
    </div>

    <div class="grid" style="grid-template-columns:.30fr .70fr">
      <div class="card sidebar">
        <a class="<?php echo ($active ?? '')==='home'?'active':''; ?>" href="<?php echo BASE_URL; ?>/dashboard/index.php">Overview</a>
        <a class="<?php echo ($active ?? '')==='bmi'?'active':''; ?>" href="<?php echo BASE_URL; ?>/dashboard/bmi.php">BMI Records (CRUD)</a>
        <?php if (($me['user_type'] ?? 'user')==='admin'): ?>
          <a class="<?php echo ($active ?? '')==='users'?'active':''; ?>" href="<?php echo BASE_URL; ?>/dashboard/users.php">Users (CRUD)</a>
        <?php endif; ?>
        <div class="hr"></div>
        <div class="muted" style="font-size:12px">Session auto-expires after 5 minutes of inactivity.</div>
      </div>

      <div class="card">
