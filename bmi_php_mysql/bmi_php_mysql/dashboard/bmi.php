<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/helpers.php";

require_login();
$me = current_user();
$title = "Dashboard • BMI Records";
$active = "bmi";
$conn = db();
$uid = (int)$me['id'];
$flash = flash_get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $height = (float)($_POST['height_cm'] ?? 0);
  $weight = (float)($_POST['weight_kg'] ?? 0);
  $id = (int)($_POST['id'] ?? 0);

  if ($height <= 0 || $weight <= 0) {
    flash_set('err','Height and Weight are required.');
    header("Location: " . BASE_URL . "/dashboard/bmi.php");
    exit;
  }

  $bmi = bmi_calc($height, $weight);
  $status = bmi_status($bmi);

  if ($id > 0) {
    $stmt = $conn->prepare("UPDATE bmi_records SET height_cm=?, weight_kg=?, bmi=?, status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("dddssi", $height, $weight, $bmi, $status, $id, $uid);
    $stmt->execute();
    flash_set('ok','Record updated.');
  } else {
    $stmt = $conn->prepare("INSERT INTO bmi_records(user_id, height_cm, weight_kg, bmi, status) VALUES(?,?,?,?,?)");
    $stmt->bind_param("iddds", $uid, $height, $weight, $bmi, $status);
    $stmt->execute();
    flash_set('ok','Record added.');
  }

  header("Location: " . BASE_URL . "/dashboard/bmi.php");
  exit;
}

if (!empty($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM bmi_records WHERE id=? AND user_id=?");
  $stmt->bind_param("ii", $id, $uid);
  $stmt->execute();
  flash_set('ok','Record deleted.');
  header("Location: " . BASE_URL . "/dashboard/bmi.php");
  exit;
}

$edit = null;
if (!empty($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM bmi_records WHERE id=? AND user_id=? LIMIT 1");
  $stmt->bind_param("ii", $id, $uid);
  $stmt->execute();
  $edit = $stmt->get_result()->fetch_assoc();
}

$stmt = $conn->prepare("SELECT * FROM bmi_records WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . "/_header.php";
?>

<h2 style="margin:0">BMI Records (CRUD)</h2>
<p class="muted" style="margin-top:6px">Insert, update, delete your BMI history.</p>

<?php if ($flash): ?>
  <div class="flash <?php echo e($flash['type']); ?>"><?php echo e($flash['msg']); ?></div>
<?php endif; ?>

<div class="hr"></div>

<div class="row">
  <div class="card" style="padding:14px">
    <h3 style="margin:0 0 8px 0"><?php echo $edit ? "Edit Record" : "Add New Record"; ?></h3>
    <form method="post">
      <input type="hidden" name="id" value="<?php echo $edit ? (int)$edit['id'] : 0; ?>">

      <label>Height (cm) *</label>
      <input class="input" type="number" step="0.01" name="height_cm" required value="<?php echo $edit ? e($edit['height_cm']) : ''; ?>">

      <div style="height:12px"></div>

      <label>Weight (kg) *</label>
      <input class="input" type="number" step="0.01" name="weight_kg" required value="<?php echo $edit ? e($edit['weight_kg']) : ''; ?>">

      <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn primary" type="submit"><?php echo $edit ? "Update" : "Add"; ?></button>

        <!-- ✅ FIXED: Clear link using BASE_URL -->
        <a class="btn" href="<?php echo BASE_URL; ?>/dashboard/bmi.php">Clear</a>
      </div>
    </form>
  </div>

  <div class="card" style="padding:14px">
    <h3 style="margin:0 0 8px 0">Health Tips</h3>
    <?php
      $latest = $list ? $list[0] : null;
      $st = $latest ? $latest['status'] : 'Unknown';
      $tips = [];
      if ($st==='Underweight') $tips = ["Eat more nutrient-dense foods","Add strength training","Increase meals/snacks"];
      elseif ($st==='Normal') $tips = ["Keep balanced diet","Exercise regularly","Maintain good sleep"];
      elseif ($st==='Overweight') $tips = ["Reduce sugar/fried foods","Increase steps","Control portions"];
      elseif ($st==='Obese') $tips = ["Start with walking daily","Choose whole foods","Seek professional advice if needed"];
      else $tips = ["Add your first BMI record to get tips."];
    ?>
    <div class="pill <?php echo status_pill_class($st); ?>">Latest Status: <?php echo e($st); ?></div>
    <div class="hr"></div>
    <ul class="muted" style="margin:0;padding-left:18px">
      <?php foreach ($tips as $t): ?><li><?php echo e($t); ?></li><?php endforeach; ?>
    </ul>
  </div>
</div>

<div class="hr"></div>

<h3 style="margin:0 0 10px 0">Your History</h3>
<table class="table">
  <thead>
    <tr><th>Date</th><th>Height (cm)</th><th>Weight (kg)</th><th>BMI</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php if (!$list): ?><tr><td colspan="6" class="muted">No records yet.</td></tr><?php endif; ?>
    <?php foreach ($list as $r): ?>
      <tr>
        <td><?php echo e($r['created_at']); ?></td>
        <td><?php echo e($r['height_cm']); ?></td>
        <td><?php echo e($r['weight_kg']); ?></td>
        <td><?php echo number_format((float)$r['bmi'], 1); ?></td>
        <td><span class="pill <?php echo status_pill_class($r['status']); ?>"><?php echo e($r['status']); ?></span></td>

        <td style="display:flex;gap:8px;flex-wrap:wrap">
          <!-- ✅ FIXED: Edit/Delete links using BASE_URL -->
          <a class="btn small" href="<?php echo BASE_URL; ?>/dashboard/bmi.php?edit=<?php echo (int)$r['id']; ?>">Edit</a>
          <a class="btn small danger" href="<?php echo BASE_URL; ?>/dashboard/bmi.php?delete=<?php echo (int)$r['id']; ?>"
             onclick="return confirm('Delete record?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . "/_footer.php"; ?>
