<?php
declare(strict_types=1);

$rows = repo_drivers();
$vtypes = ['6-wheeler (Isuzu / Fuso)', '4-wheeler truck', 'L300 van', '2-wheeler (express)', 'Reefer / specialized'];
?>
<div class="card">
  <h2>Add driver</h2>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/admin_driver_action.php') ?>" novalidate>
    <input type="hidden" name="action" value="add">
    <div class="grid grid--2">
      <div class="form-row">
        <label for="username">Username *</label>
        <input id="username" name="username" required maxlength="80">
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input id="password" name="password" value="demo123" maxlength="120">
      </div>
      <div class="form-row">
        <label for="name">Name *</label>
        <input id="name" name="name" required maxlength="120">
      </div>
      <div class="form-row">
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" required maxlength="120">
      </div>
      <div class="form-row">
        <label for="mobile">Mobile</label>
        <input id="mobile" name="mobile" maxlength="40">
      </div>
      <div class="form-row">
        <label for="vehicle_type">Vehicle type *</label>
        <select id="vehicle_type" name="vehicle_type" required>
          <option value="">Select…</option>
          <?php foreach ($vtypes as $v): ?>
            <option value="<?= e($v) ?>"><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <label for="plate">Plate *</label>
        <input id="plate" name="plate" required maxlength="20">
      </div>
      <div class="form-row">
        <label for="capacity_kg">Capacity (kg)</label>
        <input id="capacity_kg" name="capacity_kg" type="number" min="0" value="1500">
      </div>
    </div>
    <button type="submit" class="btn btn--primary">Add driver</button>
  </form>
</div>

<div class="card">
  <h2>Driver roster</h2>
  <?php if (!$rows): ?>
    <p style="color:var(--muted)">No drivers.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Update</th>
            <th style="width:90px"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= (int) ($r['id'] ?? 0) ?></td>
              <td><?= e($r['username'] ?? '') ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_driver_action.php') ?>" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;margin:0">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= (int) ($r['id'] ?? 0) ?>">
                  <div class="form-row" style="margin:0;flex:1;min-width:90px">
                    <label>Name</label>
                    <input name="name" value="<?= e($r['name'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:1;min-width:110px">
                    <label>Email</label>
                    <input name="email" type="email" value="<?= e($r['email'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:1;min-width:90px">
                    <label>Mobile</label>
                    <input name="mobile" value="<?= e($r['mobile'] ?? '') ?>">
                  </div>
                  <div class="form-row" style="margin:0;flex:1;min-width:120px">
                    <label>Vehicle</label>
                    <select name="vehicle_type">
                      <?php foreach ($vtypes as $v): ?>
                        <option value="<?= e($v) ?>" <?= (($r['vehicle_type'] ?? '') === $v) ? 'selected' : '' ?>><?= e($v) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 80px">
                    <label>Plate</label>
                    <input name="plate" value="<?= e($r['plate'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 80px">
                    <label>kg</label>
                    <input name="capacity_kg" type="number" min="0" value="<?= (int) ($r['capacity_kg'] ?? 0) ?>">
                  </div>
                  <button type="submit" class="btn btn--ghost" style="padding:0.45rem 0.65rem;font-size:0.85rem">Save</button>
                </form>
              </td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_driver_action.php') ?>" style="margin:0" onsubmit="return confirm('Remove this driver?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) ($r['id'] ?? 0) ?>">
                  <button type="submit" class="btn btn--danger" style="padding:0.35rem 0.5rem;font-size:0.8rem">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
