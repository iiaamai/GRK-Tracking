<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if (auth_user() && (auth_user()['role'] ?? '') === AUTH_ROLE_DRIVER) {
    redirect(BASE_URL . '/driver/dashboard.php');
}

$token = trim((string) ($_GET['token'] ?? ''));
if ($token === '' || !pw_reset_validate('driver', $token)) {
    flash_set('error', 'This reset link is invalid or has expired. Please request a new one.');
    redirect(BASE_URL . '/driver/forgot_password.php');
}

$err = flash_get('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set new password — Driver | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_login() ?></style>
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <h1>Set new password</h1>
      <p class="sub">Choose a new password for your driver account.</p>
      <?php if ($err): ?>
        <div class="flash flash--err"><?= e($err) ?></div>
      <?php endif; ?>
      <form class="js-validate" method="post" action="../handlers/reset_password_driver.php" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <div class="form-row">
          <label for="password">New password</label>
          <input id="password" name="password" type="password" required minlength="6" maxlength="120" autocomplete="new-password">
        </div>
        <div class="form-row">
          <label for="password_confirm">Confirm password</label>
          <input id="password_confirm" name="password_confirm" type="password" required minlength="6" maxlength="120" autocomplete="new-password">
        </div>
        <div class="actions">
          <button type="submit" class="btn btn--primary">Update password</button>
        </div>
      </form>
      <p class="login-meta"><a href="./login.php">Back to sign in</a></p>
    </div>
  </div>
  <script src="<?= e(asset('js/validate.js')) ?>" defer></script>
</body>
</html>
