<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/booking_uploads.php';

auth_require_role(AUTH_ROLE_ADMIN);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

csrf_require_post();

$bn = trim((string) ($_POST['booking_number'] ?? ''));
if ($bn === '') {
    flash_set('error', 'Missing booking.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

$current = repo_find_booking_by_number($bn);
if ($current === null) {
    flash_set('error', 'Booking not found.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

$file = $_FILES['gatepass'] ?? [];
$res = booking_store_uploaded_image_with_error(is_array($file) ? $file : [], $bn, 'gatepass');
$path = $res['path'];
if ($path === null) {
    $reason = (string) ($res['error'] ?? 'Upload failed.');
    flash_set('error', 'Gate pass upload failed: ' . $reason);
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

$old = $current['gatepass_image'] ?? null;
if ($old !== null && $old !== '' && str_starts_with($old, 'uploads/bookings/')) {
    $full = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $old);
    if (is_file($full)) {
        @unlink($full);
    }
}

repo_update_booking($bn, static function (array $b) use ($path) {
    $b['gatepass_image'] = $path;

    return $b;
});

flash_set('success', 'Gate pass uploaded. Booking is now available for driver acceptance.');
redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
