<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/booking_uploads.php';

auth_require_role('driver');
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$bn = trim((string) ($_POST['booking_number'] ?? ''));
if ($bn === '') {
    flash_set('error', 'Missing booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$current = repo_find_booking_by_number($bn);
if ($current === null || (int) ($current['driver_id'] ?? 0) !== (int) $u['id']) {
    flash_set('error', 'Invalid booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$st = (string) ($current['status'] ?? '');
if (!in_array($st, ['assigned', 'in_transit'], true)) {
    flash_set('error', 'EIR can only be uploaded for active deliveries.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$file = $_FILES['eir'] ?? [];
$path = booking_store_uploaded_image(is_array($file) ? $file : [], $bn, 'eir');
if ($path === null) {
    flash_set('error', 'Please upload a valid EIR image (JPG, PNG, WebP, or GIF), max 5MB.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$old = $current['eir_image'] ?? null;
if ($old !== null && $old !== '' && str_starts_with($old, 'uploads/bookings/')) {
    $full = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $old);
    if (is_file($full)) {
        @unlink($full);
    }
}

repo_update_booking($bn, static function (array $b) use ($path) {
    $b['eir_image'] = $path;

    return $b;
});

flash_set('success', 'Equipment Interchange Receipt (EIR) uploaded.');
redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
