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
    $booking = repo_find_booking_by_number($bn);
    if ($booking === null) {
        flash_set('error', 'Booking not found.');
        redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
    }
    if (($booking['status'] ?? '') !== 'completed') {
        flash_set('error', 'Payment receipt can only be recorded for completed bookings.');
        redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
    }
    $ref = trim((string) ($_POST['payment_receipt_reference'] ?? ''));
    if (!booking_payment_receipt_reference_required_valid($ref)) {
        flash_set('error', 'Payment receipt reference is required and must be exactly 13 digits.');
        redirect(BASE_URL . '/admin/dashboard.php?section=bookings');
    }
    repo_update_booking($bn, static function (array $b) use ($ref) {
        $b['payment_receipt_reference'] = $ref;
        $b['driver_completion_status'] = 'clear';

        return $b;
    });
    flash_set('success', 'Payment receipt saved; driver completion marked clear.');
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
