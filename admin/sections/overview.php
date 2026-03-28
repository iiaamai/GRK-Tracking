<?php
declare(strict_types=1);

$b = repo_bookings();
$c = repo_customers();
$d = repo_drivers();
$f = repo_fleet();
?>
<div class="grid grid--stats">
  <div class="stat">
    <div class="stat__val"><?= count($b) ?></div>
    <div class="stat__lbl">Bookings</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= count($c) ?></div>
    <div class="stat__lbl">Customers</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= count($d) ?></div>
    <div class="stat__lbl">Drivers</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= count($f) ?></div>
    <div class="stat__lbl">Fleet units</div>
  </div>
</div>

<div class="card">
  <h2><?= e(repo_settings()['company_name'] ?? 'Express Urban Logistics') ?></h2>
  <p style="margin:0;color:var(--muted);font-size:0.95rem;">
    Truck-first logistics for pallets and container-scale freight. Figures below reflect live MySQL data.
  </p>
</div>
