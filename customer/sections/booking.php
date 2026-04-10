<?php
declare(strict_types=1);

$u = auth_user();
$dt = (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d\TH:i');
?>
<div class="card">
  <h2>Request a truck</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Your request is saved as <span class="badge badge--pending">pending</span> until an administrator reviews it and uploads a <strong>gate pass</strong>. Only then can a driver accept the job.
  </p>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/booking_submit.php') ?>" novalidate>
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
          <option>6-wheeler (Isuzu / Fuso)</option>
          <option>4-wheeler truck</option>
          <option>L300 van</option>
          <option>2-wheeler (express)</option>
          <option>Reefer / specialized</option>
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
    <button type="submit" class="btn btn--primary">Submit booking</button>
  </form>
</div>
