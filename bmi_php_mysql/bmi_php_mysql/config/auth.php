<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/app.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Auto session expiry after 5 minutes of inactivity
define('SESSION_TIMEOUT_SECONDS', 300);

function session_enforce_timeout() {
  if (!empty($_SESSION['user_id'])) {
    $now = time();
    if (!empty($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS)) {
      session_unset();
      session_destroy();
      setcookie('remember_token', '', time()-3600, '/', '', false, true);
      header("Location: " . BASE_URL . "/login.php?expired=1");
      exit;
    }
    $_SESSION['last_activity'] = $now;
  }
}

function current_user() {
  if (!empty($_SESSION['user_id'])) {
    return [
      'id' => $_SESSION['user_id'],
      'name' => $_SESSION['name'] ?? '',
      'email' => $_SESSION['email'] ?? '',
      'user_type' => $_SESSION['user_type'] ?? 'user',
      'status' => $_SESSION['status'] ?? 'active'
    ];
  }
  return null;
}

function require_login() {
  session_enforce_timeout();

  if (!empty($_SESSION['user_id'])) return;

  if (!empty($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $conn = db();
    $stmt = $conn->prepare("SELECT t.user_id, u.first_name, u.last_name, u.email, u.user_type, u.user_status
                            FROM auth_tokens t
                            JOIN users u ON u.id = t.user_id
                            WHERE t.token_hash = SHA2(?,256) AND t.expires_at > NOW()
                            LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
      if ($row['user_status'] !== 'active') {
        header("Location: " . BASE_URL . "/login.php?inactive=1");
        exit;
      }
      $_SESSION['user_id'] = (int)$row['user_id'];
      $_SESSION['name'] = $row['first_name'].' '.$row['last_name'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['user_type'] = $row['user_type'];
      $_SESSION['status'] = $row['user_status'];
      $_SESSION['last_activity'] = time();
      return;
    }
  }

  header("Location: " . BASE_URL . "/login.php");
  exit;
}

function require_admin() {
  require_login();
  $u = current_user();
  if (!$u || $u['user_type'] !== 'admin') {
    header("Location: " . BASE_URL . "/dashboard/index.php?forbidden=1");
    exit;
  }
}

function flash_set($type, $msg) { $_SESSION['flash'] = ['type'=>$type,'msg'=>$msg]; }
function flash_get() {
  if (!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; unset($_SESSION['flash']); return $f; }
  return null;
}
?>
