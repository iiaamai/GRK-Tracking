<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if (auth_user() && (auth_user()['role'] ?? '') === 'admin') {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$err = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin login — Express Logistics</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset('css/global.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/login.css')) ?>">
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <h1>Administrator sign in</h1>
      <p class="sub">Restricted access for operations staff.</p>
      <?php if ($err): ?>
        <div class="flash flash--err"><?= e($err) ?></div>
      <?php endif; ?>
      <form class="js-validate" method="post" action="<?= e(BASE_URL . '/handlers/login_admin.php') ?>" novalidate>
        <div class="form-row">
          <label for="username">Username</label>
          <input id="username" name="username" required autocomplete="username" maxlength="80">
        </div>
        <div class="form-row">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required autocomplete="current-password" maxlength="120">
        </div>
        <div class="actions">
          <button type="submit" class="btn btn--primary">Sign in</button>
        </div>
      </form>
      <div class="demo-hint">
        Demo: <code>admin</code> / <code>admin123</code>
      </div>
      <p class="login-meta"><a href="<?= e(BASE_URL . '/admin/index.php') ?>">Admin home</a> · <a href="<?= e(BASE_URL . '/index.php') ?>">Portals</a></p>
    </div>
  </div>
  <script src="<?= e(asset('js/validate.js')) ?>" defer></script>
</body>
</html>
