<?php
declare(strict_types=1);
/** @var string $section */
/** @var string $pageTitle */
$base = 'dashboard.php';
$logoUrl = '../assets/GRK%20LOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Driver | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_driver($section) ?></style>
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar__brand">
        <div class="sidebar__logoWrap">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </div>
        <small>Driver portal</small>
      </div>
      <nav class="sidebar__nav" aria-label="Driver">
        <a href="<?= e($base . '?section=overview') ?>" class="<?= $section === 'overview' ? 'is-active' : '' ?>">Overview</a>
        <a href="<?= e($base . '?section=jobs') ?>" class="<?= $section === 'jobs' ? 'is-active' : '' ?>">Available Jobs</a>
        <a href="<?= e($base . '?section=deliveries') ?>" class="<?= $section === 'deliveries' ? 'is-active' : '' ?>">My Deliveries</a>
        <a href="<?= e($base . '?section=profile') ?>" class="<?= $section === 'profile' ? 'is-active' : '' ?>">Profile</a>
      </nav>
      <div class="sidebar__foot">
        <form method="post" action="../handlers/logout.php" style="margin:0">
          <?= csrf_field() ?>
          <input type="hidden" name="portal" value="driver">
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
        <p>Accept jobs, run deliveries, and keep your vehicle profile up to date.</p>
      </header>
