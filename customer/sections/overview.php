<?php
declare(strict_types=1);

$u = auth_user();
$mine = repo_customer_bookings((int) $u['id']);
$pending = count(array_filter($mine, static fn ($b) => ($b['status'] ?? '') === 'pending'));
$active = count(array_filter($mine, static fn ($b) => in_array($b['status'] ?? '', ['assigned', 'in_transit'], true)));
?>
<div class="grid grid--stats">
  <div class="stat">
    <div class="stat__val"><?= count($mine) ?></div>
    <div class="stat__lbl">Total bookings</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= $pending ?></div>
    <div class="stat__lbl">Awaiting driver</div>
  </div>
  <div class="stat">
    <div class="stat__val"><?= $active ?></div>
    <div class="stat__lbl">In progress</div>
  </div>
</div>

<div class="card">
  <h2>Welcome, <?= e((string) $u['name']) ?></h2>
  <p style="margin:0;color:var(--muted);font-size:0.95rem;">
    Book 6-wheelers, 4-wheelers, or L300 vans for pallets and container-scale loads.
    New bookings appear instantly for drivers under <strong>Available Jobs</strong>.
  </p>
</div>

<div class="grid grid--2">
  <div class="card">
    <h2>Quick actions</h2>
    <p style="margin:0 0 1rem;color:var(--muted);font-size:0.9rem;">Start a shipment or check status.</p>
    <a class="btn btn--primary" href="./dashboard.php?section=booking">New booking</a>
    <a class="btn btn--ghost" href="./dashboard.php?section=track" style="margin-left:0.5rem">Track</a>
  </div>
  <div class="card">
    <h2>Need help?</h2>
    <p style="margin:0;color:var(--muted);font-size:0.9rem;">
      Support: <?= e(repo_settings()['support_email'] ?? 'support@express.test') ?> · Region: <?= e(repo_settings()['default_region'] ?? 'NCR') ?>
    </p>
  </div>
</div>
