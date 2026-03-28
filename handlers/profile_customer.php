<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('customer');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/dashboard.php?section=profile');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));

if ($name === '' || $email === '' || $mobile === '') {
    flash_set('error', 'Name, email, and mobile are required.');
    redirect(BASE_URL . '/customer/dashboard.php?section=profile');
}

$_SESSION['user']['name'] = $name;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['mobile'] = $mobile;

// TODO: UPDATE customers SET ... WHERE id = ?

flash_set('success', 'Profile updated.');
redirect(BASE_URL . '/customer/dashboard.php?section=profile');
