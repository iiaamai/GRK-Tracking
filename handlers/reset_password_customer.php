<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/forgot_password.php');
}

csrf_require_post();

$token = trim((string) ($_POST['token'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$password2 = (string) ($_POST['password_confirm'] ?? '');

if ($token === '' || !pw_reset_validate('customer', $token)) {
    flash_set('error', 'This reset link is invalid or has expired. Please request a new one.');
    redirect(BASE_URL . '/customer/forgot_password.php');
}

if (strlen($password) < 6) {
    flash_set('error', 'Password must be at least 6 characters.');
    redirect(BASE_URL . '/customer/reset_password.php?token=' . rawurlencode($token));
}

if ($password !== $password2) {
    flash_set('error', 'Passwords do not match.');
    redirect(BASE_URL . '/customer/reset_password.php?token=' . rawurlencode($token));
}

$data = pw_reset_get('customer');
$userId = (int) ($data['user_id'] ?? 0);
if ($userId <= 0) {
    flash_set('error', 'This reset link is invalid or has expired. Please request a new one.');
    redirect(BASE_URL . '/customer/forgot_password.php');
}

repo_update_customer_password($userId, $password);
pw_reset_clear('customer');

flash_set('success', 'Your password has been updated. You can sign in now.');
redirect(BASE_URL . '/customer/login.php');
