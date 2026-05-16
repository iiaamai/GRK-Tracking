<?php
declare(strict_types=1);

$u = auth_user();
$mine = repo_customer_bookings((int) $u['id']);
?>
<div class="card">
  <h2>Your shipments</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">All bookings tied to your account.</p>
  <?php if (!$mine): ?>
    <p style="color:var(--muted)">No bookings yet. Use <a href="./dashboard.php?section=booking">Booking</a> to create one.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Posting</th>
            <th>Pickup time</th>
            <th>Amount</th>
            <th>Payment ref.</th>
            <th>Status</th>
            <th>Vehicle</th>
            <th>Route</th>
            <th class="booking-receipt-actions">Receipt</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($mine as $b): ?>
            <?php $cls = 'badge--' . preg_replace('/[^a-z_]/', '', (string) ($b['status'] ?? 'pending')); ?>
            <tr>
              <td><?= e($b['booking_number'] ?? '') ?></td>
              <td><?= e(format_timestamp($b['posting_date'] ?? '', 'M j, Y')) ?></td>
              <td><?= e(format_timestamp($b['booking_datetime'] ?? '')) ?></td>
              <td><?= e(format_php_money(isset($b['payout']) && $b['payout'] !== null ? (float) $b['payout'] : null)) ?></td>
              <td><?php $pref = trim((string) ($b['payment_receipt_reference'] ?? '')); ?><?php if ($pref !== ''): ?><?= e($pref) ?><?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?></td>
              <td><span class="badge <?= e($cls) ?>"><?= e($b['status'] ?? '') ?></span></td>
              <td><?= e($b['vehicle_type'] ?? '') ?></td>
              <td style="white-space:normal;max-width:14rem"><?= e($b['pickup'] ?? '') ?> → <?= e($b['dropoff'] ?? '') ?></td>
              <td class="booking-receipt-actions" style="white-space:nowrap"><a href="./dashboard.php?section=receipt&amp;booking_number=<?= rawurlencode((string) ($b['booking_number'] ?? '')) ?>" class="btn btn--ghost" style="padding:0.35rem 0.55rem;font-size:0.8rem">View / print</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
