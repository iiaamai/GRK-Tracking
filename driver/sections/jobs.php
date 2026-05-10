<?php
declare(strict_types=1);

$u = auth_user();
$jobs = repo_driver_jobs_available((string) ($u['vehicle_type'] ?? ''));
$active = repo_driver_deliveries((int) ($u['id'] ?? 0));
$hasActive = count($active) > 0;
$vehStatus = repo_vehicle_status_by_plate_number((string) ($u['plate'] ?? ''));
$vehicleBlocking = $vehStatus !== null && $vehStatus !== 'available';
$blocked = $hasActive || $vehicleBlocking;
$blockReason = $hasActive
  ? 'You have an unfinished delivery. Finish it before accepting a new job.'
  : ($vehicleBlocking ? ('Your vehicle is currently ' . $vehStatus . '. You cannot accept jobs right now.') : '');
?>
<div class="card">
  <h2>Available Jobs</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Only jobs that match your profile vehicle type are listed. Accept a job to add it to your deliveries.
  </p>
  <?php if ($blocked): ?>
    <div class="flash flash--err" style="margin:0 0 1rem">
      <?= e($blockReason) ?>
    </div>
  <?php endif; ?>
  <?php if (!$jobs): ?>
    <p style="color:var(--muted)">No open jobs. When customers book, listings appear here.</p>
  <?php else: ?>
    <div class="driver-cards driver-cards--jobs">
      <?php foreach ($jobs as $b): ?>
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

          <div class="driver-job-card__cta">
            <form
              method="post"
              action="../handlers/driver_accept_job.php"
              style="margin:0"
              onsubmit='return confirm(<?= json_encode('Accept job: ' . $route . '? It will move to My Deliveries.', JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);'
            >
              <?= csrf_field() ?>
              <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
              <button
                type="submit"
                class="driver-job-card__ctaBtn"
                <?= $blocked ? 'disabled aria-disabled="true"' : '' ?>
                title="<?= $blocked ? e($blockReason) : 'Accept job' ?>"
              >ACCEPT JOB</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
