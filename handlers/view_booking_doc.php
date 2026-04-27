<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$u = auth_user();
if (!$u) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

$bn = trim((string) ($_GET['booking_number'] ?? ''));
$doc = trim((string) ($_GET['doc'] ?? ''));

if ($bn === '' || !in_array($doc, ['gatepass', 'eir'], true)) {
    header('HTTP/1.1 400 Bad Request');
    exit('Bad request');
}

$b = repo_find_booking_by_number($bn);
if ($b === null) {
    header('HTTP/1.1 404 Not Found');
    exit('Not found');
}

$role = (string) ($u['role'] ?? '');
$status = (string) ($b['status'] ?? '');
$path = '';
if ($doc === 'gatepass') {
    $path = (string) ($b['gatepass_image'] ?? '');
} else {
    $stmt = db()->prepare('SELECT eir_file FROM eir WHERE booking_id = ? LIMIT 1');
    $stmt->execute([(int) ($b['id'] ?? 0)]);
    $path = (string) ($stmt->fetchColumn() ?: '');
}

if ($path === '') {
    header('HTTP/1.1 404 Not Found');
    exit('Not found');
}

// Role-based access checks.
if ($role === 'admin') {
    // Admin can view both.
} elseif ($role === 'driver') {
    if ($doc === 'eir') {
        if ((int) ($b['driver_id'] ?? 0) !== (int) ($u['id'] ?? 0)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Forbidden');
        }
        if (!in_array($status, ['in_transit', 'completed'], true)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Forbidden');
        }
    } else {
        // Drivers may preview gate pass before accepting (pending) or during active runs.
        if (!in_array($status, ['pending', 'accepted', 'in_transit', 'completed'], true)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Forbidden');
        }
    }
} elseif ($role === 'customer') {
    if ($doc !== 'eir') {
        header('HTTP/1.1 403 Forbidden');
        exit('Forbidden');
    }
    if ((int) ($b['customer_id'] ?? 0) !== (int) ($u['id'] ?? 0)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Forbidden');
    }
    // Customer can view EIR for in-transit (and completed) shipments.
    if (!in_array($status, ['in_transit', 'completed'], true)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Forbidden');
    }
} else {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

// Only allow files stored under uploads/bookings/
$path = str_replace('\\', '/', $path);
if (!str_starts_with($path, 'uploads/bookings/')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

$full = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
if (!is_file($full)) {
    header('HTTP/1.1 404 Not Found');
    exit('Not found');
}

$ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
$mime = match ($ext) {
    'jpg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'webp' => 'image/webp',
    'gif' => 'image/gif',
    default => 'application/octet-stream',
};

header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . basename($full) . '"');
header('Content-Length: ' . (string) filesize($full));

readfile($full);
