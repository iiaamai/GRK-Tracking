<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_DRIVER);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/dashboard.php?section=profile');
}

csrf_require_post();

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));
$vehicle_type = trim((string) ($_POST['vehicle_type'] ?? ''));
$plate = trim((string) ($_POST['plate'] ?? ''));
$capacity = (int) ($_POST['capacity_kg'] ?? 0);

if ($name === '' || $email === '' || $mobile === '' || $vehicle_type === '' || $plate === '') {
    flash_set('error', 'Please complete all required fields.');
    redirect(BASE_URL . '/driver/dashboard.php?section=profile');
}

$u = auth_user();
repo_update_driver_profile(
    (int) ($u['id'] ?? 0),
    $name,
    $email,
    $mobile,
    $vehicle_type,
    $plate,
    $capacity
);

$_SESSION['user']['name'] = $name;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['mobile'] = $mobile;
$_SESSION['user']['vehicle_type'] = $vehicle_type;
$_SESSION['user']['plate'] = $plate;
$_SESSION['user']['capacity_kg'] = $capacity;

flash_set('success', 'Profile and vehicle details saved.');
redirect(BASE_URL . '/driver/dashboard.php?section=profile');
