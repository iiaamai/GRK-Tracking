<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if (auth_user() && (auth_user()['role'] ?? '') === AUTH_ROLE_DRIVER) {
    redirect(BASE_URL . '/driver/dashboard.php');
}

$err = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot password — Driver | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_login() ?></style>
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <h1>Forgot password</h1>
      <p class="sub">Enter your username or email. We will generate a reset link (demo: shown on the next screen).</p>
      <?php if ($err): ?>
        <div class="flash flash--err"><?= e($err) ?></div>
      <?php endif; ?>
      <form class="js-validate" method="post" action="../handlers/forgot_password_driver.php" novalidate>
        <?= csrf_field() ?>
        <div class="form-row">
          <label for="identifier">Username or email</label>
          <input id="identifier" name="identifier" required autocomplete="username" maxlength="255" placeholder="driver_juan">
        </div>
        <div class="actions">
          <button type="submit" class="btn btn--primary">Send reset link</button>
        </div>
      </form>
      <p class="login-meta"><a href="./login.php">Back to sign in</a></p>
    </div>
  </div>
  <script src="<?= e(asset('js/validate.js')) ?>" defer></script>
</body>
</html>
