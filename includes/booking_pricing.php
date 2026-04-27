<?php
declare(strict_types=1);

/**
 * Base booking payout in Philippine Pesos (PHP), keyed by the exact vehicle_type option label.
 */
function booking_vehicle_payouts_map(): array
{
    return [
        '6-wheeler (Isuzu / Fuso)' => 14500.00,
        '4-wheeler truck' => 9200.00,
        'L300 van' => 4500.00,
        'Reefer / specialized' => 18500.00,
    ];
}

function booking_payout_for_vehicle_type(string $vehicleType): ?float
{
    $vehicleType = trim($vehicleType);
    $map = booking_vehicle_payouts_map();

    return array_key_exists($vehicleType, $map) ? (float) $map[$vehicleType] : null;
}
