<?php
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function bmi_calc($height_cm, $weight_kg) {
  $h = ((float)$height_cm)/100.0;
  if ($h <= 0) return 0;
  return ((float)$weight_kg)/($h*$h);
}
function bmi_status($bmi) {
  if ($bmi <= 0) return "Unknown";
  if ($bmi < 18.5) return "Underweight";
  if ($bmi < 25) return "Normal";
  if ($bmi < 30) return "Overweight";
  return "Obese";
}
function status_pill_class($status) {
  switch ($status) {
    case 'Normal': return 'ok';
    case 'Overweight': return 'warn';
    case 'Obese': return 'danger';
    case 'Underweight': return 'warn';
    default: return '';
  }
}
?>
