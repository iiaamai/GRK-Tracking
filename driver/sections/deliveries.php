<?php
declare(strict_types=1);

$u = auth_user();
$del = repo_driver_deliveries((int) $u['id']);
?>
<div class="card">
  <h2>Active runs</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">Mark <em>In transit</em> then <em>Complete</em> to record payout.</p>
  <?php if (!$del): ?>
    <p style="color:var(--muted)">No active deliveries. Accept jobs from Available Jobs.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Status</th>
            <th>Route</th>
            <th>Cargo</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($del as $b): ?>
            <?php $cls = 'badge--' . preg_replace('/[^a-z_]/', '', (string) ($b['status'] ?? 'pending')); ?>
            <tr>
              <td><?= e($b['booking_number'] ?? '') ?></td>
              <td><span class="badge <?= e($cls) ?>"><?= e($b['status'] ?? '') ?></span></td>
              <td><?= e($b['pickup'] ?? '') ?> → <?= e($b['dropoff'] ?? '') ?></td>
              <td><?= e($b['cargo_desc'] ?? '') ?></td>
              <td style="white-space:nowrap">
                <?php if (($b['status'] ?? '') === 'assigned'): ?>
                  <form method="post" action="<?= e(BASE_URL . '/handlers/driver_update_delivery.php') ?>" style="display:inline;margin:0">
                    <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                    <input type="hidden" name="action" value="in_transit">
                    <button type="submit" class="btn btn--ghost" style="padding:0.35rem 0.55rem;font-size:0.8rem">Start transit</button>
                  </form>
                <?php endif; ?>
                <?php if (($b['status'] ?? '') === 'in_transit'): ?>
                  <form method="post" action="<?= e(BASE_URL . '/handlers/driver_update_delivery.php') ?>" style="display:inline;margin:0">
                    <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                    <input type="hidden" name="action" value="completed">
                    <button type="submit" class="btn btn--primary" style="padding:0.35rem 0.55rem;font-size:0.8rem">Complete</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
