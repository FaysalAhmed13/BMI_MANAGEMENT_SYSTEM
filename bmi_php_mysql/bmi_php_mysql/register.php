<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . "/config/db.php";
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/includes/helpers.php";

$title = "Sign Up • BMI System";
if (!empty($_SESSION['user_id'])) { header("Location: " . BASE_URL . "/dashboard/index.php"); exit; }

$flash = flash_get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last  = trim($_POST['last_name'] ?? '');
  $sex   = trim($_POST['sex'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $user_type = trim($_POST['user_type'] ?? 'user');
  $user_status = trim($_POST['user_status'] ?? 'active');

  $profile_pic = null;
  if (!empty($_FILES['profile_pic']['name'])) {
    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) { flash_set('err','Profile picture must be jpg/png/webp'); header("Location: " . BASE_URL . "/register.php"); exit; }
    $newName = 'u_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = __DIR__ . "/assets/uploads/" . $newName;
    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dest)) { flash_set('err','Failed to upload image'); header("Location: " . BASE_URL . "/register.php"); exit; }
    $profile_pic = $newName;
  }

  if ($first==='' || $last==='' || $sex==='' || $username==='' || $email==='' || $password==='') {
    flash_set('err','Please fill required fields.'); header("Location: " . BASE_URL . "/register.php"); exit;
  }

  $conn = db();
  $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR username=? LIMIT 1");
  $stmt->bind_param("ss", $email, $username);
  $stmt->execute();
  if ($stmt->get_result()->fetch_assoc()) { flash_set('err','Email or username already exists.'); header("Location: " . BASE_URL . "/register.php"); exit; }

  $hash = password_hash($password, PASSWORD_BCRYPT);

  $stmt = $conn->prepare("INSERT INTO users(auto_id, first_name, last_name, sex, username, password_hash, phone, email, profile_pic, user_type, user_status)
                          VALUES(NULL,?,?,?,?,?,?,?,?,?,?)");
  $stmt->bind_param("ssssssssss", $first, $last, $sex, $username, $hash, $phone, $email, $profile_pic, $user_type, $user_status);
  $stmt->execute();

  flash_set('ok','Account created successfully. Please login.');
  header("Location: " . BASE_URL . "/login.php"); exit;
}

require_once __DIR__ . "/includes/header.php";
?>
<div class="grid">
  <div class="card">
    <h2 style="margin:0">User Registration</h2>
    <p class="muted">Create an account to access BMI dashboard.</p>

    <?php if ($flash): ?>
      <div class="flash <?php echo e($flash['type']); ?>"><?php echo e($flash['msg']); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="row">
        <div><label>First Name *</label><input class="input" name="first_name" required></div>
        <div><label>Last Name *</label><input class="input" name="last_name" required></div>
      </div>

      <div class="row">
        <div>
          <label>Sex *</label>
          <select class="input" name="sex" required>
            <option value="">Select</option><option>Male</option><option>Female</option>
          </select>
        </div>
        <div><label>Username *</label><input class="input" name="username" required></div>
      </div>

      <div class="row">
        <div><label>Email *</label><input class="input" name="email" type="email" required></div>
        <div><label>Phone</label><input class="input" name="phone"></div>
      </div>

      <div class="row">
        <div><label>Password *</label><input class="input" name="password" type="password" required></div>
        <div><label>Profile Picture</label><input class="input" name="profile_pic" type="file" accept=".jpg,.jpeg,.png,.webp"></div>
      </div>

      <div class="row">
        <div>
          <label>User Type</label>
          <select class="input" name="user_type">
            <option value="user" selected>User</option>
            <option value="admin">Admin</option>
          </select>
          <div class="muted" style="font-size:12px;margin-top:6px">Demo: you can create admin here, or change in DB.</div>
        </div>
        <div>
          <label>User Status</label>
          <select class="input" name="user_status">
            <option value="active" selected>Active</option>
            <option value="not_active">Not Active</option>
          </select>
        </div>
      </div>

      <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit">Submit</button>
        <a class="btn" href="/login.php">Already have account? Login</a>
        <button class="btn danger" type="reset">Reset</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px 0">Rules</h3>
    <p class="muted">This project meets assignment: MySQL CRUD, sessions & cookies, dashboard control panel, session expires after 5 minutes.</p>
  </div>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
