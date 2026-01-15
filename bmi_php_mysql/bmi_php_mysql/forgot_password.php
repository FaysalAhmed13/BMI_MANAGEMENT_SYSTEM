<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/includes/helpers.php";

$title = "Forgot Password • BMI System";
$flash = flash_get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ue = trim($_POST['username_or_email'] ?? '');
  $newpass = $_POST['new_password'] ?? '';
  if ($ue==='' || $newpass==='') { flash_set('err','Enter username/email and new password.'); header("Location: " . BASE_URL . "/forgot_password.php"); exit; }

  $conn = db();
  $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR username=? LIMIT 1");
  $stmt->bind_param("ss", $ue, $ue);
  $stmt->execute();
  $u = $stmt->get_result()->fetch_assoc();
  if (!$u) { flash_set('err','User not found.'); header("Location: " . BASE_URL . "/forgot_password.php"); exit; }

  $hash = password_hash($newpass, PASSWORD_BCRYPT);
  $uid = (int)$u['id'];
  $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
  $stmt->bind_param("si", $hash, $uid);
  $stmt->execute();

  flash_set('ok','Password reset successfully. Please login.');
  header("Location: " . BASE_URL . "/login.php"); exit;
}

require_once __DIR__ . "/includes/header.php";
?>
<div class="grid">
  <div class="card">
    <h2 style="margin:0">Forgot Password</h2>
    <p class="muted">Demo reset: enter username/email and set a new password.</p>

    <?php if ($flash): ?>
      <div class="flash <?php echo e($flash['type']); ?>"><?php echo e($flash['msg']); ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Username or Email *</label>
      <input class="input" name="username_or_email" required>
      <div style="height:12px"></div>
      <label>New Password *</label>
      <input class="input" name="new_password" type="password" required>
      <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit">Reset Password</button>
        <a class="btn" href="/login.php">Back to Login</a>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px 0">Security Note</h3>
    <p class="muted">Real systems use email token reset. This is a class demo to meet requirements.</p>
  </div>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
