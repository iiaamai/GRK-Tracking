<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_require_role('driver');
$u = auth_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

$bn = trim((string) ($_POST['booking_number'] ?? ''));
if ($bn === '') {
    flash_set('error', 'Missing booking.');
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

$current = repo_find_booking_by_number($bn);
if (!$current || ($current['status'] ?? '') !== 'pending') {
    flash_set('error', 'That job is no longer available.');
    redirect(BASE_URL . '/driver/dashboard.php?section=jobs');
}

repo_update_booking($bn, static function (array $b) use ($u) {
    $b['status'] = 'assigned';
    $b['driver_id'] = (int) $u['id'];
    $b['payout'] = $b['payout'] ?? 5200.0;
    return $b;
});

flash_set('success', 'Job accepted. Check My Deliveries.');
redirect(BASE_URL . '/driver/dashboard.php?section=deliveries');
