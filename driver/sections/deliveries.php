<?php
declare(strict_types=1);

$u = auth_user();
$del = repo_driver_deliveries((int) $u['id']);
?>
<div class="card">
  <h2>My Deliveries</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">Mark <em>In transit</em> then <em>Complete</em> to record payout.</p>
  <?php if (!$del): ?>
    <p style="color:var(--muted)">No active deliveries. Accept jobs from Available Jobs.</p>
  <?php else: ?>
    <div class="driver-cards driver-cards--deliveries">
      <?php foreach ($del as $b): ?>
        <?php
          $pickup = (string) ($b['pickup'] ?? '');
          $dropoff = (string) ($b['dropoff'] ?? '');
          $route = $pickup . ' → ' . $dropoff;
          $bizName = (string) ($b['name'] ?? '—');
          $cargo = (string) ($b['cargo_desc'] ?? '—');
          $vehicleType = (string) ($b['vehicle_type'] ?? '—');
          $dtVal = (string) ($b['booking_datetime'] ?? '');

          $payout = $b['payout'] ?? null;
          $expectedPayout = $payout === null ? 5200.0 : (float) $payout;
          $payoutStr = '$' . number_format($expectedPayout, 0);

          $status = (string) ($b['status'] ?? '');
          $ctaText = $status === 'assigned' ? 'START TRANSIT' : ($status === 'in_transit' ? 'COMPLETE' : 'UPDATE');
          $ctaAction = $status === 'assigned' ? 'in_transit' : ($status === 'in_transit' ? 'completed' : 'in_transit');
        ?>
        <div class="driver-job-card">
          <div class="driver-job-card__head">
            <div class="driver-job-card__left">
              <div class="driver-job-card__route"><?= e($route) ?></div>
              <div class="driver-job-card__biz"><?= e($bizName) ?></div>
            </div>
            <div class="driver-job-card__payout"><?= e($payoutStr) ?></div>
          </div>

          <div class="driver-job-card__grid">
            <div class="driver-job-card__field">
              <div class="driver-job-card__label">CARGO</div>
              <div class="driver-job-card__value"><?= e($cargo) ?></div>
            </div>
            <div class="driver-job-card__field">
              <div class="driver-job-card__label">REQUIRED VEHICLE</div>
              <div class="driver-job-card__value"><?= e($vehicleType) ?></div>
            </div>
            <div class="driver-job-card__field">
              <div class="driver-job-card__label">PREF DATE</div>
              <div class="driver-job-card__value"><?= e(format_timestamp($dtVal, 'Y-m-d')) ?></div>
            </div>
            <div class="driver-job-card__field">
              <div class="driver-job-card__label">PREF TIME</div>
              <div class="driver-job-card__value"><?= e(format_timestamp($dtVal, 'h:i A')) ?></div>
            </div>
          </div>

          <div class="driver-job-card__cta">
            <form method="post" action="<?= e(BASE_URL . '/handlers/driver_update_delivery.php') ?>" style="margin:0">
              <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
              <input type="hidden" name="action" value="<?= e($ctaAction) ?>">
              <button type="submit" class="driver-job-card__ctaBtn"><?= e($ctaText) ?></button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
