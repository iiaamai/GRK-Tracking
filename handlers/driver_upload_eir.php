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
if ($st !== 'in_transit') {
    flash_set('error', 'EIR upload is only enabled when the booking is in transit.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$file = $_FILES['eir'] ?? [];
$path = booking_store_uploaded_image(is_array($file) ? $file : [], $bn, 'eir');
if ($path === null) {
    flash_set('error', 'Please upload a valid EIR image (JPG, PNG, WebP, or GIF), max 5MB.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$bid = (int) ($current['id'] ?? 0);
if ($bid <= 0) {
    flash_set('error', 'Invalid booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT eir_file FROM eir WHERE booking_id = ? LIMIT 1');
$stmt->execute([$bid]);
$old = (string) ($stmt->fetchColumn() ?: '');
if ($old !== '' && str_starts_with(str_replace('\\', '/', $old), 'uploads/bookings/')) {
    $full = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', '/', $old));
    if (is_file($full)) {
        @unlink($full);
    }
}

$ins = $pdo->prepare('INSERT INTO eir (booking_id, eir_file) VALUES (?, ?) ON DUPLICATE KEY UPDATE eir_file = VALUES(eir_file), uploaded_at = CURRENT_TIMESTAMP');
$ins->execute([$bid, $path]);

flash_set('success', 'Equipment Interchange Receipt (EIR) uploaded.');
redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
