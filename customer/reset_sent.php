<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if (auth_user() && (auth_user()['role'] ?? '') === AUTH_ROLE_CUSTOMER) {
    redirect(BASE_URL . '/customer/dashboard.php');
}

$reset = pw_reset_get('customer');
$hasLink = $reset !== null && ($reset['expires_at'] ?? 0) >= time();
$token = $hasLink ? (string) ($reset['token'] ?? '') : '';
$resetUrl = $hasLink && $token !== '' ? pw_reset_url('customer', $token) : '';
$username = $hasLink ? (string) ($reset['username'] ?? '') : '';
$email = $hasLink ? (string) ($reset['email'] ?? '') : '';
$expiresAt = $hasLink ? (int) ($reset['expires_at'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset link — Customer | GRK Trucking Services</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
  <style><?= embedded_styles_global() ?><?= embedded_styles_login() ?></style>
</head>
<body>
  <div class="login-page">
    <div class="login-card">
      <h1>Check your email (demo)</h1>
      <p class="sub">If an account matches what you entered, reset instructions were generated.</p>
      <?php if ($hasLink): ?>
        <div class="reset-sent-demo">
          <p style="margin:0 0 0.5rem;font-size:0.9rem">
            In production, a link would be sent to <strong><?= e($email) ?></strong> for account <strong><?= e($username) ?></strong>.
          </p>
          <p style="margin:0 0 0.75rem;font-size:0.85rem;color:var(--muted)">
            Open the link in the <strong>same browser</strong> where you requested the reset. Expires <?= e(date('M j, Y g:i A', $expiresAt)) ?>.
          </p>
          <p style="margin:0;font-size:0.85rem;word-break:break-all">
            <a href="<?= e($resetUrl) ?>"><?= e($resetUrl) ?></a>
          </p>
        </div>
        <div class="actions" style="margin-top:1rem">
          <a href="<?= e($resetUrl) ?>" class="btn btn--primary" style="display:block;text-align:center;text-decoration:none">Reset my password</a>
        </div>
      <?php else: ?>
        <p style="margin:0;font-size:0.9rem;color:var(--muted)">
          If you do not see a reset link, check the username or email and try again.
        </p>
      <?php endif; ?>
      <p class="login-meta"><a href="./login.php">Back to sign in</a> · <a href="./forgot_password.php">Try again</a></p>
    </div>
  </div>
  <script src="<?= e(asset('js/validate.js')) ?>" defer></script>
</body>
</html>
