<?php
declare(strict_types=1);

$b = repo_bookings();
$c = repo_customers();
$d = repo_drivers();
$f = repo_fleet();

$typeCounts = [];
$statusCounts = ['available' => 0, 'in_use' => 0, 'maintenance' => 0];
foreach ($f as $v) {
    $t = trim((string) ($v['type'] ?? ''));
    if ($t === '') {
        $t = 'Other';
    }
    $typeCounts[$t] = (int) ($typeCounts[$t] ?? 0) + 1;

    $st = (string) ($v['status'] ?? '');
    if (array_key_exists($st, $statusCounts)) {
        $statusCounts[$st] = (int) $statusCounts[$st] + 1;
    }
}
ksort($typeCounts);
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

<div class="grid grid--2">
  <div class="card">
    <h2>Fleet by type</h2>
    <?php if (!$typeCounts): ?>
      <p style="color:var(--muted)">No vehicles found.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table id="overview_fleet_types_table" class="js-paginated-table" data-page-size="5">
          <thead>
            <tr>
              <th>Type</th>
              <th style="width:110px">Units</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($typeCounts as $t => $n): ?>
              <tr>
                <td><?= e($t) ?></td>
                <td><strong><?= (int) $n ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>Fleet status (overall)</h2>
    <div class="grid grid--stats" style="margin-top:0">
      <div class="stat">
        <div class="stat__val"><?= (int) ($statusCounts['available'] ?? 0) ?></div>
        <div class="stat__lbl">Available</div>
      </div>
      <div class="stat">
        <div class="stat__val"><?= (int) ($statusCounts['in_use'] ?? 0) ?></div>
        <div class="stat__lbl">In use</div>
      </div>
      <div class="stat">
        <div class="stat__val"><?= (int) ($statusCounts['maintenance'] ?? 0) ?></div>
        <div class="stat__lbl">Maintenance</div>
      </div>
    </div>
    <p style="margin:0.75rem 0 0;color:var(--muted);font-size:0.9rem;">
      Status counts are totals across all vehicle types.
    </p>
  </div>
</div>
