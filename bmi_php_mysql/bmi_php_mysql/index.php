<?php
require_once __DIR__ . '/config/app.php';
$title = "Home • BMI System";
require_once __DIR__ . "/includes/header.php";
?>
<div class="grid">
  <div class="card">
    <h1 class="h1">BMI Management System</h1>
    <p class="muted">
      A full functional project using <b>PHP + MySQL</b> concepts: database CRUD, cookies & sessions, authentication,
      dashboard control panel, and BMI history.
    </p>
    <div class="hr"></div>
    <div class="row">
      <div>
        <div class="pill">✅ Register & Login</div><br><br>
        <div class="pill">✅ Cookies & Sessions</div><br><br>
        <div class="pill">✅ CRUD (Insert/Update/Delete)</div>
      </div>
      <div>
        <div class="pill">✅ Dashboard Control Panel</div><br><br>
        <div class="pill">✅ BMI Calculator + History</div><br><br>
        <div class="pill">✅ Session Expire (5 min)</div>
      </div>
    </div>
    <div class="hr"></div>
    <a class="btn primary" href="/register.php">Get Started (Sign Up)</a>
    <a class="btn" href="/login.php" style="margin-left:8px">Login</a>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px 0">BMI Categories</h3>
    <p class="muted" style="margin-top:0">Common classification:</p>
    <table class="table">
      <thead><tr><th>Category</th><th>BMI</th></tr></thead>
      <tbody>
        <tr><td>Underweight</td><td>&lt; 18.5</td></tr>
        <tr><td>Normal</td><td>18.5 – 24.9</td></tr>
        <tr><td>Overweight</td><td>25 – 29.9</td></tr>
        <tr><td>Obese</td><td>≥ 30</td></tr>
      </tbody>
    </table>
    <div class="hr"></div>
    <p class="muted">Note: Student project demo (not medical advice).</p>
  </div>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
