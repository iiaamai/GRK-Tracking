<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/login.php');
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$user = auth_login('driver', $username, $password);
if (!$user) {
    flash_set('error', 'Invalid username or password.');
    redirect(BASE_URL . '/driver/login.php');
}

$_SESSION['user'] = [
    'role' => 'driver',
    'id' => $user['id'],
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
    'mobile' => $user['mobile'],
    'vehicle_type' => $user['vehicle_type'] ?? '',
    'plate' => $user['plate'] ?? '',
    'capacity_kg' => $user['capacity_kg'] ?? 0,
];

flash_set('success', 'Welcome, ' . $user['name'] . '.');
redirect(BASE_URL . '/driver/dashboard.php');
