<?php
declare(strict_types=1);

$rows = repo_bookings();
$statuses = ['pending', 'accepted', 'in_transit', 'completed', 'cancelled'];
?>
<div class="card">
  <h2>All bookings</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Review bookings, update status, or remove a record. New bookings are immediately available for drivers to accept.
  </p>
  <?php if (!$rows): ?>
    <p style="color:var(--muted)">No bookings.</p>
  <?php else: ?>
    <div class="grid grid--2" style="margin-bottom:0.75rem">
      <div class="form-row" style="margin:0">
        <label for="bookings_q">Search</label>
        <input id="bookings_q" placeholder="Search booking # / customer / driver / route / status">
      </div>
      <div class="form-row" style="margin:0">
        <label for="bookings_status">Status</label>
        <select id="bookings_status">
          <option value="">All</option>
          <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>"><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="table-wrap">
      <table id="bookings_table">
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Customer</th>
            <th>Driver</th>
            <th>Posting</th>
            <th>Pickup time</th>
            <th>Status</th>
            <th>Payment / completion</th>
            <th>Route</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <?php
                $bn = (string) ($row['booking_number'] ?? '');
                $cust = (string) ($row['customer_name'] ?? '');
                $driverNm = trim((string) ($row['driver_name'] ?? ''));
                $route = (string) ($row['pickup'] ?? '') . ' ' . (string) ($row['dropoff'] ?? '');
                $stVal = (string) ($row['status'] ?? '');
                $rowText = strtolower(trim($bn . ' ' . $cust . ' ' . $driverNm . ' ' . $route . ' ' . $stVal));
                $receipt = (string) ($row['payment_receipt_reference'] ?? '');
                $dcomp = (string) ($row['driver_completion_status'] ?? 'unclear');
              ?>
              <td data-search="<?= e($rowText) ?>" data-status="<?= e($stVal) ?>"><?= e($bn) ?></td>
              <td><?= e($cust) ?></td>
              <td><?php if ($driverNm !== ''): ?><?= e($driverNm) ?><?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?></td>
              <td><?= e(format_timestamp($row['posting_date'] ?? '', 'M j, Y')) ?></td>
              <td><?= e(format_timestamp($row['booking_datetime'] ?? '')) ?></td>
              <td>
                <form method="post" action="../handlers/admin_booking_action.php" style="display:flex;gap:0.35rem;align-items:center;margin:0;flex-wrap:wrap">
                  <?= csrf_field() ?>
                  <input type="hidden" name="booking_number" value="<?= e($row['booking_number'] ?? '') ?>">
                  <select name="action" style="max-width:160px;padding:0.35rem">
                    <?php foreach ($statuses as $s): ?>
                      <option value="<?= e($s) ?>" <?= (($row['status'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn btn--ghost" style="padding:0.35rem 0.5rem;font-size:0.8rem">Save</button>
                </form>
              </td>
              <td style="max-width:220px;font-size:0.8rem;vertical-align:top">
                <?php if ($stVal === 'completed'): ?>
                  <form method="post" action="../handlers/admin_booking_action.php" style="margin:0;display:flex;flex-direction:column;gap:0.35rem">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_number" value="<?= e($row['booking_number'] ?? '') ?>">
                    <input type="hidden" name="action" value="update_meta">
                    <p style="margin:0;font-size:0.75rem;color:var(--muted)">Driver completion: <strong><?= e($dcomp) ?></strong> (set to clear when receipt is saved)</p>
                    <div class="form-row" style="margin:0">
                      <label style="font-size:0.75rem">Receipt ref. (13 digits, required)</label>
                      <input name="payment_receipt_reference" value="<?= e($receipt) ?>" maxlength="13" pattern="\d{13}" inputmode="numeric" autocomplete="off" placeholder="13-digit ref" required style="padding:0.35rem;font-size:0.8rem">
                    </div>
                    <button type="submit" class="btn btn--ghost" style="padding:0.35rem 0.5rem;font-size:0.8rem;align-self:flex-start">Save receipt</button>
                  </form>
                <?php else: ?>
                  <p style="margin:0 0 0.35rem;color:var(--muted);font-size:0.75rem">Record payment when status is <strong>completed</strong>.</p>
                  <?php if ($receipt !== '' || $dcomp !== 'unclear'): ?>
                    <p style="margin:0;font-size:0.75rem">Ref: <?= $receipt !== '' ? e($receipt) : '<span style="color:var(--muted)">—</span>' ?> · Completion: <?= e($dcomp) ?></p>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td><?= e($row['pickup'] ?? '') ?> → <?= e($row['dropoff'] ?? '') ?></td>
              <td>
                <form method="post" action="../handlers/admin_booking_action.php" style="margin:0" onsubmit="return confirm('Delete this booking?');">
                  <?= csrf_field() ?>
                  <input type="hidden" name="booking_number" value="<?= e($row['booking_number'] ?? '') ?>">
                  <input type="hidden" name="action" value="delete">
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
  var q = document.getElementById('bookings_q');
  var st = document.getElementById('bookings_status');
  var table = document.getElementById('bookings_table');
  if (!q || !st || !table) return;
  var rows = table.querySelectorAll('tbody tr');
  function apply() {
    var term = (q.value || '').toLowerCase().trim();
    var status = (st.value || '').toLowerCase().trim();
    rows.forEach(function (tr) {
      var cell = tr.querySelector('td[data-search]');
      var hay = cell ? (cell.getAttribute('data-search') || '') : '';
      var rowStatus = (cell ? (cell.getAttribute('data-status') || '') : '').toLowerCase();
      var ok = true;
      if (term) ok = ok && hay.indexOf(term) !== -1;
      if (status) ok = ok && rowStatus === status;
      tr.style.display = ok ? '' : 'none';
    });
  }
  q.addEventListener('input', apply);
  st.addEventListener('change', apply);
})();
</script>
