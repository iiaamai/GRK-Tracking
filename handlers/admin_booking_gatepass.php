<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/booking_uploads.php';

auth_require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

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
$path = booking_store_uploaded_image(is_array($file) ? $file : [], $bn, 'gatepass');
if ($path === null) {
    flash_set('error', 'Please upload a valid image (JPG, PNG, WebP, or GIF), max 5MB.');
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
    if (($b['status'] ?? '') === 'pending') {
        $b['status'] = 'ready_for_assignment';
    }

    return $b;
});

flash_set('success', 'Gate pass uploaded. Booking is now ready for driver assignment.');
redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
