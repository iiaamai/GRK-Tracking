<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
$logoUrl = '../assets/GRK%20LOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer — GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_landing() ?></style>
</head>
<body>
  <div class="landing landing--customer">
    <header class="landing__topbar">
      <span class="landing__logo">
        <span class="landing__logoFrame">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </span>
        <span class="landing__logoText">
          <strong>GRK TRUCKING SERVICES</strong>
          <small>Customer portal</small>
        </span>
      </span>
      <a class="landing__back" href="../index.php">← All portals</a>
    </header>
    <section class="landing__hero">
      <span class="landing__eyebrow">Book trucks & track cargo</span>
      <h1>Schedule pallet and container-scale moves with clear status updates.</h1>
      <p class="lead">Create bookings, track by booking number or username, and manage your profile after signing in.</p>
      <div class="landing__actions">
        <a class="btn btn--primary" href="./login.php">Customer login</a>
        <a class="btn btn--ghost" href="../index.php">Back</a>
      </div>
    </section>
    <section class="landing__grid">
      <div class="landing-card">
        <h3>Truck booking</h3>
        <p>Choose vehicle class, pickup window, and route details for large batches.</p>
      </div>
      <div class="landing-card">
        <h3>Track shipment</h3>
        <p>Look up by booking number or customer username to see live status.</p>
      </div>
      <div class="landing-card">
        <h3>My bookings</h3>
        <p>Full history with posting dates, routes, and operational status.</p>
      </div>
    </section>
  </div>
</body>
</html>
