<?php
declare(strict_types=1);

$jobs = repo_driver_jobs_available();
?>
<div class="card">
  <h2>Available Jobs</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Jobs appear here only after an administrator has reviewed the booking and uploaded a <strong>gate pass</strong>.
  </p>
  <?php if (!$jobs): ?>
    <p style="color:var(--muted)">No open jobs. When customers book, listings appear here.</p>
  <?php else: ?>
    <div class="driver-cards driver-cards--jobs">
      <?php foreach ($jobs as $b): ?>
        <?php
          $pickup = (string) ($b['pickup'] ?? '');
          $dropoff = (string) ($b['dropoff'] ?? '');
          $route = $pickup . ' → ' . $dropoff;
          $bizName = (string) ($b['name'] ?? '—');
          $cargo = (string) ($b['cargo_desc'] ?? '—');
          $vehicleType = (string) ($b['vehicle_type'] ?? '—');
          $dtVal = (string) ($b['booking_datetime'] ?? '');
          $payoutVal = $b['payout'] ?? null;
          $payoutStr = format_php_money($payoutVal !== null ? (float) $payoutVal : null);
          $gpLink = BASE_URL . '/handlers/view_booking_doc.php?booking_number=' . urlencode((string) ($b['booking_number'] ?? '')) . '&doc=gatepass';
        ?>
        <div class="driver-job-card">
          <div class="driver-job-card__head">
            <div>
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

          <div class="driver-job-card__docs">
            <a href="<?= e($gpLink) ?>" target="_blank" rel="noopener noreferrer">View gate pass</a>
          </div>

          <div class="driver-job-card__cta">
            <form method="post" action="<?= e(BASE_URL . '/handlers/driver_accept_job.php') ?>" style="margin:0">
              <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
              <button type="submit" class="driver-job-card__ctaBtn">ACCEPT JOB</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
