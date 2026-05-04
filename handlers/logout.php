<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Method Not Allowed');
}

csrf_require_post();

$portal = trim((string) ($_POST['portal'] ?? 'customer'));
auth_logout();
csrf_rotate();
auth_session_regenerate();

$urls = [
    'customer' => BASE_URL . '/customer/login.php',
    'driver' => BASE_URL . '/driver/login.php',
    'admin' => BASE_URL . '/admin/login.php',
];
redirect($urls[$portal] ?? BASE_URL . '/index.php');
