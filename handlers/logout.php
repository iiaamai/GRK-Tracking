<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$portal = $_GET['portal'] ?? 'customer';
auth_logout();

$urls = [
    'customer' => BASE_URL . '/customer/login.php',
    'driver' => BASE_URL . '/driver/login.php',
    'admin' => BASE_URL . '/admin/login.php',
];
redirect($urls[$portal] ?? BASE_URL . '/index.php');
