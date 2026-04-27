-- System Enhancements Compliance Migration
-- Run against an existing `express_logistics` database.
-- Safe-ish migration: adds new tables/columns and preserves existing data where possible.

-- IMPORTANT:
-- This migration must be executed *inside* your existing database.
-- If you're using phpMyAdmin, select `express_logistics` first.
-- If you're using CLI, run:  USE `express_logistics`;

SET FOREIGN_KEY_CHECKS = 0;

-- Allow conditional DDL blocks (MariaDB/MySQL).
SET @db := DATABASE();

-- ---------------------------------------------------------------------------
-- Base tables (create if missing) so migration won't error on fresh DBs
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(64) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `drivers` (
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

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(64) NOT NULL,
  `setting_value` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Legacy bookings table shape (older versions) so later ALTER/SELECT work.
CREATE TABLE IF NOT EXISTS `bookings` (
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
  `gatepass_image` VARCHAR(512) NULL,
  `eir_image` VARCHAR(512) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bookings_number` (`booking_number`),
  KEY `idx_bookings_customer` (`customer_id`),
  KEY `idx_bookings_driver` (`driver_id`),
  KEY `idx_bookings_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Demo accounts (Admin / Customer / Driver)
-- ---------------------------------------------------------------------------

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`)
VALUES (1, 'admin', 'admin@express.test', 'admin123', 'System Admin')
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name);

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

INSERT INTO `settings` (`setting_key`, `setting_value`)
VALUES
  ('company_name', 'Express Urban Logistics'),
  ('support_email', 'support@express.test'),
  ('default_region', 'NCR + Calabarzon'),
  ('booking_seq', '3'),
  ('fleet_seq', '6')
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value);

-- 1) USERS: create users table + backfill from customers
-- Ensure legacy `customers` exists (older installs already have this).
CREATE TABLE IF NOT EXISTS `customers` (
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

INSERT INTO `customers` (`id`, `username`, `email`, `password`, `name`, `mobile`)
VALUES
  (1, 'acme_corp', 'orders@acme.test', 'demo123', 'Acme Trading', '+63 917 000 0001'),
  (2, 'metro_retail', 'logistics@metro.test', 'demo123', 'Metro Retail', '+63 918 000 0002')
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  password = VALUES(password),
  name = VALUES(name),
  mobile = VALUES(mobile);

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(64) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `mobile` VARCHAR(40) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`)
SELECT `id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`
FROM `customers`
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  email = VALUES(email),
  username = VALUES(username),
  password = VALUES(password),
  mobile = VALUES(mobile);

-- 2) VEHICLES: if fleet exists, migrate it to vehicles and keep a compatibility view `fleet`
-- Ensure legacy `fleet` exists (older installs already have this).
CREATE TABLE IF NOT EXISTS `fleet` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(120) NOT NULL,
  `type` VARCHAR(64) NOT NULL,
  `plate` VARCHAR(20) NOT NULL,
  `capacity_kg` INT UNSIGNED NOT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  KEY `idx_fleet_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plate_number` VARCHAR(20) NOT NULL,
  `label` VARCHAR(120) NOT NULL,
  `type` VARCHAR(64) NOT NULL,
  `capacity_kg` INT UNSIGNED NOT NULL,
  `status` ENUM('available','in_use','maintenance') NOT NULL DEFAULT 'available',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vehicles_plate` (`plate_number`),
  KEY `idx_vehicles_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Try to backfill from fleet if it exists (plate -> plate_number)
INSERT IGNORE INTO `vehicles` (`id`, `plate_number`, `label`, `type`, `capacity_kg`, `status`)
SELECT
  `id`,
  `plate`,
  `label`,
  `type`,
  `capacity_kg`,
  CASE
    WHEN `status` IN ('available', 'in_use', 'maintenance') THEN CAST(`status` AS CHAR)
    ELSE 'available'
  END
FROM `fleet`;

-- Demo fleet inventory mapped to demo drivers (plate_number matches drivers.plate)
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

