<?php
declare(strict_types=1);

$u = auth_user();
$jobs = repo_driver_jobs_available();
$del = repo_driver_deliveries((int) $u['id']);
$completed = repo_driver_completed_deliveries_count((int) $u['id']);
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
    <div class="stat__val"><?= (int) $completed ?></div>
    <div class="stat__lbl">Total deliveries (completed)</div>
  </div>
</div>

<div class="card">
  <h2>Hello, <?= e((string) $u['name']) ?></h2>
  <p style="margin:0;color:var(--muted);font-size:0.95rem;">
    Vehicle: <strong><?= e((string) ($u['vehicle_type'] ?? '')) ?></strong> · Plate <strong><?= e((string) ($u['plate'] ?? '')) ?></strong>
    · New customer bookings appear in <strong>Available Jobs</strong> as soon as they are submitted.
  </p>
</div>
