<?php
declare(strict_types=1);

$u = auth_user();
$total = repo_driver_earnings((int) $u['id']);
$all = repo_bookings();
$done = array_filter($all, static function ($b) use ($u) {
    return (int) ($b['driver_id'] ?? 0) === (int) $u['id'] && ($b['status'] ?? '') === 'completed';
});
?>
<div class="card">
  <h2>Summary</h2>
  <p style="margin:0;font-size:1.25rem;font-weight:700;"><?= e(format_php_money((float) $total)) ?></p>
  <p style="margin:0.35rem 0 0;color:var(--muted);font-size:0.9rem;">Total from completed deliveries (demo payout field).</p>
</div>

<div class="card">
  <h2>Completed deliveries</h2>
  <?php if (!$done): ?>
    <p style="color:var(--muted)">Complete a delivery from My Deliveries to populate earnings.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table id="driver_earnings_table" class="js-paginated-table" data-page-size="5">
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Route</th>
            <th>Payout (₱)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($done as $b): ?>
            <tr>
              <td><?= e($b['booking_number'] ?? '') ?></td>
              <td><?= e($b['pickup'] ?? '') ?> → <?= e($b['dropoff'] ?? '') ?></td>
              <td><?= e(format_php_money(isset($b['payout']) && $b['payout'] !== null ? (float) $b['payout'] : null)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
