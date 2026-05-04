<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/login.php');
}

csrf_require_post();

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$user = auth_login(AUTH_ROLE_ADMIN, $username, $password);
if (!$user) {
    flash_set('error', 'Invalid credentials.');
    redirect(BASE_URL . '/admin/login.php');
}

auth_session_regenerate();
csrf_rotate();

$_SESSION['user'] = [
    'role' => AUTH_ROLE_ADMIN,
    'id' => $user['id'],
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
];

flash_set('success', 'Signed in as administrator.');
redirect(BASE_URL . '/admin/dashboard.php');
