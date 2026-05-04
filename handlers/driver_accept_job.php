<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_DRIVER);
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

csrf_require_post();

$active = repo_driver_deliveries((int) ($u['id'] ?? 0));
if (count($active) > 0) {
    flash_set('error', 'You have an unfinished delivery. Finish it before accepting a new job.');
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

// Block acceptance if driver's vehicle is in_use or maintenance.
$plateSelf = trim((string) ($u['plate'] ?? ''));
if ($plateSelf !== '') {
    $stmt = db()->prepare('SELECT status FROM vehicles WHERE plate_number = ? LIMIT 1');
    $stmt->execute([$plateSelf]);
    $st = (string) ($stmt->fetchColumn() ?: '');
    if ($st !== '' && $st !== 'available') {
        flash_set('error', 'Your vehicle is currently ' . $st . '. You cannot accept jobs right now.');
        redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
    }
}

$bn = trim((string) ($_POST['booking_number'] ?? ''));
if ($bn === '') {
    flash_set('error', 'Missing booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

$current = repo_find_booking_by_number($bn);
$gp = (string) ($current['gatepass_image'] ?? '');
if (
    !$current
    || ($current['status'] ?? '') !== 'pending'
    || $gp === ''
) {
    flash_set('error', 'That job is no longer available.');
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

// Auto-link vehicle by matching driver's plate to vehicles.plate_number.
$vehicleId = null;
try {
    $plate = trim((string) ($u['plate'] ?? ''));
    if ($plate !== '') {
        $stmt = db()->prepare("SELECT id, status FROM vehicles WHERE plate_number = ? LIMIT 1");
        $stmt->execute([$plate]);
        $veh = $stmt->fetch();
        if (is_array($veh) && ($veh['status'] ?? '') !== 'maintenance') {
            $vehicleId = (int) ($veh['id'] ?? 0);
        }
    }
} catch (Throwable $e) {
    // Non-fatal: booking can still be accepted without a linked vehicle.
    $vehicleId = null;
}

repo_update_booking($bn, static function (array $b) use ($u, $vehicleId) {
    if (!empty($b['is_locked']) || ($b['driver_id'] ?? null) !== null) {
        return $b;
    }

    $b['status'] = 'accepted';
    $b['driver_id'] = (int) $u['id'];
    if ($vehicleId !== null && $vehicleId > 0) {
        $b['vehicle_id'] = $vehicleId;
    }
    $b['is_locked'] = true;
    $b['accepted_at'] = (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

    return $b;
});

// Auto-update vehicle status to in_use (don't override maintenance).
if ($vehicleId !== null && $vehicleId > 0) {
    try {
        $upd = db()->prepare("UPDATE vehicles SET status = 'in_use' WHERE id = ? AND status <> 'maintenance'");
        $upd->execute([$vehicleId]);
    } catch (Throwable $e) {
        // Non-fatal.
    }
}

flash_set('success', 'Job accepted. Check My Deliveries.');
redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