-- 3) BOOKINGS: add new columns; keep existing booking data
ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `user_id` INT UNSIGNED NULL AFTER `customer_id`,
  ADD COLUMN IF NOT EXISTS `vehicle_id` INT UNSIGNED NULL AFTER `driver_id`,
  ADD COLUMN IF NOT EXISTS `is_locked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `vehicle_id`,
  ADD COLUMN IF NOT EXISTS `accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_locked`;

UPDATE `bookings` SET `user_id` = `customer_id` WHERE `user_id` IS NULL;

-- Map legacy statuses to new enum values (best-effort)
UPDATE `bookings` SET `status` = 'pending' WHERE `status` IN ('ready_for_assignment');
UPDATE `bookings` SET `status` = 'accepted' WHERE `status` IN ('assigned');
UPDATE `bookings` SET `status` = 'pending' WHERE `status` NOT IN ('pending','accepted','in_transit','completed','cancelled') OR `status` IS NULL OR `status` = '';

ALTER TABLE `bookings`
  MODIFY COLUMN `status` ENUM('pending','accepted','in_transit','completed','cancelled') NOT NULL DEFAULT 'pending';

-- 4) DRIVERS: add clearance fields
ALTER TABLE `drivers`
  ADD COLUMN IF NOT EXISTS `status` ENUM('cleared','uncleared') NOT NULL DEFAULT 'uncleared',
  ADD COLUMN IF NOT EXISTS `last_cleared_at` TIMESTAMP NULL DEFAULT NULL;

-- 5) EIR: move bookings.eir_image into eir table if present
CREATE TABLE IF NOT EXISTS `eir` (
  `booking_id` INT UNSIGNED NOT NULL,
  `eir_file` VARCHAR(512) NOT NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Some installs may have already dropped `bookings.eir_image`.
-- Add it back (nullable) so the migration queries won't fail.
ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `eir_image` VARCHAR(512) NULL;

INSERT INTO `eir` (`booking_id`, `eir_file`, `uploaded_at`)
SELECT b.id, b.eir_image, CURRENT_TIMESTAMP
FROM bookings b
WHERE b.eir_image IS NOT NULL AND b.eir_image <> ''
ON DUPLICATE KEY UPDATE
  eir_file = VALUES(eir_file),
  uploaded_at = VALUES(uploaded_at);

ALTER TABLE `bookings` DROP COLUMN IF EXISTS `eir_image`;

-- 6) REPORTS + CLEARANCES
CREATE TABLE IF NOT EXISTS `earnings_reports` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('daily','weekly','monthly','yearly') NOT NULL,
  `total_earnings` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total_bookings` INT NOT NULL DEFAULT 0,
  `total_maintenance_vehicles` INT NOT NULL DEFAULT 0,
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_range_start` DATE NOT NULL,
  `date_range_end` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_earnings_reports_type` (`type`),
  KEY `idx_earnings_reports_generated_at` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `driver_clearances` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `driver_id` INT UNSIGNED NOT NULL,
  `cleared_by_admin_id` INT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `confirmation_file` VARCHAR(512) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_driver_clearances_driver` (`driver_id`),
  KEY `idx_driver_clearances_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign keys (add last, after data moves)
-- Ensure values won't violate FKs before adding them.
UPDATE `bookings` b
LEFT JOIN `users` u ON u.id = b.user_id
SET b.user_id = NULL
WHERE b.user_id IS NOT NULL AND u.id IS NULL;

UPDATE `bookings` b
LEFT JOIN `vehicles` v ON v.id = b.vehicle_id
SET b.vehicle_id = NULL
WHERE b.vehicle_id IS NOT NULL AND v.id IS NULL;

-- Add foreign keys only if they don't already exist (prevents "Duplicate key name" / re-run errors).
SET @has_fk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db
    AND TABLE_NAME = 'bookings'
    AND CONSTRAINT_NAME = 'fk_bookings_user'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @has_fk = 0,
  'ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db
    AND TABLE_NAME = 'bookings'
    AND CONSTRAINT_NAME = 'fk_bookings_vehicle'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @has_fk = 0,
  'ALTER TABLE `bookings` ADD CONSTRAINT `fk_bookings_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON UPDATE CASCADE ON DELETE SET NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db
    AND TABLE_NAME = 'eir'
    AND CONSTRAINT_NAME = 'fk_eir_booking'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @has_fk = 0,
  'ALTER TABLE `eir` ADD CONSTRAINT `fk_eir_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON UPDATE CASCADE ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db
    AND TABLE_NAME = 'driver_clearances'
    AND CONSTRAINT_NAME = 'fk_driver_clearances_driver'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @has_fk = 0,
  'ALTER TABLE `driver_clearances` ADD CONSTRAINT `fk_driver_clearances_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON UPDATE CASCADE ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_fk := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db
    AND TABLE_NAME = 'driver_clearances'
    AND CONSTRAINT_NAME = 'fk_driver_clearances_admin'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @has_fk = 0,
  'ALTER TABLE `driver_clearances` ADD CONSTRAINT `fk_driver_clearances_admin` FOREIGN KEY (`cleared_by_admin_id`) REFERENCES `admins` (`id`) ON UPDATE CASCADE ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

