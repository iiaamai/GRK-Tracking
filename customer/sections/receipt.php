<?php
declare(strict_types=1);

$u = auth_user();
$bookingNumber = trim((string) ($_GET['booking_number'] ?? ''));

if ($bookingNumber === '') {
    flash_set('error', 'Choose a booking to view its receipt from My Bookings.');
    redirect(BASE_URL . '/customer/dashboard.php?section=bookings');
}

$b = repo_find_booking_by_number($bookingNumber);
$custId = (int) ($u['id'] ?? 0);

if ($b === null || (int) ($b['customer_id'] ?? 0) !== $custId) {
    flash_set('error', 'That booking could not be found on your account.');
    redirect(BASE_URL . '/customer/dashboard.php?section=bookings');
}

$payRef = (string) ($b['payment_receipt_reference'] ?? '');
$payoutAmt = isset($b['payout']) && $b['payout'] !== null ? (float) $b['payout'] : null;

?>
<div class="receipt-actions no-print card" style="display:flex;flex-wrap:wrap;gap:0.65rem;align-items:center;margin-bottom:1rem">
  <a href="<?= e('./dashboard.php?section=bookings') ?>" class="btn btn--ghost">← Back to My Bookings</a>
  <button type="button" class="btn btn--primary" onclick="window.print()">Print / Save as PDF</button>
  <p style="margin:0;font-size:0.85rem;color:var(--muted);flex:1;min-width:12rem">
    Use your browser print dialog and choose <strong>Save as PDF</strong> for a PDF copy.
  </p>
</div>

<div class="booking-receipt card" role="article" aria-labelledby="receipt-heading">
  <header class="booking-receipt__head">
    <div>
      <h2 id="receipt-heading" style="margin:0 0 0.35rem;font-size:1.35rem">Payment & booking receipt</h2>
      <p style="margin:0;font-size:0.92rem;color:var(--muted)">GRK Trucking Services · Customer copy</p>
    </div>
  </header>

  <div class="booking-receipt__block">
    <h3 style="margin:0 0 0.65rem;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--muted);font-weight:650">Booking</h3>
    <dl class="booking-receipt__dl">
      <div><dt>Booking number</dt><dd><strong><?= e($b['booking_number'] ?? '') ?></strong></dd></div>
      <div><dt>Posted</dt><dd><?= e(format_timestamp($b['posting_date'] ?? '', 'M j, Y')) ?></dd></div>
      <div><dt>Pickup date &amp; time</dt><dd><?= e(format_timestamp($b['booking_datetime'] ?? '')) ?></dd></div>
      <div><dt>Status</dt><dd><?= e((string) ($b['status'] ?? '')) ?></dd></div>
    </dl>
  </div>

  <div class="booking-receipt__block">
    <h3 style="margin:0 0 0.65rem;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--muted);font-weight:650">Billing</h3>
    <dl class="booking-receipt__dl">
      <div>
        <dt>Quoted freight / service fee</dt>
        <dd class="booking-receipt__amt"><?= e(format_php_money($payoutAmt)) ?></dd>
      </div>
      <div>
        <dt>Payment receipt reference</dt>
        <dd>
          <?php if ($payRef !== ''): ?>
            <?= e($payRef) ?>
          <?php else: ?>
            <span style="color:var(--muted)">Pending — GRK records this after payment is confirmed.</span>
          <?php endif; ?>
        </dd>
      </div>
    </dl>
  </div>

  <div class="booking-receipt__block">
    <h3 style="margin:0 0 0.65rem;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--muted);font-weight:650">Customer</h3>
    <dl class="booking-receipt__dl">
      <div><dt>Account</dt><dd><?= e($b['username'] ?? (string) ($u['username'] ?? '')) ?></dd></div>
      <div><dt>Name</dt><dd><?= e($b['name'] ?? '') ?></dd></div>
      <div><dt>Email</dt><dd><?= e($b['email'] ?? (string) ($u['email'] ?? '')) ?></dd></div>
      <div><dt>Mobile</dt><dd><?= e($b['mobile'] ?? (string) ($u['mobile'] ?? '')) ?></dd></div>
    </dl>
  </div>

  <div class="booking-receipt__block">
    <h3 style="margin:0 0 0.65rem;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.04em;color:var(--muted);font-weight:650">Shipment</h3>
    <dl class="booking-receipt__dl">
      <div><dt>Vehicle type</dt><dd><?= e($b['vehicle_type'] ?? '—') ?></dd></div>
      <div class="booking-receipt__dl-full"><dt>Pickup</dt><dd><?= e($b['pickup'] ?? '—') ?></dd></div>
      <div class="booking-receipt__dl-full"><dt>Drop-off</dt><dd><?= e($b['dropoff'] ?? '—') ?></dd></div>
      <div class="booking-receipt__dl-full"><dt>Cargo / parcel description</dt><dd><?= e((string) ($b['cargo_desc'] ?? '')) !== '' ? e((string) $b['cargo_desc']) : '—' ?></dd></div>
    </dl>
  </div>

  <footer class="booking-receipt__foot no-print" style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border);font-size:0.85rem;color:var(--muted)">
    This document is generated from your account for your records. For questions, contact GRK with your booking number.
  </footer>
</div>
