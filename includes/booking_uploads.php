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
    $res = booking_store_uploaded_image_with_error($file, $bookingNumber, $suffix);
    return $res['path'];
}

/**
 * Store an uploaded image and return both path + failure reason.
 *
 * @param array{name?:string,tmp_name?:string,type?:string,size?:int,error?:int} $file
 * @return array{path:?string,error:?string}
 */
function booking_store_uploaded_image_with_error(array $file, string $bookingNumber, string $suffix): array
{
    $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err !== UPLOAD_ERR_OK) {
        $map = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload_max_filesize.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds the form MAX_FILE_SIZE limit.',
            UPLOAD_ERR_PARTIAL => 'File upload was incomplete.',
            UPLOAD_ERR_NO_FILE => 'No file was received.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temp folder is missing (upload_tmp_dir).',
            UPLOAD_ERR_CANT_WRITE => 'Server failed to write the uploaded file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        ];
        $msg = $map[$err] ?? ('Upload failed with PHP error code: ' . $err . '.');
        return ['path' => null, 'error' => $msg];
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '') {
        return ['path' => null, 'error' => 'Upload temp file is missing.'];
    }
    if (!is_uploaded_file($tmp)) {
        $extra = is_file($tmp) ? ' Temp file exists but is_uploaded_file() failed (possible server restriction).' : '';
        return ['path' => null, 'error' => 'Upload temp file is not a valid uploaded file.' . $extra];
    }

    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0) {
        return ['path' => null, 'error' => 'Uploaded file size is 0 bytes.'];
    }
    if ($size > BOOKING_UPLOAD_MAX_BYTES) {
        return ['path' => null, 'error' => 'File is too large. Max allowed is 15MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $mimeMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    if ($mime === false || !isset($mimeMap[$mime])) {
        return ['path' => null, 'error' => 'Invalid image type. Allowed: JPG, PNG, WebP, GIF.'];
    }

    $ext = $mimeMap[$mime];
    $safeBn = preg_replace('/[^A-Za-z0-9_-]+/', '', $bookingNumber) ?: 'booking';
    $token = bin2hex(random_bytes(8));
    $filename = $safeBn . '_' . $suffix . '_' . $token . '.' . $ext;

    if (!booking_ensure_uploads_dir()) {
        return ['path' => null, 'error' => 'Server failed to create upload folder: ' . booking_uploads_dir()];
    }

    $dest = booking_uploads_dir() . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmp, $dest)) {
        return ['path' => null, 'error' => 'Server failed to save the uploaded file. Check folder permissions: ' . booking_uploads_dir()];
    }

    return ['path' => 'uploads/bookings/' . $filename, 'error' => null];
}
