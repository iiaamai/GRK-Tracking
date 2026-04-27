<?php
declare(strict_types=1);

$u = auth_user();
?>
<div class="card">
  <h2>Driver & vehicle</h2>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/profile_driver.php') ?>" novalidate>
    <div class="grid grid--2">
      <div class="form-row">
        <label for="name">Full name *</label>
        <input id="name" name="name" required value="<?= e((string) $u['name']) ?>">
      </div>
      <div class="form-row">
        <label for="username">Username</label>
        <input id="username" value="<?= e((string) $u['username']) ?>" disabled>
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
          <?php
          $vt = (string) ($u['vehicle_type'] ?? '');
          $opts = ['6-wheeler (Isuzu / Fuso)', '4-wheeler truck', 'L300 van', 'Reefer / specialized'];
          foreach ($opts as $o) {
              $sel = $o === $vt ? ' selected' : '';
              echo '<option' . $sel . '>' . htmlspecialchars($o, ENT_QUOTES, 'UTF-8') . '</option>';
          }
          ?>
        </select>
      </div>
      <div class="form-row">
        <label for="plate">Plate number *</label>
        <input id="plate" name="plate" required value="<?= e((string) ($u['plate'] ?? '')) ?>">
      </div>
      <div class="form-row">
        <label for="capacity_kg">Capacity (kg)</label>
        <input id="capacity_kg" name="capacity_kg" type="number" min="0" step="1" value="<?= e((string) ($u['capacity_kg'] ?? '0')) ?>">
      </div>
    </div>
    <button type="submit" class="btn btn--primary">Save</button>
  </form>
</div>
