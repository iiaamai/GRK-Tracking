<?php
declare(strict_types=1);

function clearance_uploads_dir(): string
{
    return APP_ROOT . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'clearances';
}

function clearance_ensure_uploads_dir(): bool
{
    $dir = clearance_uploads_dir();
    if (is_dir($dir)) {
        return true;
    }

    return @mkdir($dir, 0755, true);
}

/**
 * Store clearance confirmation images under /uploads/clearances/.
 * Returns a project-relative web path like "uploads/clearances/driver_1_2026-04-27_clearance_abc.jpg".
 *
 * @param array{name?:string,tmp_name?:string,type?:string,size?:int,error?:int} $file
 */
function clearance_store_uploaded_image(array $file, int $driverId, string $dateYmd): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return null;
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > 5 * 1024 * 1024) {
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
    $safeDate = preg_replace('/[^0-9-]+/', '', $dateYmd) ?: 'date';
    $token = bin2hex(random_bytes(8));
    $filename = 'driver_' . $driverId . '_' . $safeDate . '_clearance_' . $token . '.' . $ext;

    if (!clearance_ensure_uploads_dir()) {
        return null;
    }

    $dest = clearance_uploads_dir() . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmp, $dest)) {
        return null;
    }

    return 'uploads/clearances/' . $filename;
}

