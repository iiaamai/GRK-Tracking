<?php
declare(strict_types=1);

/**
 * Store booking document images (gate pass, EIR) under /uploads/bookings/.
 * Returns a project-relative web path like "uploads/bookings/EXP-2026-0001_gatepass_abc.jpg".
 */
const BOOKING_UPLOAD_MAX_BYTES = 15 * 1024 * 1024;

function booking_uploads_dir(): string
{
    return APP_ROOT . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'bookings';
}

function booking_ensure_uploads_dir(): bool
{
    $dir = booking_uploads_dir();
    if (is_dir($dir)) {
        return true;
    }

    return @mkdir($dir, 0755, true);
}

/**
 * @param array{name?:string,tmp_name?:string,type?:string,size?:int,error?:int} $file
 */
function booking_store_uploaded_image(array $file, string $bookingNumber, string $suffix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return null;
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > BOOKING_UPLOAD_MAX_BYTES) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    if ($mime === false || !isset($map[$mime])) {
        return null;
    }

    $ext = $map[$mime];
    $safeBn = preg_replace('/[^A-Za-z0-9_-]+/', '', $bookingNumber) ?: 'booking';
    $token = bin2hex(random_bytes(8));
    $filename = $safeBn . '_' . $suffix . '_' . $token . '.' . $ext;

    if (!booking_ensure_uploads_dir()) {
        return null;
    }

    $dest = booking_uploads_dir() . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmp, $dest)) {
        return null;
    }

    return 'uploads/bookings/' . $filename;
}
