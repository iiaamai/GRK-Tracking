<?php
declare(strict_types=1);

/**
 * Payment receipt reference: exactly 13 digits when provided; empty string is allowed (legacy / nullable fields).
 */
function booking_payment_receipt_reference_valid(string $value): bool
{
    $value = trim($value);

    return $value === '' || preg_match('/^\d{13}$/', $value) === 1;
}

/**
 * Admin “Save receipt”: reference must be set and exactly 13 digits.
 */
function booking_payment_receipt_reference_required_valid(string $value): bool
{
    return preg_match('/^\d{13}$/', trim($value)) === 1;
}
