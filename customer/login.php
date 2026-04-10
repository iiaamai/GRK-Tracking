<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (auth_user() && (auth_user()['role'] ?? '') === 'customer') {
    redirect(BASE_URL . '/customer/dashboard.php');
}

$err = flash_get('error');
$ok = flash_get('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer login — GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset_v('css/global.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset_v('css/login.css')) ?>">
</head>
<body>
  <div class="login-page">
    <div class="login-card login-card--stack">
      <h1>Customer sign in</h1>
      <p class="sub">Separate login for booking customers. Session-based auth.</p>
      <?php if ($ok): ?>
        <div class="flash flash--ok"><?= e($ok) ?></div>
      <?php endif; ?>
      <?php if ($err): ?>
        <div class="flash flash--err"><?= e($err) ?></div>
      <?php endif; ?>
      <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/login_customer.php') ?>" novalidate>
        <div class="form-row">
          <label for="login_username">Username</label>
          <input id="login_username" name="username" required autocomplete="username" maxlength="80">
        </div>
        <div class="form-row">
          <label for="login_password">Password</label>
          <input id="login_password" name="password" type="password" required autocomplete="current-password" maxlength="120">
        </div>
        <div class="actions">
          <button type="submit" class="btn btn--primary">Sign in</button>
        </div>
      </form>
      <div class="demo-hint">
        Demo: <code>acme_corp</code> / <code>demo123</code> or <code>metro_retail</code> / <code>demo123</code>
      </div>

      <div class="login-divider" role="presentation"></div>

      <h2 class="login-register-title">Create an account</h2>
      <p class="login-register-lead">Register to book trucks and manage shipments.</p>
      <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/register_customer.php') ?>" novalidate>
        <div class="grid grid--2">
          <div class="form-row">
            <label for="reg_username">Username *</label>
            <input id="reg_username" name="username" required maxlength="80" autocomplete="username" pattern="[a-zA-Z0-9._\-]+" title="Letters, numbers, dot, underscore, hyphen">
          </div>
          <div class="form-row">
            <label for="reg_name">Full name *</label>
            <input id="reg_name" name="name" required maxlength="120" autocomplete="name">
          </div>
          <div class="form-row">
            <label for="reg_email">Email *</label>
            <input id="reg_email" name="email" type="email" required maxlength="120" autocomplete="email">
          </div>
          <div class="form-row">
            <label for="reg_mobile">Mobile *</label>
            <input id="reg_mobile" name="mobile" required maxlength="40" autocomplete="tel">
          </div>
          <div class="form-row">
            <label for="reg_password">Password *</label>
            <input id="reg_password" name="password" type="password" required minlength="6" maxlength="120" autocomplete="new-password">
          </div>
          <div class="form-row">
            <label for="reg_password_confirm">Confirm password *</label>
            <input id="reg_password_confirm" name="password_confirm" type="password" required minlength="6" maxlength="120" autocomplete="new-password">
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn btn--ghost" style="width:100%">Register</button>
        </div>
      </form>

      <p class="login-meta"><a href="<?= e(BASE_URL . '/customer/index.php') ?>">Customer home</a> · <a href="<?= e(BASE_URL . '/index.php') ?>">Portals</a></p>
    </div>
  </div>
  <script src="<?= e(asset('js/validate.js')) ?>" defer></script>
</body>
</html>
