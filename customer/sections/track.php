<?php
declare(strict_types=1);

$qNum = trim((string) ($_GET['booking_number'] ?? ''));
$qUser = trim((string) ($_GET['username'] ?? ''));
$result = null;
$rows = [];

if ($qNum !== '') {
    $result = repo_find_booking_by_number($qNum);
}
if ($qUser !== '') {
    $rows = repo_find_bookings_by_username($qUser);
}

$trackUrl = './dashboard.php';
?>
<div class="card">
  <h2>Check status</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Enter a <strong>booking number</strong> for one shipment, or a <strong>username</strong> to list all bookings for that account.
  </p>
  <form method="get" action="<?= e($trackUrl) ?>" class="track-search-form">
    <input type="hidden" name="section" value="track">
    <div class="grid grid--2" style="align-items:end">
      <div class="form-row" style="margin-bottom:0">
        <label for="booking_number">Booking number</label>
        <input id="booking_number" name="booking_number" value="<?= e($qNum) ?>" placeholder="EXP-2026-0001" autocomplete="off">
      </div>
      <div class="form-row" style="margin-bottom:0">
        <label for="username">Username</label>
        <input id="username" name="username" value="<?= e($qUser) ?>" placeholder="acme_corp" autocomplete="off">
      </div>
      <div class="form-row" style="margin-bottom:0">
        <button type="submit" class="btn btn--primary">Search</button>
      </div>
    </div>
  </form>
</div>

<?php if ($qNum !== '' && $result): ?>
  <?php
  $b = $result;
  $cls = 'badge--' . preg_replace('/[^a-z_]/', '', (string) ($b['status'] ?? 'pending'));
  $driverId = (int) ($b['driver_id'] ?? 0);
  $driver = $driverId > 0 ? repo_driver_by_id($driverId) : null;
  ?>
  <div class="card track-shipment-panel">
    <header class="track-shipment-panel__head">
      <h2>Shipment & booking details</h2>
      <p class="track-shipment-panel__ref">
        Tracking reference <strong><?= e($b['booking_number'] ?? '') ?></strong>
        <span class="badge <?= e($cls) ?>"><?= e($b['status'] ?? '') ?></span>
      </p>
    </header>

    <div class="track-shipment-panel__grid">
      <section class="track-shipment-panel__block" aria-labelledby="track-block-booking">
        <h3 id="track-block-booking">Booking & contact</h3>
        <dl class="track-dl">
          <div><dt>Account username</dt><dd><?= e($b['username'] ?? '—') ?></dd></div>
          <div><dt>Contact name</dt><dd><?= e($b['name'] ?? '') ?></dd></div>
          <div><dt>Email</dt><dd><?= e($b['email'] ?? '') ?></dd></div>
          <div><dt>Mobile</dt><dd><?= e($b['mobile'] ?? '') ?></dd></div>
          <div><dt>Booking date & time</dt><dd><?= e(format_timestamp($b['booking_datetime'] ?? '')) ?></dd></div>
          <div><dt>Posted</dt><dd><?= e(format_timestamp($b['posting_date'] ?? '', 'M j, Y')) ?></dd></div>
        </dl>
      </section>

      <section class="track-shipment-panel__block" aria-labelledby="track-block-route">
        <h3 id="track-block-route">Route</h3>
        <dl class="track-dl">
          <div><dt>Pickup</dt><dd><?= e($b['pickup'] ?? '—') ?></dd></div>
          <div><dt>Drop-off</dt><dd><?= e($b['dropoff'] ?? '—') ?></dd></div>
        </dl>
      </section>

      <section class="track-shipment-panel__block track-shipment-panel__block--wide" aria-labelledby="track-block-cargo">
        <h3 id="track-block-cargo">Vehicle & cargo</h3>
        <dl class="track-dl">
          <div><dt>Vehicle type</dt><dd><?= e($b['vehicle_type'] ?? '—') ?></dd></div>
          <div class="track-dl__full"><dt>Cargo / parcel description</dt><dd><?= e($b['cargo_desc'] ?? '') !== '' ? e($b['cargo_desc']) : '—' ?></dd></div>
          <div class="track-dl__full"><dt>Additional requirements</dt><dd><?= e($b['additional_requirements'] ?? '') !== '' ? e($b['additional_requirements']) : '—' ?></dd></div>
        </dl>
      </section>

      <?php
        $status = (string) ($b['status'] ?? '');
        $eirReady = (string) ($b['eir_image'] ?? '') !== '';
        $eirLink = '../handlers/view_booking_doc.php?booking_number=' . urlencode((string) ($b['booking_number'] ?? '')) . '&doc=eir';
      ?>
      <?php if (in_array($status, ['in_transit', 'completed'], true) && $eirReady): ?>
        <section class="track-shipment-panel__block" aria-labelledby="track-block-docs">
          <h3 id="track-block-docs">Documents</h3>
          <p style="margin:0;color:var(--muted);font-size:0.9rem;">
            Equipment Interchange Receipt (EIR):
            <a href="<?= e($eirLink) ?>" target="_blank" rel="noopener noreferrer">View / download</a>
          </p>
        </section>
      <?php endif; ?>

      <section class="track-shipment-panel__block" aria-labelledby="track-block-assignment">
        <h3 id="track-block-assignment">Assignment</h3>
        <?php if ($driver): ?>
          <dl class="track-dl">
            <div><dt>Driver</dt><dd><?= e($driver['name'] ?? '') ?></dd></div>
            <div><dt>Vehicle (driver)</dt><dd><?= e($driver['vehicle_type'] ?? '') ?> · <?= e($driver['plate'] ?? '') ?></dd></div>
            <div><dt>Driver mobile</dt><dd><?= e($driver['mobile'] ?? '—') ?></dd></div>
          </dl>
        <?php else: ?>
          <p class="track-shipment-panel__pending">No driver assigned yet. Your shipment is queued for dispatch.</p>
        <?php endif; ?>
      </section>
    </div>
  </div>
<?php elseif ($qNum !== ''): ?>
  <div class="flash flash--err">No booking found for that number.</div>
<?php endif; ?>

<?php if ($qUser !== '' && $rows): ?>
  <div class="card">
    <h2>Bookings for username “<?= e($qUser) ?>”</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Date/Time</th>
            <th>Status</th>
            <th>Route</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $b): ?>
            <?php $cls = 'badge--' . preg_replace('/[^a-z_]/', '', (string) ($b['status'] ?? 'pending')); ?>
            <tr>
              <td><?= e($b['booking_number'] ?? '') ?></td>
              <td><?= e(format_timestamp($b['booking_datetime'] ?? '')) ?></td>
              <td><span class="badge <?= e($cls) ?>"><?= e($b['status'] ?? '') ?></span></td>
              <td><?= e($b['pickup'] ?? '') ?> → <?= e($b['dropoff'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php elseif ($qUser !== ''): ?>
  <div class="flash flash--err">No bookings for that username.</div>
<?php endif; ?>
