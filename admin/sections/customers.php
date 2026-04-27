<?php
declare(strict_types=1);

$rows = repo_customers();
?>
<div class="card">
  <h2>Add customer</h2>
  <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/admin_customer_action.php') ?>" novalidate>
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
    </div>
    <button type="submit" class="btn btn--primary">Add customer</button>
  </form>
</div>

<div class="card">
  <h2>Customer list</h2>
  <?php if (!$rows): ?>
    <p style="color:var(--muted)">No customers.</p>
  <?php else: ?>
    <div class="form-row" style="margin:0 0 0.75rem">
      <label for="customers_q">Search customers</label>
      <input id="customers_q" placeholder="Search username / name / email / mobile">
    </div>
    <div class="table-wrap">
      <table id="customers_table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Update details</th>
            <th style="width:90px"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <?php
                $rowText = strtolower(trim(
                  (string) ($r['username'] ?? '') . ' ' .
                  (string) ($r['name'] ?? '') . ' ' .
                  (string) ($r['email'] ?? '') . ' ' .
                  (string) ($r['mobile'] ?? '')
                ));
              ?>
              <td data-search="<?= e($rowText) ?>"><?= (int) ($r['id'] ?? 0) ?></td>
              <td><?= e($r['username'] ?? '') ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_customer_action.php') ?>" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:flex-end;margin:0">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= (int) ($r['id'] ?? 0) ?>">
                  <div class="form-row" style="margin:0;flex:1;min-width:100px">
                    <label>Name</label>
                    <input name="name" value="<?= e($r['name'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:1;min-width:120px">
                    <label>Email</label>
                    <input name="email" type="email" value="<?= e($r['email'] ?? '') ?>" required>
                  </div>
                  <div class="form-row" style="margin:0;flex:1;min-width:100px">
                    <label>Mobile</label>
                    <input name="mobile" value="<?= e($r['mobile'] ?? '') ?>">
                  </div>
                  <button type="submit" class="btn btn--ghost" style="padding:0.45rem 0.65rem;font-size:0.85rem">Save</button>
                </form>
              </td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_customer_action.php') ?>" style="margin:0" onsubmit="return confirm('Remove this customer from the list?');">
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

<script>
(function () {
  var q = document.getElementById('customers_q');
  var table = document.getElementById('customers_table');
  if (!q || !table) return;
  var rows = table.querySelectorAll('tbody tr');
  function apply() {
    var term = (q.value || '').toLowerCase().trim();
    rows.forEach(function (tr) {
      var cell = tr.querySelector('td[data-search]');
      var hay = cell ? (cell.getAttribute('data-search') || '') : '';
      var ok = !term || hay.indexOf(term) !== -1;
      tr.style.display = ok ? '' : 'none';
    });
  }
  q.addEventListener('input', apply);
})();
</script>
