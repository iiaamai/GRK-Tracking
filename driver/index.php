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
  <title>Driver — GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_landing() ?></style>
</head>
<body>
  <div class="landing landing--driver">
    <header class="landing__topbar">
      <span class="landing__logo">
        <span class="landing__logoFrame">
          <img src="<?= e($logoUrl) ?>" alt="GRK Trucking Services logo">
        </span>
        <span class="landing__logoText">
          <strong>GRK TRUCKING SERVICES</strong>
          <small>Driver portal</small>
        </span>
      </span>
      <a class="landing__back" href="<?= e(BASE_URL . '/index.php') ?>">← All portals</a>
    </header>
    <section class="landing__hero">
      <span class="landing__eyebrow">Fleet & jobs</span>
      <h1>Accept jobs the moment customers book, then run the route.</h1>
      <p class="lead">Available jobs update automatically. Track deliveries, earnings, and your vehicle profile.</p>
      <div class="landing__actions">
        <a class="btn btn--primary" href="<?= e(BASE_URL . '/driver/login.php') ?>">Driver login</a>
        <a class="btn btn--ghost" href="<?= e(BASE_URL . '/index.php') ?>">Back</a>
      </div>
    </section>
    <section class="landing__grid">
      <div class="landing-card">
        <h3>Available jobs</h3>
        <p>Pending customer bookings appear here for one-click acceptance.</p>
      </div>
      <div class="landing-card">
        <h3>Deliveries</h3>
        <p>Move from assigned → in transit → completed with payout tracking.</p>
      </div>
      <div class="landing-card">
        <h3>Vehicle profile</h3>
        <p>Keep type, plate, and capacity aligned with your assigned unit.</p>
      </div>
    </section>
  </div>
</body>
</html>
