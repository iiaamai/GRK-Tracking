-- System Enhancements Compliance (Sample Data Only)
-- Run after `data/enhancements/schemas.sql`.
-- Selects the app database (must match config/config.php DB_NAME).

CREATE DATABASE IF NOT EXISTS `express_logistics`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `express_logistics`;

SET FOREIGN_KEY_CHECKS = 0;

-- Admin demo
INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`)
VALUES (1, 'admin', 'admin@express.test', 'admin123', 'System Admin')
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name);

-- Customers demo
INSERT INTO `customers` (`id`, `username`, `email`, `password`, `name`, `mobile`)
VALUES
  (1, 'acme_corp', 'orders@acme.test', 'demo123', 'Acme Trading', '+63 917 000 0001'),
  (2, 'metro_retail', 'logistics@metro.test', 'demo123', 'Metro Retail', '+63 918 000 0002')
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name),
  mobile = VALUES(mobile);

-- Keep users aligned (customers are users)
INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`)
SELECT `id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`
FROM `customers`
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  email = VALUES(email),
  username = VALUES(username),
  password = VALUES(password),
  mobile = VALUES(mobile);

-- Drivers demo (includes +5 additional demo drivers)
INSERT INTO `drivers` (`id`, `username`, `email`, `password`, `name`, `mobile`, `vehicle_type`, `plate`, `capacity_kg`)
VALUES
  (1, 'driver_juan', 'juan@fleet.test', 'demo123', 'Juan Dela Cruz', '+63 919 111 1111', '6-wheeler (Isuzu / Fuso)', 'ABC-1234', 8000),
  (2, 'driver_maria', 'maria@fleet.test', 'demo123', 'Maria Santos', '+63 920 222 2222', 'L300 van', 'XYZ-5678', 1500),
  (3, 'driver_demo_01', 'demo01@fleet.test', 'demo123', 'Demo Driver 01', '+63 921 000 0001', '6-wheeler (Isuzu / Fuso)', 'DEMO-1001', 8000),
  (4, 'driver_demo_02', 'demo02@fleet.test', 'demo123', 'Demo Driver 02', '+63 921 000 0002', '6-wheeler (Isuzu / Fuso)', 'DEMO-1002', 8000),
  (5, 'driver_demo_03', 'demo03@fleet.test', 'demo123', 'Demo Driver 03', '+63 921 000 0003', '6-wheeler (Isuzu / Fuso)', 'DEMO-1003', 8000),
  (6, 'driver_demo_04', 'demo04@fleet.test', 'demo123', 'Demo Driver 04', '+63 921 000 0004', '6-wheeler (Isuzu / Fuso)', 'DEMO-1004', 8000),
  (7, 'driver_demo_05', 'demo05@fleet.test', 'demo123', 'Demo Driver 05', '+63 921 000 0005', '6-wheeler (Isuzu / Fuso)', 'DEMO-1005', 8000)
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name),
  mobile = VALUES(mobile),
  vehicle_type = VALUES(vehicle_type),
  plate = VALUES(plate),
  capacity_kg = VALUES(capacity_kg);

-- Settings demo defaults
INSERT INTO `settings` (`setting_key`, `setting_value`)
VALUES
  ('company_name', 'Express Urban Logistics'),
  ('support_email', 'support@express.test'),
  ('default_region', 'NCR + Calabarzon'),
  ('booking_seq', '3'),
  ('fleet_seq', '6')
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value);

-- Vehicles demo inventory (plate_number matches driver plates for "assignment")
INSERT INTO `vehicles` (`plate_number`, `label`, `type`, `capacity_kg`, `status`)
VALUES
  ('ABC-1234', 'Juan Dela Cruz unit', '6-wheeler', 8000, 'available'),
  ('XYZ-5678', 'Maria Santos unit', 'L300', 1500, 'available'),
  ('DEMO-1001', 'Demo Driver 01 unit', '6-wheeler', 8000, 'available'),
  ('DEMO-1002', 'Demo Driver 02 unit', '6-wheeler', 8000, 'available'),
  ('DEMO-1003', 'Demo Driver 03 unit', '6-wheeler', 8000, 'available'),
  ('DEMO-1004', 'Demo Driver 04 unit', '6-wheeler', 8000, 'available'),
  ('DEMO-1005', 'Demo Driver 05 unit', '6-wheeler', 8000, 'available'),
  ('FUS-1001', 'Isuzu F-Series 6-wheeler', '6-wheeler', 7500, 'available'),
  ('CAN-2002', 'Fuso Canter 4-wheeler', '4-wheeler', 3500, 'in_use'),
  ('REF-5005', 'Refrigerated 6-wheeler', '6-wheeler', 7000, 'available')
