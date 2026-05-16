<?php
declare(strict_types=1);

$fleet = repo_fleet();
$types = ['6-wheeler', '4-wheeler', 'L300', 'Reefer / specialized', 'Other'];
$st = ['available', 'in_use', 'maintenance'];
?>
<div class="card">
  <h2>Add vehicle</h2>
  <form class="js-validate" method="post" action="../handlers/admin_fleet_action.php" novalidate>
    <?= csrf_field() ?>
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
    <div class="grid grid--2" style="margin-bottom:0.75rem">
      <div class="form-row" style="margin:0">
        <label for="fleet_q">Search</label>
        <input id="fleet_q" placeholder="Search label / plate / type / status">
      </div>
      <div class="form-row" style="margin:0">
        <label for="fleet_status">Status</label>
        <select id="fleet_status">
          <option value="">All</option>
          <?php foreach ($st as $s): ?>
            <option value="<?= e($s) ?>"><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row" style="margin:0">
        <label for="fleet_type_filter">Type</label>
        <select id="fleet_type_filter">
          <option value="">All</option>
          <?php foreach ($types as $t): ?>
            <option value="<?= e($t) ?>"><?= e($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="table-wrap">
      <table id="fleet_table" class="js-paginated-table" data-page-size="5">
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
              <?php
                $rowText = strtolower(trim(
                  (string) ($v['label'] ?? '') . ' ' .
                  (string) ($v['plate'] ?? '') . ' ' .
                  (string) ($v['type'] ?? '') . ' ' .
                  (string) ($v['status'] ?? '')
                ));
              ?>
              <td data-search="<?= e($rowText) ?>" data-status="<?= e((string) ($v['status'] ?? '')) ?>" data-type="<?= e((string) ($v['type'] ?? '')) ?>"><?= (int) ($v['id'] ?? 0) ?></td>
              <td><?= e($v['label'] ?? '') ?></td>
              <td>
                <form method="post" action="../handlers/admin_fleet_action.php" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;margin:0">
                  <?= csrf_field() ?>
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
                <form method="post" action="../handlers/admin_fleet_action.php" style="margin:0" onsubmit="return confirm('Remove this vehicle?');">
                  <?= csrf_field() ?>
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

<script>
(function () {
  var q = document.getElementById('fleet_q');
  var st = document.getElementById('fleet_status');
  var ty = document.getElementById('fleet_type_filter');
  var table = document.getElementById('fleet_table');
  if (!q || !st || !ty || !table) return;
  var rows = table.querySelectorAll('tbody tr');
  function apply() {
    var term = (q.value || '').toLowerCase().trim();
    var status = (st.value || '').toLowerCase().trim();
    var type = (ty.value || '').toLowerCase().trim();
    rows.forEach(function (tr) {
      var cell = tr.querySelector('td[data-search]');
      var hay = cell ? (cell.getAttribute('data-search') || '') : '';
      var rowStatus = cell ? (cell.getAttribute('data-status') || '') : '';
      var rowType = cell ? (cell.getAttribute('data-type') || '') : '';
      var ok = true;
      if (term) ok = ok && hay.indexOf(term) !== -1;
      if (status) ok = ok && rowStatus === status;
      if (type) ok = ok && rowType === type;
      tr.style.display = ok ? '' : 'none';
    });
    table.dispatchEvent(new CustomEvent('paginate-refresh', { bubbles: true }));
  }
  q.addEventListener('input', apply);
  st.addEventListener('change', apply);
  ty.addEventListener('change', apply);
})();
</script>
