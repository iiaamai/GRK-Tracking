<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_CUSTOMER);

$section = $_GET['section'] ?? 'overview';
$allowed = ['overview', 'booking', 'track', 'bookings', 'profile'];
if (!in_array($section, $allowed, true)) {
    $section = 'overview';
}

$titles = [
    'overview' => 'Overview',
    'booking' => 'Truck Booking',
    'track' => 'Track Shipment',
    'bookings' => 'My Bookings',
    'profile' => 'Profile',
];
$pageTitle = $titles[$section];

require dirname(__DIR__) . '/includes/header_customer.php';
require __DIR__ . '/sections/' . $section . '.php';
require dirname(__DIR__) . '/includes/footer_customer.php';
