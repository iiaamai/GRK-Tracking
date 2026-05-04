<?php
declare(strict_types=1);

$u = auth_user();
$dt = (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d\TH:i');
$vehiclePayouts = booking_vehicle_payouts_map();
$avail = [];
foreach (array_keys($vehiclePayouts) as $label) {
    $avail[$label] = repo_available_vehicles_count_for_booking_vehicle_type((string) $label);
}
?>
<div class="card">
  <h2>Request a truck</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Your request is saved as <span class="badge badge--pending">pending</span> until an administrator reviews it and uploads a <strong>gate pass</strong>. Only then can a driver accept the job.
  </p>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Availability is based on Fleet Management units marked <strong>available</strong>.
  </p>
  <form class="js-validate" method="post" action="../handlers/booking_submit.php" novalidate>
    <?= csrf_field() ?>
    <div class="grid grid--2">
      <div class="form-row">
        <label for="name">Full name / company *</label>
        <input id="name" name="name" required maxlength="120" value="<?= e((string) $u['name']) ?>">
      </div>
      <div class="form-row">
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" required value="<?= e((string) $u['email']) ?>">
      </div>
      <div class="form-row">
        <label for="mobile">Mobile *</label>
        <input id="mobile" name="mobile" required value="<?= e((string) $u['mobile']) ?>">
      </div>
      <div class="form-row">
        <label for="vehicle_type">Vehicle type *</label>
        <select id="vehicle_type" name="vehicle_type" required>
          <option value="">Select…</option>
          <?php foreach ($vehiclePayouts as $label => $amount): ?>
            <?php $n = (int) ($avail[$label] ?? 0); ?>
            <option value="<?= e($label) ?>" data-payout="<?= e((string) $amount) ?>"><?= e($label) ?> (<?= $n ?> available)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <label for="booking_datetime">Preferred pickup date/time *</label>
        <input id="booking_datetime" name="booking_datetime" type="datetime-local" required value="<?= e($dt) ?>">
      </div>
    </div>
    <div class="form-row">
      <label for="pickup">Pickup location *</label>
      <input id="pickup" name="pickup" required maxlength="200" placeholder="Warehouse / city">
    </div>
    <div class="form-row">
      <label for="dropoff">Drop-off location *</label>
      <input id="dropoff" name="dropoff" required maxlength="200">
    </div>
    <div class="form-row">
      <label for="cargo_desc">Cargo description</label>
      <textarea id="cargo_desc" name="cargo_desc" maxlength="2000" placeholder="Pallets, containers, dimensions…"></textarea>
    </div>
    <div class="form-row">
      <label for="additional_requirements">Additional requirements</label>
      <textarea id="additional_requirements" name="additional_requirements" maxlength="2000" placeholder="Liftgate, tail lift, documents…"></textarea>
    </div>
    <div class="booking-payout-summary" id="booking-payout-summary" aria-live="polite">
      <div class="booking-payout-summary__label">Estimated payout (Philippine Peso)</div>
      <div class="booking-payout-summary__value" id="booking-payout-value">Select a vehicle type to see the amount.</div>
    </div>
    <button type="submit" class="btn btn--primary">Submit booking</button>
  </form>
</div>
<script>
(function () {
  var sel = document.getElementById('vehicle_type');
  var out = document.getElementById('booking-payout-value');
  if (!sel || !out) return;
  function formatPhp(n) {
    try {
      return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(n);
    } catch (e) {
      return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
  }
  function update() {
    var opt = sel.options[sel.selectedIndex];
    var raw = opt && opt.getAttribute('data-payout');
    if (!raw || raw === '') {
      out.textContent = 'Select a vehicle type to see the amount.';
      out.classList.remove('is-amount');
      return;
    }
    out.textContent = formatPhp(parseFloat(raw));
    out.classList.add('is-amount');
  }
  sel.addEventListener('change', update);
  update();
})();
</script>
