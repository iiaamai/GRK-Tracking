-- Express Urban Logistics — MySQL schema aligned with data/mock_data.php and data/repository.php
-- Run in phpMyAdmin or: mysql -u root -p < data/schema.sql
-- Database name matches config/config.php (DB_NAME)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS `express_logistics`;
CREATE DATABASE `express_logistics`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `express_logistics`;

-- ---------------------------------------------------------------------------
-- Users (customers & drivers — same fields as mock_users(); admins separate)
-- ---------------------------------------------------------------------------

CREATE TABLE `customers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(64) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `mobile` VARCHAR(40) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_username` (`username`),
  KEY `idx_customers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `drivers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(64) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `mobile` VARCHAR(40) NOT NULL,
  `vehicle_type` VARCHAR(120) NOT NULL,
  `plate` VARCHAR(20) NOT NULL,
  `capacity_kg` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_drivers_username` (`username`),
  KEY `idx_drivers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(64) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Fleet (mock_fleet_seed)
-- Status values: available, in_use, maintenance (admin UI)
-- ---------------------------------------------------------------------------

CREATE TABLE `fleet` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(120) NOT NULL,
  `type` VARCHAR(64) NOT NULL,
  `plate` VARCHAR(20) NOT NULL,
  `capacity_kg` INT UNSIGNED NOT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  KEY `idx_fleet_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Bookings (mock_bookings_seed + handlers)
-- Status values used in app: pending, assigned, in_transit, completed, cancelled
-- ---------------------------------------------------------------------------

CREATE TABLE `bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_number` VARCHAR(32) NOT NULL,
  `customer_id` INT UNSIGNED NOT NULL,
  `username` VARCHAR(64) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `mobile` VARCHAR(40) NOT NULL,
  `booking_datetime` TIMESTAMP NOT NULL,
  `posting_date` TIMESTAMP NOT NULL,
  `vehicle_type` VARCHAR(64) NOT NULL,
  `pickup` VARCHAR(255) NOT NULL,
  `dropoff` VARCHAR(255) NOT NULL,
  `cargo_desc` TEXT NOT NULL,
  `additional_requirements` TEXT NOT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
  `driver_id` INT UNSIGNED NULL,
  `payout` DECIMAL(12, 2) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bookings_number` (`booking_number`),
  KEY `idx_bookings_customer` (`customer_id`),
  KEY `idx_bookings_driver` (`driver_id`),
  KEY `idx_bookings_status` (`status`),
  CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_bookings_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- App settings + sequence counters (session repo: settings + booking_seq + fleet_seq)
-- ---------------------------------------------------------------------------

CREATE TABLE `settings` (
  `setting_key` VARCHAR(64) NOT NULL,
  `setting_value` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- Seed data (matches mock_data.php)
-- ---------------------------------------------------------------------------

INSERT INTO `customers` (`id`, `username`, `email`, `password`, `name`, `mobile`) VALUES
  (1, 'acme_corp', 'orders@acme.test', 'demo123', 'Acme Trading', '+63 917 000 0001'),
  (2, 'metro_retail', 'logistics@metro.test', 'demo123', 'Metro Retail', '+63 918 000 0002');

INSERT INTO `drivers` (`id`, `username`, `email`, `password`, `name`, `mobile`, `vehicle_type`, `plate`, `capacity_kg`) VALUES
  (1, 'driver_juan', 'juan@fleet.test', 'demo123', 'Juan Dela Cruz', '+63 919 111 1111', '6-wheeler (Isuzu / Fuso)', 'ABC-1234', 8000),
  (2, 'driver_maria', 'maria@fleet.test', 'demo123', 'Maria Santos', '+63 920 222 2222', 'L300 van', 'XYZ-5678', 1500);

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`) VALUES
  (1, 'admin', 'admin@express.test', 'admin123', 'System Admin');

INSERT INTO `fleet` (`id`, `label`, `type`, `plate`, `capacity_kg`, `status`) VALUES
  (1, 'Isuzu F-Series 6-wheeler', '6-wheeler', 'FUS-1001', 7500, 'available'),
  (2, 'Fuso Canter 4-wheeler', '4-wheeler', 'CAN-2002', 3500, 'in_use'),
  (3, 'L300 Van', 'L300', 'L30-3003', 1200, 'available'),
  (4, 'Motorcycle courier', '2-wheeler', 'MC-4004', 50, 'maintenance'),
  (5, 'Refrigerated 6-wheeler', '6-wheeler', 'REF-5005', 7000, 'available');

INSERT INTO `bookings` (
  `booking_number`, `customer_id`, `username`, `name`, `email`, `mobile`,
  `booking_datetime`, `posting_date`, `vehicle_type`, `pickup`, `dropoff`,
  `cargo_desc`, `additional_requirements`, `status`, `driver_id`, `payout`
) VALUES
  (
    'EXP-2026-0001', 1, 'acme_corp', 'Acme Trading', 'orders@acme.test', '+63 917 000 0001',
    '2026-03-26 09:30:00', '2026-03-26', '6-wheeler',
    'Quezon City Warehouse', 'Makati CBD',
    'Palletized goods — 40 pallets', 'Liftgate required; morning slot only.',
    'assigned', 1, 5200.00
  ),
  (
    'EXP-2026-0002', 2, 'metro_retail', 'Metro Retail', 'logistics@metro.test', '+63 918 000 0002',
    '2026-03-27 14:00:00', '2026-03-27', 'L300',
    'Parañaque Hub', 'BGC',
    'Retail cartons — 200 boxes', '',
    'pending', NULL, NULL
  );

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
  ('company_name', 'Express Urban Logistics'),
  ('support_email', 'support@express.test'),
  ('default_region', 'NCR + Calabarzon'),
  ('booking_seq', '3'),
  ('fleet_seq', '6');

-- Reset AUTO_INCREMENT so new rows continue after seed IDs
ALTER TABLE `customers` AUTO_INCREMENT = 3;
ALTER TABLE `drivers` AUTO_INCREMENT = 3;
ALTER TABLE `admins` AUTO_INCREMENT = 2;
ALTER TABLE `fleet` AUTO_INCREMENT = 6;
ALTER TABLE `bookings` AUTO_INCREMENT = 3;
