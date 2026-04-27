<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('admin');

$section = $_GET['section'] ?? 'overview';
$allowed = ['overview', 'earnings_reports', 'bookings', 'drivers', 'customers', 'fleet', 'settings'];
if (!in_array($section, $allowed, true)) {
    $section = 'overview';
}

$titles = [
    'overview' => 'Overview',
    'earnings_reports' => 'Earnings & Reports',
    'bookings' => 'All Bookings',
    'drivers' => 'Drivers',
    'customers' => 'Customers',
    'fleet' => 'Fleet Management',
    'settings' => 'Settings',
];
$pageTitle = $titles[$section];

require dirname(__DIR__) . '/includes/header_admin.php';
require __DIR__ . '/sections/' . $section . '.php';
require dirname(__DIR__) . '/includes/footer_admin.php';
