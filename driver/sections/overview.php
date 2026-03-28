<?php
declare(strict_types=1);

$u = auth_user();
$jobs = repo_driver_jobs_available();
$del = repo_driver_deliveries((int) $u['id']);
?>
<div class="grid grid--stats">
  <div class="stat">
    <div class="stat__val"><?= count($jobs) ?></div>
    <div class="stat__lbl">Open jobs</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= count($del) ?></div>
    <div class="stat__lbl">Your active deliveries</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= number_format(repo_driver_earnings((int) $u['id']), 0) ?> ₱</div>
    <div class="stat__lbl">Completed earnings (demo)</div>
  </div>
</div>

<div class="card">
  <h2>Hello, <?= e((string) $u['name']) ?></h2>
  <p style="margin:0;color:var(--muted);font-size:0.95rem;">
    Vehicle: <strong><?= e((string) ($u['vehicle_type'] ?? '')) ?></strong> · Plate <strong><?= e((string) ($u['plate'] ?? '')) ?></strong>
    · New customer bookings appear automatically under Available Jobs.
  </p>
</div>
