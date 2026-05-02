<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
$logoUrl = BASE_URL . '/assets/GRK%20LOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_landing() ?></style>
</head>
<body>
  <div class="landing landing--admin">
    <header class="landing__topbar">
      <span class="landing__logo">
        <span class="landing__logoFrame">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </span>
        <span class="landing__logoText">
          <strong>GRK TRUCKING SERVICES</strong>
          <small>Admin portal</small>
        </span>
      </span>
      <a class="landing__back" href="<?= e(BASE_URL . '/index.php') ?>">← All portals</a>
    </header>
    <section class="landing__hero">
      <span class="landing__eyebrow">Control tower</span>
      <h1>Bookings, people, fleet, and configuration in one secure dashboard.</h1>
      <p class="lead">Full CRUD backed by MySQL (XAMPP).</p>
      <div class="landing__actions">
        <a class="btn btn--primary" href="<?= e(BASE_URL . '/admin/login.php') ?>">Admin login</a>
        <a class="btn btn--ghost" href="<?= e(BASE_URL . '/index.php') ?>">Back</a>
      </div>
    </section>
    <section class="landing__grid">
      <div class="landing-card">
        <h3>Bookings</h3>
        <p>Override statuses, clear bad records, and keep the pipeline visible.</p>
      </div>
      <div class="landing-card">
        <h3>People</h3>
        <p>Manage customers and drivers before wiring to production tables.</p>
      </div>
      <div class="landing-card">
        <h3>Fleet & settings</h3>
        <p>Vehicle inventory and operational defaults for the network.</p>
      </div>
    </section>
  </div>
</body>
</html>
