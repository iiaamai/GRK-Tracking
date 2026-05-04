<?php
declare(strict_types=1);
/** @var string $section */
/** @var string $pageTitle */
$base = 'dashboard.php';
$logoUrl = '../assets/GRKLOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Customer | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_customer($section) ?></style>
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar__brand">
        <div class="sidebar__logoWrap">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </div>
        <small>Customer portal</small>
      </div>
      <nav class="sidebar__nav" aria-label="Customer">
        <a href="<?= e($base . '?section=overview') ?>" class="<?= $section === 'overview' ? 'is-active' : '' ?>">Overview</a>
        <a href="<?= e($base . '?section=booking') ?>" class="<?= $section === 'booking' ? 'is-active' : '' ?>">Booking</a>
        <a href="<?= e($base . '?section=track') ?>" class="<?= $section === 'track' ? 'is-active' : '' ?>">Track Shipment</a>
        <a href="<?= e($base . '?section=bookings') ?>" class="<?= $section === 'bookings' ? 'is-active' : '' ?>">My Bookings</a>
        <a href="<?= e($base . '?section=profile') ?>" class="<?= $section === 'profile' ? 'is-active' : '' ?>">Profile</a>
      </nav>
      <div class="sidebar__foot">
        <form method="post" action="../handlers/logout.php" style="margin:0">
          <?= csrf_field() ?>
          <input type="hidden" name="portal" value="customer">
          <button type="submit" class="btn btn--ghost" style="width:100%">Log out</button>
        </form>
      </div>
    </aside>
    <main class="main">
      <?php if ($m = flash_get('success')): ?>
        <div class="flash flash--ok"><?= e($m) ?></div>
      <?php endif; ?>
      <?php if ($m = flash_get('error')): ?>
        <div class="flash flash--err"><?= e($m) ?></div>
      <?php endif; ?>
      <header class="page-head">
        <h1><?= e($pageTitle) ?></h1>
        <p>Truck-based delivery for large batches and container-scale urban & regional moves.</p>
      </header>
