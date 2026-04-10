<?php
declare(strict_types=1);

$rows = repo_bookings();
$statuses = ['pending', 'ready_for_assignment', 'assigned', 'in_transit', 'completed', 'cancelled'];
?>
<div class="card">
  <h2>All bookings</h2>
  <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">
    Review new bookings, upload a <strong>gate pass</strong> (required before drivers can accept), then update status or remove a record.
  </p>
  <?php if (!$rows): ?>
    <p style="color:var(--muted)">No bookings.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Booking #</th>
            <th>Customer</th>
            <th>Posting</th>
            <th>Pickup time</th>
            <th>Status</th>
            <th>Gate pass</th>
            <th>EIR</th>
            <th>Route</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= e($row['booking_number'] ?? '') ?></td>
              <td><?= e($row['name'] ?? '') ?></td>
              <td><?= e(format_timestamp($row['posting_date'] ?? '', 'M j, Y')) ?></td>
              <td><?= e(format_timestamp($row['booking_datetime'] ?? '')) ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_booking_action.php') ?>" style="display:flex;gap:0.35rem;align-items:center;margin:0;flex-wrap:wrap">
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
                <?php
                  $gp = (string) ($row['gatepass_image'] ?? '');
                  $bn = (string) ($row['booking_number'] ?? '');
                  $gpUrl = $gp !== '' ? (BASE_URL . '/handlers/view_booking_doc.php?booking_number=' . urlencode($bn) . '&doc=gatepass') : '';
                ?>
                <?php if ($gpUrl !== ''): ?>
                  <div style="margin-bottom:0.35rem">
                    <a href="<?= e($gpUrl) ?>" target="_blank" rel="noopener noreferrer">View current</a>
                  </div>
                <?php endif; ?>
                <?php if (!in_array(($row['status'] ?? ''), ['completed', 'cancelled'], true)): ?>
                  <form method="post" enctype="multipart/form-data" action="<?= e(BASE_URL . '/handlers/admin_booking_gatepass.php') ?>" style="margin:0;display:flex;flex-direction:column;gap:0.35rem;align-items:flex-start">
                    <input type="hidden" name="booking_number" value="<?= e($row['booking_number'] ?? '') ?>">
                    <input type="file" name="gatepass" accept="image/*" required style="max-width:100%;font-size:0.75rem">
                    <button type="submit" class="btn btn--ghost" style="padding:0.3rem 0.45rem;font-size:0.75rem"><?= $gpUrl !== '' ? 'Replace' : 'Upload' ?></button>
                  </form>
                <?php else: ?>
                  <span style="color:var(--muted)">—</span>
                <?php endif; ?>
              </td>
              <td style="max-width:160px;font-size:0.8rem;vertical-align:top">
                <?php
                  $eir = (string) ($row['eir_image'] ?? '');
                  $eirUrl = $eir !== '' ? (BASE_URL . '/handlers/view_booking_doc.php?booking_number=' . urlencode($bn) . '&doc=eir') : '';
                ?>
                <?php if ($eirUrl !== ''): ?>
                  <a href="<?= e($eirUrl) ?>" target="_blank" rel="noopener noreferrer">View EIR</a>
                <?php else: ?>
                  <span style="color:var(--muted)">—</span>
                <?php endif; ?>
              </td>
              <td><?= e($row['pickup'] ?? '') ?> → <?= e($row['dropoff'] ?? '') ?></td>
              <td>
                <form method="post" action="<?= e(BASE_URL . '/handlers/admin_booking_action.php') ?>" style="margin:0" onsubmit="return confirm('Delete this booking?');">
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
