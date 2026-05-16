<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/forgot_password.php');
}

csrf_require_post();

$identifier = trim((string) ($_POST['identifier'] ?? ''));
if ($identifier === '') {
    flash_set('error', 'Please enter your username or email.');
    redirect(BASE_URL . '/driver/forgot_password.php');
}

$user = repo_find_driver_by_login($identifier);
if ($user !== null) {
    pw_reset_issue('driver', $user);
}

redirect(BASE_URL . '/driver/reset_sent.php');
