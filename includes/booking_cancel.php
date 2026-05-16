<?php
declare(strict_types=1);

/**
 * Preset cancellation reasons for admin booking cancel modal.
 *
 * @return array<string, string> reason key => display label
 */
function booking_cancel_reasons(): array
{
    return [
        'customer_request' => 'Customer requested cancellation',
        'no_vehicle' => 'No available vehicle / capacity',
        'payment_issue' => 'Payment not received',
        'incorrect_details' => 'Incorrect booking details',
        'operational' => 'Operational or weather delay',
        'other' => 'Other',
    ];
}

function booking_cancel_message_from_post(string $reasonKey, string $otherText): ?string
{
    $reasonKey = trim($reasonKey);
    $reasons = booking_cancel_reasons();

    if ($reasonKey === 'other') {
        $text = trim($otherText);

        return $text !== '' ? $text : null;
    }

    return $reasons[$reasonKey] ?? null;
}
