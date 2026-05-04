<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/login.php');
}

csrf_require_post();

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$user = auth_login(AUTH_ROLE_CUSTOMER, $username, $password);
if (!$user) {
    flash_set('error', 'Invalid username or password.');
    redirect(BASE_URL . '/customer/login.php');
}

auth_session_regenerate();
csrf_rotate();

$_SESSION['user'] = [
    'role' => AUTH_ROLE_CUSTOMER,
    'id' => $user['id'],
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
    'mobile' => $user['mobile'],
];

flash_set('success', 'Welcome back, ' . $user['name'] . '.');
redirect(BASE_URL . '/customer/dashboard.php');
