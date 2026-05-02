<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
$logoUrl = './assets/GRK%20LOGO.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_landing() ?></style>
</head>
<body>
  <div class="landing">
    <header class="landing__topbar">
      <span class="landing__logo">
        <span class="landing__logoFrame">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </span>
        <span class="landing__logoText">
          <strong>GRK TRUCKING SERVICES</strong>
          <small>Urban & regional delivery</small>
        </span>
      </span>
      <span class="landing__tagline">Urban & regional truck delivery</span>
    </header>
    <section class="landing__hero">
      <span class="landing__eyebrow">Reliable trucking & logistics</span>
      <h1>Large batches and container-scale freight, moved by trucks you can trust.</h1>
      <p class="lead">Choose a portal to book shipments, run deliveries, or manage operations. Data is stored in MySQL on XAMPP; sign-in uses session cookies.</p>
      <div class="landing__actions">
        <a class="btn btn--primary" href="./customer/index.php">Customer</a>
        <a class="btn btn--ghost" href="./driver/index.php">Driver</a>
        <a class="btn btn--ghost" href="./admin/index.php">Admin</a>
      </div>
    </section>
    <section class="landing__grid">
      <div class="landing-card">
        <h3>Fleet</h3>
        <p>6-wheelers, 4-wheelers, and L300 vans for dense city legs.</p>
      </div>
      <div class="landing-card">
        <h3>Live flow</h3>
        <p>Customer bookings surface instantly to drivers as available jobs.</p>
      </div>
      <div class="landing-card">
        <h3>Operations</h3>
        <p>Admins manage bookings, people, fleet, and configuration in one place.</p>
      </div>
    </section>
  </div>
</body>
</html>
