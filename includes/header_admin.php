<?php
declare(strict_types=1);
/** @var string $section */
/** @var string $pageTitle */
$base = BASE_URL . '/admin/dashboard.php';
$logoUrl = BASE_URL . '/assets/GRK%20LOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Admin | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_admin($section) ?></style>
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar__brand">
        <div class="sidebar__logoWrap">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </div>
        <small>Administration</small>
      </div>
      <nav class="sidebar__nav" aria-label="Admin">
        <a href="<?= e($base . '?section=overview') ?>" class="<?= $section === 'overview' ? 'is-active' : '' ?>">Overview</a>
        <a href="<?= e($base . '?section=earnings_reports') ?>" class="<?= $section === 'earnings_reports' ? 'is-active' : '' ?>">Earnings & Reports</a>
        <a href="<?= e($base . '?section=bookings') ?>" class="<?= $section === 'bookings' ? 'is-active' : '' ?>">All Bookings</a>
        <a href="<?= e($base . '?section=drivers') ?>" class="<?= $section === 'drivers' ? 'is-active' : '' ?>">Drivers</a>
        <a href="<?= e($base . '?section=customers') ?>" class="<?= $section === 'customers' ? 'is-active' : '' ?>">Customers</a>
        <a href="<?= e($base . '?section=fleet') ?>" class="<?= $section === 'fleet' ? 'is-active' : '' ?>">Fleet Management</a>
        <a href="<?= e($base . '?section=settings') ?>" class="<?= $section === 'settings' ? 'is-active' : '' ?>">Settings</a>
      </nav>
      <div class="sidebar__foot">
        <a class="btn btn--ghost" style="width:100%" href="<?= e(BASE_URL . '/handlers/logout.php?portal=admin') ?>">Log out</a>
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
        <p>Operations control: bookings, people, fleet, and configuration.</p>
      </header>
