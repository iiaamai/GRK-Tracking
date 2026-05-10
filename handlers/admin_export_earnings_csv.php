<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';

auth_require_role(AUTH_ROLE_ADMIN, ['forbidden_wrong_role' => true]);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Method Not Allowed');
}

csrf_require_post();

$period = (string) ($_POST['period'] ?? 'daily');
$period = in_array($period, ['daily', 'weekly', 'monthly', 'yearly'], true) ? $period : 'daily';

$tz = new DateTimeZone('Asia/Manila');
$now = new DateTimeImmutable('now', $tz);

$date = trim((string) ($_POST['date'] ?? ''));
$year = (int) ($_POST['year'] ?? 0);
if ($date !== '') {
    $date = preg_replace('/[^0-9-]+/', '', $date);
    try {
        $anchor = new DateTimeImmutable($date, $tz);
    } catch (Throwable $e) {
        $anchor = $now;
    }
} elseif ($year > 0) {
    $anchor = new DateTimeImmutable(sprintf('%04d-01-01', $year), $tz);
} else {
    $anchor = $now;
}

$start = $anchor;
$end = $anchor;
if ($period === 'weekly') {
    $start = $anchor->modify('monday this week');
    $end = $anchor->modify('sunday this week');
} elseif ($period === 'monthly') {
    $start = $anchor->modify('first day of this month');
    $end = $anchor->modify('last day of this month');
} elseif ($period === 'yearly') {
    $start = $anchor->setDate((int) $anchor->format('Y'), 1, 1);
    $end = $anchor->setDate((int) $anchor->format('Y'), 12, 31);
}

$filename = 'deliveries_' . $period . '_' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'wb');
if ($out === false) {
    exit;
}

fputcsv($out, [
    'booking_number',
    'booking_date',
    'customer_name',
    'driver_id',
    'vehicle_type',
    'pickup',
    'dropoff',
    'payout',
]);

$rows = repo_completed_deliveries_for_range($start->format('Y-m-d'), $end->format('Y-m-d'));
$total = 0.0;
foreach ($rows as $r) {
    $payout = isset($r['payout']) && $r['payout'] !== null ? (float) $r['payout'] : 0.0;
    $total += $payout;
    fputcsv($out, [
        (string) ($r['booking_number'] ?? ''),
        (string) substr((string) ($r['booking_datetime'] ?? ''), 0, 10),
        (string) ($r['customer_name'] ?? ''),
        (string) ((int) ($r['driver_id'] ?? 0)),
        (string) ($r['vehicle_type'] ?? ''),
        (string) ($r['pickup'] ?? ''),
        (string) ($r['dropoff'] ?? ''),
        number_format($payout, 2, '.', ''),
    ]);
}

fputcsv($out, []);
fputcsv($out, ['TOTAL', '', '', '', '', '', '', number_format($total, 2, '.', '')]);
fclose($out);
exit;
