<?php
declare(strict_types=1);

/**
 * Payment receipt reference: exactly 13 digits when provided; empty string is allowed (cleared / not set).
 */
function booking_payment_receipt_reference_valid(string $value): bool
{
    $value = trim($value);

    return $value === '' || preg_match('/^\d{13}$/', $value) === 1;
}
