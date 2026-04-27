<?php
declare(strict_types=1);

$u = auth_user();
$del = repo_driver_deliveries((int) $u['id']);
?>
<div class="card">
  <h2>My Deliveries</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Use the <strong>gate pass</strong> for the run. After <em>In transit</em>, upload your <strong>EIR</strong> before marking the delivery <em>Complete</em>.
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
          $bizName = (string) ($b['name'] ?? '—');
          $cargo = (string) ($b['cargo_desc'] ?? '—');
          $vehicleType = (string) ($b['vehicle_type'] ?? '—');
          $dtVal = (string) ($b['booking_datetime'] ?? '');

          $payoutVal = $b['payout'] ?? null;
          $payoutStr = format_php_money($payoutVal !== null ? (float) $payoutVal : null);

          $status = (string) ($b['status'] ?? '');
          $bn = (string) ($b['booking_number'] ?? '');
          $gpLink = BASE_URL . '/handlers/view_booking_doc.php?booking_number=' . urlencode($bn) . '&doc=gatepass';
          $eirLink = BASE_URL . '/handlers/view_booking_doc.php?booking_number=' . urlencode($bn) . '&doc=eir';
  $hasEir = false;
  if (isset($b['id'])) {
    $stmt = db()->prepare('SELECT 1 FROM eir WHERE booking_id = ? LIMIT 1');
    $stmt->execute([(int) $b['id']]);
    $hasEir = (bool) $stmt->fetchColumn();
  }
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

          <div class="driver-job-card__docs">
            <a href="<?= e($gpLink) ?>" target="_blank" rel="noopener noreferrer">View gate pass</a>
            <?php if ($hasEir): ?>
              <span class="driver-job-card__docsSep">·</span>
              <a href="<?= e($eirLink) ?>" target="_blank" rel="noopener noreferrer">View EIR</a>
            <?php endif; ?>
          </div>

          <?php if ($status === 'in_transit' && !$hasEir): ?>
            <form
              class="driver-job-card__eirForm"
              method="post"
              enctype="multipart/form-data"
              action="<?= e(BASE_URL . '/handlers/driver_upload_eir.php') ?>"
              style="margin:0 0 0.75rem 0"
            >
              <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
              <div class="driver-job-card__eirLbl">Upload EIR (required before complete)</div>
              <div class="driver-job-card__eirRow">
                <input type="file" name="eir" accept="image/*" required>
                <button type="submit" class="btn btn--ghost" style="padding:0.35rem 0.55rem;font-size:0.8rem;white-space:nowrap">Upload EIR</button>
              </div>
            </form>
          <?php endif; ?>

          <div class="driver-job-card__cta">
            <?php if ($status === 'accepted'): ?>
              <form method="post" action="<?= e(BASE_URL . '/handlers/driver_update_delivery.php') ?>" style="margin:0">
                <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                <input type="hidden" name="action" value="in_transit">
                <button type="submit" class="driver-job-card__ctaBtn">START TRANSIT</button>
              </form>
            <?php elseif ($status === 'in_transit' && $hasEir): ?>
              <form method="post" action="<?= e(BASE_URL . '/handlers/driver_update_delivery.php') ?>" style="margin:0">
                <input type="hidden" name="booking_number" value="<?= e($b['booking_number'] ?? '') ?>">
                <input type="hidden" name="action" value="completed">
                <button type="submit" class="driver-job-card__ctaBtn">COMPLETE</button>
              </form>
            <?php elseif ($status === 'in_transit'): ?>
              <div class="driver-job-card__hint">Upload your EIR above to enable Complete.</div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
