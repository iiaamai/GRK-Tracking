<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/login.php');
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$user = auth_login('customer', $username, $password);
if (!$user) {
    flash_set('error', 'Invalid username or password.');
    redirect(BASE_URL . '/customer/login.php');
}

$_SESSION['user'] = [
    'role' => 'customer',
    'id' => $user['id'],
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
    'mobile' => $user['mobile'],
];

flash_set('success', 'Welcome back, ' . $user['name'] . '.');
redirect(BASE_URL . '/customer/dashboard.php');
