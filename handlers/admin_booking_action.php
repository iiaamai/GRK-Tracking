<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_ADMIN);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

csrf_require_post();

$bn = trim((string) ($_POST['booking_number'] ?? ''));
$act = trim((string) ($_POST['action'] ?? ''));

if ($bn === '') {
    flash_set('error', 'Missing booking.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

if ($act === 'delete') {
    $all = array_values(array_filter(repo_bookings(), static fn ($b) => ($b['booking_number'] ?? '') !== $bn));
    repo_save_bookings($all);
    flash_set('success', 'Booking removed.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

if ($act === 'update_meta') {
    $ref = trim((string) ($_POST['payment_receipt_reference'] ?? ''));
    if (!booking_payment_receipt_reference_valid($ref)) {
        flash_set('error', 'Payment receipt must be empty or exactly 13 digits.');
        redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
    }
    $dc = trim((string) ($_POST['driver_completion_status'] ?? 'unclear'));
    if (!in_array($dc, ['clear', 'unclear'], true)) {
        $dc = 'unclear';
    }
    repo_update_booking($bn, static function (array $b) use ($ref, $dc) {
        $b['payment_receipt_reference'] = $ref === '' ? null : $ref;
        $b['driver_completion_status'] = $dc;

        return $b;
    });
    flash_set('success', 'Payment receipt and driver completion saved.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

$allowed = ['pending', 'accepted', 'in_transit', 'completed', 'cancelled'];
if (!in_array($act, $allowed, true)) {
    flash_set('error', 'Invalid status.');
    redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
}

repo_update_booking($bn, static function (array $b) use ($act) {
    $b['status'] = $act;

    return $b;
});

flash_set('success', 'Booking updated.');
redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
