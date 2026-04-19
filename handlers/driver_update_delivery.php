<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('driver');
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$bn = trim((string) ($_POST['booking_number'] ?? ''));
$action = trim((string) ($_POST['action'] ?? ''));

if ($bn === '' || !in_array($action, ['in_transit', 'completed'], true)) {
    flash_set('error', 'Invalid request.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

$current = repo_find_booking_by_number($bn);
if ($current === null || (int) ($current['driver_id'] ?? 0) !== (int) $u['id']) {
    flash_set('error', 'Invalid booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

if ($action === 'completed' && trim((string) ($current['eir_image'] ?? '')) === '') {
    flash_set('error', 'Upload the Equipment Interchange Receipt (EIR) before completing this delivery.');
    redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
}

repo_update_booking($bn, static function (array $b) use ($u, $action) {
    if ((int) ($b['driver_id'] ?? 0) !== (int) $u['id']) {
        return $b;
    }
    if ($action === 'in_transit') {
        $b['status'] = 'in_transit';
    }
    if ($action === 'completed') {
        $b['status'] = 'completed';
    }
    return $b;
});

flash_set('success', $action === 'completed' ? 'Delivery marked completed.' : 'Status updated to in transit.');
redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
