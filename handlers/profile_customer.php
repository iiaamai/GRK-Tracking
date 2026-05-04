<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_CUSTOMER);

$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/dashboard.php?section=profile');
}

csrf_require_post();

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));

if ($name === '' || $email === '' || $mobile === '') {
    flash_set('error', 'Name, email, and mobile are required.');
    redirect(BASE_URL . '/customer/dashboard.php?section=profile');
}

repo_update_customer_profile((int) ($u['id'] ?? 0), $name, $email, $mobile);

$_SESSION['user']['name'] = $name;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['mobile'] = $mobile;

flash_set('success', 'Profile updated.');
redirect(BASE_URL . '/customer/dashboard.php?section=profile');
