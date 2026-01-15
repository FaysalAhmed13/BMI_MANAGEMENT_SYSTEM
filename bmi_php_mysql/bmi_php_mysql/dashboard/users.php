<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/helpers.php";

require_admin();
$me = current_user();
$title = "Dashboard • Users";
$active = "users";
$conn = db();
$flash = flash_get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $first = trim($_POST['first_name'] ?? '');
  $last  = trim($_POST['last_name'] ?? '');
  $sex   = trim($_POST['sex'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $user_type = trim($_POST['user_type'] ?? 'user');
  $user_status = trim($_POST['user_status'] ?? 'active');
  $password = $_POST['password'] ?? '';

  if ($first==='' || $last==='' || $sex==='' || $username==='' || $email==='') {
    flash_set('err','Required fields missing.'); header("Location: " . BASE_URL . "/dashboard/users.php"); exit;
  }

  if ($id > 0) {
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, sex=?, username=?, email=?, phone=?, user_type=?, user_status=?, password_hash=? WHERE id=?");
      $stmt->bind_param("sssssssssi", $first, $last, $sex, $username, $email, $phone, $user_type, $user_status, $hash, $id);
    } else {
      $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, sex=?, username=?, email=?, phone=?, user_type=?, user_status=? WHERE id=?");
      $stmt->bind_param("ssssssssi", $first, $last, $sex, $username, $email, $phone, $user_type, $user_status, $id);
    }
    $stmt->execute();
    flash_set('ok','User updated.');
  } else {
    if ($password==='') { flash_set('err','Password is required for new user.'); header("Location: " . BASE_URL . "/dashboard/users.php"); exit; }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $profile_pic = null;
    $stmt = $conn->prepare("INSERT INTO users(auto_id, first_name, last_name, sex, username, password_hash, phone, email, profile_pic, user_type, user_status)
                            VALUES(NULL,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssss", $first, $last, $sex, $username, $hash, $phone, $email, $profile_pic, $user_type, $user_status);
    $stmt->execute();
    flash_set('ok','User created.');
  }
  header("Location: " . BASE_URL . "/dashboard/users.php"); exit;
}

if (!empty($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  if ($id === (int)$me['id']) { flash_set('err','You cannot delete yourself.'); header("Location: " . BASE_URL . "/dashboard/users.php"); exit; }
  $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  flash_set('ok','User deleted.');
  header("Location: " . BASE_URL . "/dashboard/users.php"); exit;
}

$edit = null;
if (!empty($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $edit = $stmt->get_result()->fetch_assoc();
}

$res = $conn->query("SELECT id, first_name, last_name, sex, username, email, phone, user_type, user_status, created_at FROM users ORDER BY created_at DESC");
$list = $res->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . "/_header.php";
?>
<h2 style="margin:0">Users (Admin CRUD)</h2>
<p class="muted" style="margin-top:6px">Insert, update, delete users. Manage type and status.</p>

<?php if ($flash): ?>
  <div class="flash <?php echo e($flash['type']); ?>"><?php echo e($flash['msg']); ?></div>
<?php endif; ?>

<div class="hr"></div>

<div class="row">
  <div class="card" style="padding:14px">
    <h3 style="margin:0 0 8px 0"><?php echo $edit ? "Edit User" : "Add New User"; ?></h3>
    <form method="post">
      <input type="hidden" name="id" value="<?php echo $edit ? (int)$edit['id'] : 0; ?>">

      <div class="row">
        <div><label>First Name *</label><input class="input" name="first_name" required value="<?php echo $edit ? e($edit['first_name']) : ''; ?>"></div>
        <div><label>Last Name *</label><input class="input" name="last_name" required value="<?php echo $edit ? e($edit['last_name']) : ''; ?>"></div>
      </div>

      <div class="row">
        <div>
          <label>Sex *</label>
          <?php $sx = $edit['sex'] ?? ''; ?>
          <select class="input" name="sex" required>
            <option value="">Select</option>
            <option <?php echo $sx==='Male'?'selected':''; ?>>Male</option>
            <option <?php echo $sx==='Female'?'selected':''; ?>>Female</option>
          </select>
        </div>
        <div><label>Username *</label><input class="input" name="username" required value="<?php echo $edit ? e($edit['username']) : ''; ?>"></div>
      </div>

      <div class="row">
        <div><label>Email *</label><input class="input" type="email" name="email" required value="<?php echo $edit ? e($edit['email']) : ''; ?>"></div>
        <div><label>Phone</label><input class="input" name="phone" value="<?php echo $edit ? e($edit['phone']) : ''; ?>"></div>
      </div>

      <div class="row">
        <div>
          <label>User Type</label>
          <?php $ut = $edit['user_type'] ?? 'user'; ?>
          <select class="input" name="user_type">
            <option value="user" <?php echo $ut==='user'?'selected':''; ?>>User</option>
            <option value="admin" <?php echo $ut==='admin'?'selected':''; ?>>Admin</option>
          </select>
        </div>
        <div>
          <label>User Status</label>
          <?php $us = $edit['user_status'] ?? 'active'; ?>
          <select class="input" name="user_status">
            <option value="active" <?php echo $us==='active'?'selected':''; ?>>Active</option>
            <option value="not_active" <?php echo $us==='not_active'?'selected':''; ?>>Not Active</option>
          </select>
        </div>
      </div>

      <label><?php echo $edit ? "New Password (leave empty to keep)" : "Password *"; ?></label>
      <input class="input" name="password" type="password" <?php echo $edit ? "" : "required"; ?>>

      <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit"><?php echo $edit ? "Update" : "Create"; ?></button>
        <a class="btn" href="/dashboard/users.php">Clear</a>
      </div>
    </form>
  </div>

  <div class="card" style="padding:14px">
    <h3 style="margin:0 0 8px 0">Users List</h3>
    <table class="table">
      <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($list as $r): ?>
          <tr>
            <td><?php echo e($r['first_name'].' '.$r['last_name']); ?></td>
            <td><?php echo e($r['username']); ?></td>
            <td><?php echo e($r['email']); ?></td>
            <td><span class="pill"><?php echo e($r['user_type']); ?></span></td>
            <td><span class="pill <?php echo ($r['user_status']==='active'?'ok':'danger'); ?>"><?php echo e($r['user_status']); ?></span></td>
            <td style="display:flex;gap:8px;flex-wrap:wrap">
              <a class="btn small" href="/dashboard/users.php?edit=<?php echo (int)$r['id']; ?>">Edit</a>
              <a class="btn small danger" href="/dashboard/users.php?delete=<?php echo (int)$r['id']; ?>" onclick="return confirm('Delete user?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . "/_footer.php"; ?>