ON DUPLICATE KEY UPDATE
  label = VALUES(label),
  type = VALUES(type),
  capacity_kg = VALUES(capacity_kg),
  status = VALUES(status);

-- ---------------------------------------------------------------------------
-- Sample delivery history (2024): December peak, April low
-- ---------------------------------------------------------------------------
-- Notes:
-- - Use `status='completed'` to count as delivery history.
-- - `vehicle_id` kept NULL to avoid hard-coding auto-increment IDs.
-- - `is_locked/accepted_at` populated to reflect "accepted then completed".

INSERT INTO `bookings` (
  `booking_number`, `customer_id`, `user_id`,
  `booking_datetime`, `posting_date`, `vehicle_type`, `pickup`, `dropoff`,
  `cargo_desc`, `additional_requirements`, `status`, `driver_id`, `vehicle_id`,
  `is_locked`, `accepted_at`, `payout`, `payment_receipt_reference`, `driver_completion_status`
) VALUES
  ('EXP-2024-0401', 1, 1, '2024-04-03 09:00:00', '2024-04-02 08:00:00', '6-wheeler (Isuzu / Fuso)', 'QC Warehouse', 'Makati', 'Palletized goods', '', 'completed', 1, NULL, TRUE, '2024-04-02 10:00:00', 14500.00, NULL, 'clear'),
  ('EXP-2024-0402', 2, 2, '2024-04-18 14:00:00', '2024-04-17 09:00:00', 'L300 van', 'Parañaque', 'BGC', 'Cartons', '', 'completed', 2, NULL, TRUE, '2024-04-17 11:00:00', 4500.00, NULL, 'unclear'),
  ('EXP-2024-0101', 1, 1, '2024-01-12 08:30:00', '2024-01-11 08:00:00', '4-wheeler truck', 'Valenzuela', 'Ortigas', 'Mixed cargo', '', 'completed', 3, NULL, TRUE, '2024-01-11 10:00:00', 9200.00, NULL, 'unclear'),
  ('EXP-2024-1201', 1, 1, '2024-12-01 08:00:00', '2024-11-30 09:00:00', '6-wheeler (Isuzu / Fuso)', 'QC', 'Makati', 'Holiday pallets', '', 'completed', 1, NULL, TRUE, '2024-11-30 11:00:00', 14500.00, NULL, 'clear'),
  ('EXP-2024-1202', 2, 2, '2024-12-02 09:00:00', '2024-12-01 09:00:00', '4-wheeler truck', 'Pasay', 'Makati', 'Retail cartons', '', 'completed', 3, NULL, TRUE, '2024-12-01 11:00:00', 9200.00, NULL, 'unclear'),
  ('EXP-2024-1203', 1, 1, '2024-12-03 10:00:00', '2024-12-02 08:00:00', 'L300 van', 'Makati', 'Manila', 'Small loads', '', 'completed', 2, NULL, TRUE, '2024-12-02 10:00:00', 4500.00, '1234567890123', 'clear'),
  ('EXP-2024-1204', 2, 2, '2024-12-04 11:00:00', '2024-12-03 09:00:00', '6-wheeler (Isuzu / Fuso)', 'Parañaque', 'QC', 'Bulk goods', '', 'completed', 4, NULL, TRUE, '2024-12-03 11:00:00', 14500.00, NULL, 'unclear'),
  ('EXP-2024-1212', 2, 2, '2024-12-12 15:30:00', '2024-12-11 09:00:00', 'Reefer / specialized', 'Pasig', 'Makati', 'Cold cargo', '', 'completed', 6, NULL, TRUE, '2024-12-11 11:00:00', 18500.00, NULL, 'unclear');

SET FOREIGN_KEY_CHECKS = 1;

