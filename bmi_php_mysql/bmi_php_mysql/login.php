<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/includes/helpers.php";

$title = "Login • BMI System";
if (!empty($_SESSION['user_id'])) { header("Location: " . BASE_URL . "/dashboard/index.php"); exit; }

$flash = flash_get();
if (!empty($_GET['expired'])) $flash = ['type'=>'warn','msg'=>'Session expired after 5 minutes. Please login again.'];
if (!empty($_GET['inactive'])) $flash = ['type'=>'err','msg'=>'Your account is not active. Contact admin.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ue = trim($_POST['username_or_email'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = !empty($_POST['remember']);

  if ($ue==='' || $password==='') { flash_set('err','Please enter username/email and password.'); header("Location: " . BASE_URL . "/login.php"); exit; }

  $conn = db();
  $stmt = $conn->prepare("SELECT id, first_name, last_name, email, username, password_hash, user_type, user_status
                          FROM users WHERE email=? OR username=? LIMIT 1");
  $stmt->bind_param("ss", $ue, $ue);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if (!$user || !password_verify($password, $user['password_hash'])) { flash_set('err','Invalid credentials.'); header("Location: " . BASE_URL . "/login.php"); exit; }
  if ($user['user_status'] !== 'active') { header("Location: " . BASE_URL . "/login.php?inactive=1"); exit; }

  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['name'] = $user['first_name'].' '.$user['last_name'];
  $_SESSION['email'] = $user['email'];
  $_SESSION['user_type'] = $user['user_type'];
  $_SESSION['status'] = $user['user_status'];
  $_SESSION['last_activity'] = time();

  if ($remember) {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 60*60*24*7);
    $uid = (int)$user['id'];
    $stmt = $conn->prepare("INSERT INTO auth_tokens(user_id, token_hash, expires_at) VALUES(?, SHA2(?,256), ?)");
    $stmt->bind_param("iss", $uid, $token, $expires);
    $stmt->execute();
    setcookie('remember_token', $token, time()+60*60*24*7, '/', '', false, true);
  }

  header("Location: " . BASE_URL . "/dashboard/index.php"); exit;
}

require_once __DIR__ . "/includes/header.php";
?>
<div class="grid">
  <div class="card">
    <h2 style="margin:0">Login</h2>
    <p class="muted">Use username/email and password to access dashboard.</p>

    <?php if ($flash): ?>
      <div class="flash <?php echo e($flash['type']); ?>"><?php echo e($flash['msg']); ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Username or Email *</label>
      <input class="input" name="username_or_email" required>
      <div style="height:12px"></div>
      <label>Password *</label>
      <input class="input" name="password" type="password" required>
      <div style="height:12px"></div>
      <label style="display:flex;align-items:center;gap:10px">
        <input type="checkbox" name="remember"> Remember me (cookie)
      </label>

      <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit">Login</button>
        <a class="btn" href="/register.php">Sign Up</a>
        <a class="btn" href="/forgot_password.php">Forgot Password</a>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px 0">Notes</h3>
    <p class="muted">Sessions track user login. Remember-me uses a cookie + DB token. Session expires after 5 minutes.</p>
  </div>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
