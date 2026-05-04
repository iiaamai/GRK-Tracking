<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/login.php');
}

csrf_require_post();

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$password2 = (string) ($_POST['password_confirm'] ?? '');
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));
$vehicle_type = trim((string) ($_POST['vehicle_type'] ?? ''));
$plate = trim((string) ($_POST['plate'] ?? ''));
$capacity_kg = (int) ($_POST['capacity_kg'] ?? 0);

if ($username === '' || $password === '' || $name === '' || $email === '' || $mobile === '' || $vehicle_type === '' || $plate === '') {
    flash_set('error', 'Please fill in all required fields.');
    redirect(BASE_URL . '/driver/login.php');
}

if (strlen($username) < 3 || strlen($username) > 80) {
    flash_set('error', 'Username must be between 3 and 80 characters.');
    redirect(BASE_URL . '/driver/login.php');
}

if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
    flash_set('error', 'Username may only use letters, numbers, dot, underscore, and hyphen.');
    redirect(BASE_URL . '/driver/login.php');
}

if (strlen($password) < 6) {
    flash_set('error', 'Password must be at least 6 characters.');
    redirect(BASE_URL . '/driver/login.php');
}

if ($password !== $password2) {
    flash_set('error', 'Passwords do not match.');
    redirect(BASE_URL . '/driver/login.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('error', 'Please enter a valid email address.');
    redirect(BASE_URL . '/driver/login.php');
}

if (repo_driver_username_exists($username)) {
    flash_set('error', 'That username is already taken.');
    redirect(BASE_URL . '/driver/login.php');
}

repo_insert_driver($username, $email, $password, $name, $mobile, $vehicle_type, $plate, $capacity_kg);

flash_set('success', 'Driver account created. You can sign in now.');
redirect(BASE_URL . '/driver/login.php');
