<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_CUSTOMER);
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/customer/dashboard.php?section=booking');
}

csrf_require_post();

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$mobile = trim((string) ($_POST['mobile'] ?? ''));
$vehicle_type = trim((string) ($_POST['vehicle_type'] ?? ''));
$pickup = trim((string) ($_POST['pickup'] ?? ''));
$dropoff = trim((string) ($_POST['dropoff'] ?? ''));
$cargo_desc = trim((string) ($_POST['cargo_desc'] ?? ''));
$additional = trim((string) ($_POST['additional_requirements'] ?? ''));
$booking_dt = trim((string) ($_POST['booking_datetime'] ?? ''));

// Normalize `datetime-local` values so MySQL TIMESTAMP accepts them.
$booking_dt = str_replace('T', ' ', $booking_dt);
if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $booking_dt) === 1) {
    $booking_dt .= ':00';
}

if ($name === '' || $email === '' || $mobile === '' || $vehicle_type === '' || $pickup === '' || $dropoff === '' || $booking_dt === '') {
    flash_set('error', 'Please fill all required fields.');
    redirect(BASE_URL . '/customer/dashboard.php?section=booking');
}

$payout = booking_payout_for_vehicle_type($vehicle_type);
if ($payout === null) {
    flash_set('error', 'Please choose a valid vehicle type.');
    redirect(BASE_URL . '/customer/dashboard.php?section=booking');
}

$available = repo_available_vehicles_count_for_booking_vehicle_type($vehicle_type);
if ($available <= 0) {
    flash_set('error', 'No available vehicles for the selected vehicle type right now. Please choose a different type or try again later.');
    redirect(BASE_URL . '/customer/dashboard.php?section=booking');
}

$now = new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
$row = [
    'booking_number' => repo_next_booking_number(),
    'customer_id' => (int) $u['id'],
    'user_id' => (int) $u['id'],
    'username' => (string) $u['username'],
    'name' => $name,
    'email' => $email,
    'mobile' => $mobile,
    'booking_datetime' => $booking_dt,
    'posting_date' => $now->format('Y-m-d'),
    'vehicle_type' => $vehicle_type,
    'pickup' => $pickup,
    'dropoff' => $dropoff,
    'cargo_desc' => $cargo_desc,
    'additional_requirements' => $additional,
    'status' => 'pending',
    'driver_id' => null,
    'vehicle_id' => null,
    'is_locked' => false,
    'accepted_at' => null,
    'payout' => $payout,
    'gatepass_image' => null,
];

repo_add_booking($row);
flash_set(
    'success',
    'Booking created: ' . $row['booking_number'] . '. Drivers can accept the job right away.'
);
redirect(BASE_URL . '/customer/dashboard.php?section=bookings');
