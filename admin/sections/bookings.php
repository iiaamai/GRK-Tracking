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
      <table id="bookings_table" class="js-paginated-table" data-page-size="5">
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
                <form class="js-booking-status-form" method="post" action="../handlers/admin_booking_action.php" style="display:flex;gap:0.35rem;align-items:center;margin:0;flex-wrap:wrap">
                  <?= csrf_field() ?>
                  <input type="hidden" name="booking_number" value="<?= e($row['booking_number'] ?? '') ?>">
                  <select
                    name="action"
                    class="js-booking-status"
                    data-booking-number="<?= e($bn) ?>"
                    data-current-status="<?= e($stVal) ?>"
                    style="max-width:160px;padding:0.35rem"
                  >
                    <?php foreach ($statuses as $s): ?>
                      <option value="<?= e($s) ?>" <?= (($row['status'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn btn--ghost js-booking-status-save" style="padding:0.35rem 0.5rem;font-size:0.8rem">Save</button>
                </form>
                <?php if ($stVal === 'cancelled' && trim((string) ($row['cancel_message'] ?? '')) !== ''): ?>
                  <p style="margin:0.35rem 0 0;font-size:0.72rem;color:var(--muted);max-width:14rem">Cancel: <?= e((string) $row['cancel_message']) ?></p>
                <?php endif; ?>
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

<div id="cancel_booking_modal" class="cancel-modal" hidden aria-hidden="true">
  <div class="cancel-modal__backdrop" data-cancel-modal-close tabindex="-1" aria-hidden="true"></div>
  <div class="cancel-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cancel_modal_title">
    <h3 id="cancel_modal_title" class="cancel-modal__title">Cancel booking</h3>
    <p class="cancel-modal__lead">Select why this booking is being cancelled. The customer will see this on their receipt.</p>
    <p class="cancel-modal__ref">Booking: <strong id="cancel_modal_booking_label"></strong></p>
    <form id="cancel_booking_form" method="post" action="../handlers/admin_booking_action.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="cancel">
      <input type="hidden" name="booking_number" id="cancel_modal_booking_number" value="">
      <fieldset class="cancel-modal__reasons">
        <legend class="sr-only">Cancellation reason</legend>
        <?php foreach (booking_cancel_reasons() as $key => $label): ?>
          <label class="cancel-modal__reason">
            <input type="radio" name="cancel_reason" value="<?= e($key) ?>" <?= $key === 'customer_request' ? 'checked' : '' ?> required>
            <span><?= e($label) ?></span>
          </label>
        <?php endforeach; ?>
      </fieldset>
      <div id="cancel_other_wrap" class="form-row cancel-modal__other" hidden>
        <label for="cancel_other">Please describe</label>
        <textarea id="cancel_other" name="cancel_other" rows="3" maxlength="500" placeholder="Enter cancellation details"></textarea>
      </div>
      <div class="cancel-modal__actions">
        <button type="button" class="btn btn--ghost" data-cancel-modal-close>Close</button>
        <button type="submit" class="btn btn--primary">Save cancellation</button>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  var q = document.getElementById('bookings_q');
  var st = document.getElementById('bookings_status');
  var table = document.getElementById('bookings_table');
  if (q && st && table) {
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
      table.dispatchEvent(new CustomEvent('paginate-refresh', { bubbles: true }));
    }
    q.addEventListener('input', apply);
    st.addEventListener('change', apply);
  }

  var modal = document.getElementById('cancel_booking_modal');
  var cancelForm = document.getElementById('cancel_booking_form');
  var bnInput = document.getElementById('cancel_modal_booking_number');
  var bnLabel = document.getElementById('cancel_modal_booking_label');
  var otherWrap = document.getElementById('cancel_other_wrap');
  var otherField = document.getElementById('cancel_other');

  function syncOtherField() {
    if (!otherWrap || !otherField) return;
    var otherRadio = cancelForm && cancelForm.querySelector('input[name="cancel_reason"][value="other"]');
    var show = otherRadio && otherRadio.checked;
    otherWrap.hidden = !show;
    otherField.required = !!show;
    if (!show) otherField.value = '';
  }

  function openCancelModal(bookingNumber) {
    if (!modal || !bnInput || !bnLabel) return;
    bnInput.value = bookingNumber;
    bnLabel.textContent = bookingNumber;
    if (cancelForm) {
      var first = cancelForm.querySelector('input[name="cancel_reason"]');
      if (first) first.checked = true;
    }
    if (otherField) otherField.value = '';
    syncOtherField();
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    var focusEl = cancelForm && cancelForm.querySelector('input[name="cancel_reason"]');
    if (focusEl) focusEl.focus();
  }

  function closeCancelModal() {
    if (!modal) return;
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.js-booking-status').forEach(function (sel) {
    sel.addEventListener('change', function () {
      if (sel.value !== 'cancelled') return;
      var current = sel.getAttribute('data-current-status') || '';
      var bn = sel.getAttribute('data-booking-number') || '';
      sel.value = current;
      if (current === 'cancelled') return;
      openCancelModal(bn);
    });
  });

  if (cancelForm) {
    cancelForm.addEventListener('change', function (e) {
      if (e.target && e.target.name === 'cancel_reason') syncOtherField();
    });
    cancelForm.addEventListener('submit', function () {
      if (otherField && otherField.required && otherField.value.trim() === '') {
        otherField.focus();
      }
    });
  }

  document.querySelectorAll('[data-cancel-modal-close]').forEach(function (el) {
    el.addEventListener('click', closeCancelModal);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal && !modal.hidden) closeCancelModal();
  });
})();
</script>
