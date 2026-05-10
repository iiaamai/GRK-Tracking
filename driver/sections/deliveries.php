<?php
declare(strict_types=1);

$u = auth_user();
$del = repo_driver_deliveries((int) $u['id']);
?>
<div class="card">
  <h2>My Deliveries</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Start the run when you are on your way, then mark the delivery <em>Complete</em> when finished.
  </p>
  <?php if (!$del): ?>
    <p style="color:var(--muted)">No active deliveries. Accept jobs from Available Jobs.</p>
  <?php else: ?>
    <div class="driver-cards driver-cards--deliveries">
      <?php foreach ($del as $b): ?>
        <?php
          $pickup = (string) ($b['pickup'] ?? '');
          $dropoff = (string) ($b['dropoff'] ?? '');
          $route = $pickup . ' → ' . $dropoff;
          $bizName = (string) ($b['customer_name'] ?? $b['name'] ?? '—');
          $cargo = (string) ($b['cargo_desc'] ?? '—');
          $vehicleType = (string) ($b['vehicle_type'] ?? '—');
          $dtVal = (string) ($b['booking_datetime'] ?? '');

          $payoutVal = $b['payout'] ?? null;
          $payoutStr = format_php_money($payoutVal !== null ? (float) $payoutVal : null);

          $status = (string) ($b['status'] ?? '');
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
            <?php if ($status === 'accepted'): ?>
              <form
                method="post"
                action="../handlers/driver_update_delivery.php"
                style="margin:0"
                onsubmit="return confirm('Start transit now? Only confirm when you are on the way.');"
              >
                <?= csrf_field() ?>
                <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                <input type="hidden" name="action" value="in_transit">
                <button type="submit" class="driver-job-card__ctaBtn">START TRANSIT</button>
              </form>
            <?php elseif ($status === 'in_transit'): ?>
              <form
                method="post"
                action="../handlers/driver_update_delivery.php"
                style="margin:0"
                onsubmit="return confirm('Mark this delivery as complete?');"
              >
                <?= csrf_field() ?>
                <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                <input type="hidden" name="action" value="completed">
                <button type="submit" class="driver-job-card__ctaBtn">COMPLETE</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
