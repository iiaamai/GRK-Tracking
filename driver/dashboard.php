<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_DRIVER);

$section = $_GET['section'] ?? 'overview';
$allowed = ['overview', 'jobs', 'deliveries', 'profile'];
if (!in_array($section, $allowed, true)) {
    $section = 'overview';
}

$titles = [
    'overview' => 'Overview',
    'jobs' => 'Available Jobs',
    'deliveries' => 'My Deliveries',
    'profile' => 'Profile',
];
$pageTitle = $titles[$section];

require dirname(__DIR__) . '/includes/header_driver.php';
require __DIR__ . '/sections/' . $section . '.php';
require dirname(__DIR__) . '/includes/footer_driver.php';
