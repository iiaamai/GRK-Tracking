<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/clearance_uploads.php';

auth_require_role(AUTH_ROLE_ADMIN);
$admin = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

csrf_require_post();

$driverId = (int) ($_POST['driver_id'] ?? 0);
if ($driverId <= 0) {
    flash_set('error', 'Invalid driver.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

$file = $_FILES['confirmation'] ?? [];
$today = (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d');
$path = clearance_store_uploaded_image(is_array($file) ? $file : [], $driverId, $today);
if ($path === null) {
    flash_set('error', 'Please upload a valid confirmation image (JPG, PNG, WebP, or GIF), max 5MB.');
    redirect(BASE_URL . '/admin/dashboard.php?section=drivers');
}

$pdo = db();
$pdo->beginTransaction();
try {
    $upd = $pdo->prepare("UPDATE drivers SET status = 'cleared', last_cleared_at = CURRENT_TIMESTAMP WHERE id = ?");
    $upd->execute([$driverId]);

    $ins = $pdo->prepare(
        'INSERT INTO driver_clearances (driver_id, cleared_by_admin_id, date, confirmation_file)
         VALUES (?,?,?,?)'
    );
    $ins->execute([$driverId, (int) ($admin['id'] ?? 0), $today, $path]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

flash_set('success', 'Driver cleared for today.');
redirect(BASE_URL . '/admin/dashboard.php?section=drivers');

