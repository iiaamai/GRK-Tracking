<?php
declare(strict_types=1);

$fleet = repo_fleet();
$types = ['6-wheeler', '4-wheeler', 'L300', '2-wheeler', 'Reefer / specialized', 'Other'];
$st = ['available', 'in_use', 'maintenance'];
?>
<div class="card">
  <h2>Add vehicle</h2>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/admin_fleet_action.php') ?>" novalidate>
    <input type="hidden" name="action" value="add">
    <div class="grid grid--2">
      <div class="form-row">
        <label for="label">Label *</label>
        <input id="label" name="label" required maxlength="120" placeholder="e.g. Isuzu F-Series">
      </div>
      <div class="form-row">
        <label for="type">Type *</label>
        <select id="type" name="type" required>
          <option value="">Select…</option>
          <?php foreach ($types as $t): ?>
            <option value="<?= e($t) ?>"><?= e($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <label for="plate">Plate *</label>
        <input id="plate" name="plate" required maxlength="20">
      </div>
      <div class="form-row">
        <label for="capacity_kg">Capacity (kg)</label>
        <input id="capacity_kg" name="capacity_kg" type="number" min="0" value="0">
      </div>
      <div class="form-row">
        <label for="status">Status</label>
        <select id="status" name="status">
          <?php foreach ($st as $s): ?>
            <option value="<?= e($s) ?>"><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <button type="submit" class="btn btn--primary">Add to fleet</button>
  </form>
</div>

<div class="card">
  <h2>Fleet inventory</h2>
  <?php if (!$fleet): ?>
    <p style="color:var(--muted)">No vehicles.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Label</th>
            <th>Update</th>
            <th style="width:90px"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fleet as $v): ?>
            <tr>
              <td><?= (int) ($v['id'] ?? 0) ?></td>
              <td><?= e($v['label'] ?? '') ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_fleet_action.php') ?>" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;margin:0">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= (int) ($v['id'] ?? 0) ?>">
                  <div class="form-row" style="margin:0;flex:1;min-width:100px">
                    <label>Label</label>
                    <input name="label" value="<?= e($v['label'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 110px">
                    <label>Type</label>
                    <select name="type">
                      <?php foreach ($types as $t): ?>
                        <option value="<?= e($t) ?>" <?= (($v['type'] ?? '') === $t) ? 'selected' : '' ?>><?= e($t) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 90px">
                    <label>Plate</label>
                    <input name="plate" value="<?= e($v['plate'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 70px">
                    <label>kg</label>
                    <input name="capacity_kg" type="number" min="0" value="<?= (int) ($v['capacity_kg'] ?? 0) ?>">
                  </div>
                  <div class="form-row" style="margin:0;flex:0 0 110px">
                    <label>Status</label>
                    <select name="status">
                      <?php foreach ($st as $s): ?>
                        <option value="<?= e($s) ?>" <?= (($v['status'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <button type="submit" class="btn btn--ghost" style="padding:0.45rem 0.65rem;font-size:0.85rem">Save</button>
                </form>
              </td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_fleet_action.php') ?>" style="margin:0" onsubmit="return confirm('Remove this vehicle?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) ($v['id'] ?? 0) ?>">
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
