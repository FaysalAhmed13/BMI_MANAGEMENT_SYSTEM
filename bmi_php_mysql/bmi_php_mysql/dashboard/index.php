<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/helpers.php";

require_login();
$me = current_user();
$title = "Dashboard • Overview";
$active = "home";
$conn = db();
$uid = (int)$me['id'];

$stmt = $conn->prepare("SELECT * FROM bmi_records WHERE user_id=? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $uid);
$stmt->execute();
$latest = $stmt->get_result()->fetch_assoc();
$latest_bmi = $latest ? (float)$latest['bmi'] : 0;
$latest_status = $latest ? $latest['status'] : 'No record';

require_once __DIR__ . "/_header.php";
?>
<h2 style="margin:0">Welcome, <?php echo e($me['name']); ?> 👋</h2>
<p class="muted" style="margin-top:6px">Quick BMI summary and navigation panel.</p>

<?php if (!empty($_GET['forbidden'])): ?>
  <div class="flash err">Forbidden: Admin only page.</div>
<?php endif; ?>

<div class="hr"></div>

<div class="row">
  <div class="card" style="padding:14px">
    <div class="muted" style="font-size:13px">Latest BMI</div>
    <div style="font-size:40px;font-weight:900;margin:6px 0">
      <?php echo $latest ? number_format($latest_bmi,1) : '--'; ?>
    </div>
    <div class="pill <?php echo status_pill_class($latest_status); ?>"><?php echo e($latest_status); ?></div>
  </div>

  <div class="card" style="padding:14px">
    <div class="muted" style="font-size:13px">Actions</div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
      <a class="btn primary" href="<?php echo BASE_URL; ?>/dashboard/bmi.php">Manage BMI Records</a>
      <?php if (($me['user_type'] ?? 'user')==='admin'): ?>
        <a class="btn blue" href="<?php echo BASE_URL; ?>/dashboard/users.php">Manage Users</a>
      <?php endif; ?>
    </div>
    <div class="hr"></div>
    <div class="muted" style="font-size:13px">Go to BMI Records page to insert/update/delete records.</div>
  </div>
</div>

<?php require_once __DIR__ . "/_footer.php"; ?>
