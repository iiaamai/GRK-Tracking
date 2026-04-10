<?php
declare(strict_types=1);

/**
 * Seed data — replace with MySQL queries via repository layer.
 */
function mock_users(): array
{
    return [
        'customers' => [
            ['id' => 1, 'username' => 'acme_corp', 'email' => 'orders@acme.test', 'password' => 'demo123', 'name' => 'Acme Trading', 'mobile' => '+63 917 000 0001'],
            ['id' => 2, 'username' => 'metro_retail', 'email' => 'logistics@metro.test', 'password' => 'demo123', 'name' => 'Metro Retail', 'mobile' => '+63 918 000 0002'],
        ],
        'drivers' => [
            ['id' => 1, 'username' => 'driver_juan', 'email' => 'juan@fleet.test', 'password' => 'demo123', 'name' => 'Juan Dela Cruz', 'mobile' => '+63 919 111 1111', 'vehicle_type' => '6-wheeler (Isuzu / Fuso)', 'plate' => 'ABC-1234', 'capacity_kg' => 8000],
            ['id' => 2, 'username' => 'driver_maria', 'email' => 'maria@fleet.test', 'password' => 'demo123', 'name' => 'Maria Santos', 'mobile' => '+63 920 222 2222', 'vehicle_type' => 'L300 van', 'plate' => 'XYZ-5678', 'capacity_kg' => 1500],
        ],
        'admin' => [
            ['id' => 1, 'username' => 'admin', 'email' => 'admin@express.test', 'password' => 'admin123', 'name' => 'System Admin'],
        ],
    ];
}

function mock_fleet_seed(): array
{
    return [
        ['id' => 1, 'label' => 'Isuzu F-Series 6-wheeler', 'type' => '6-wheeler', 'plate' => 'FUS-1001', 'capacity_kg' => 7500, 'status' => 'available'],
        ['id' => 2, 'label' => 'Fuso Canter 4-wheeler', 'type' => '4-wheeler', 'plate' => 'CAN-2002', 'capacity_kg' => 3500, 'status' => 'in_use'],
        ['id' => 3, 'label' => 'L300 Van', 'type' => 'L300', 'plate' => 'L30-3003', 'capacity_kg' => 1200, 'status' => 'available'],
        ['id' => 4, 'label' => 'Motorcycle courier', 'type' => '2-wheeler', 'plate' => 'MC-4004', 'capacity_kg' => 50, 'status' => 'maintenance'],
        ['id' => 5, 'label' => 'Refrigerated 6-wheeler', 'type' => '6-wheeler', 'plate' => 'REF-5005', 'capacity_kg' => 7000, 'status' => 'available'],
    ];
}

function mock_bookings_seed(): array
{
    return [
        [
            'booking_number' => 'EXP-2026-0001',
            'customer_id' => 1,
            'username' => 'acme_corp',
            'name' => 'Acme Trading',
            'email' => 'orders@acme.test',
            'mobile' => '+63 917 000 0001',
            'booking_datetime' => '2026-03-26 09:30:00',
            'posting_date' => '2026-03-26',
            'vehicle_type' => '6-wheeler',
            'pickup' => 'Quezon City Warehouse',
            'dropoff' => 'Makati CBD',
            'cargo_desc' => 'Palletized goods — 40 pallets',
            'additional_requirements' => 'Liftgate required; morning slot only.',
            'status' => 'assigned',
            'driver_id' => 1,
            'payout' => 5200.0,
            'gatepass_image' => null,
            'eir_image' => null,
        ],
        [
            'booking_number' => 'EXP-2026-0002',
            'customer_id' => 2,
            'username' => 'metro_retail',
            'name' => 'Metro Retail',
            'email' => 'logistics@metro.test',
            'mobile' => '+63 918 000 0002',
            'booking_datetime' => '2026-03-27 14:00:00',
            'posting_date' => '2026-03-27',
            'vehicle_type' => 'L300',
            'pickup' => 'Parañaque Hub',
            'dropoff' => 'BGC',
            'cargo_desc' => 'Retail cartons — 200 boxes',
            'additional_requirements' => '',
            'status' => 'ready_for_assignment',
            'driver_id' => null,
            'payout' => null,
            'gatepass_image' => 'uploads/bookings/demo-gatepass.png',
            'eir_image' => null,
        ],
    ];
}
