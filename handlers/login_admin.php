<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/login.php');
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$user = auth_login('admin', $username, $password);
if (!$user) {
    flash_set('error', 'Invalid credentials.');
    redirect(BASE_URL . '/admin/login.php');
}

$_SESSION['user'] = [
    'role' => 'admin',
    'id' => $user['id'],
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
];

flash_set('success', 'Signed in as administrator.');
redirect(BASE_URL . '/admin/dashboard.php');
