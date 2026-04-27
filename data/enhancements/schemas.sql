-- System Enhancements Compliance (Schemas / Migrations Only)
-- Run inside an existing database, e.g. `express_logistics`.
-- This file contains ONLY schema creation / alteration (no demo inserts).

SET FOREIGN_KEY_CHECKS = 0;
SET @db := DATABASE();

-- ---------------------------------------------------------------------------
-- Base legacy tables (create if missing)
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

-- Legacy fleet table (some installs)
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

-- Legacy bookings table shape so later ALTER/SELECT won't fail.
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
-- Enhancements: new canonical tables
-- ---------------------------------------------------------------------------

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

CREATE TABLE IF NOT EXISTS `eir` (
  `booking_id` INT UNSIGNED NOT NULL,
  `eir_file` VARCHAR(512) NOT NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- ---------------------------------------------------------------------------
-- Data backfills / alters (schema-level)
-- ---------------------------------------------------------------------------

-- Backfill users from customers (upsert)
INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`)
SELECT `id`, `name`, `email`, `username`, `password`, `mobile`, `created_at`
FROM `customers`
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  email = VALUES(email),
  username = VALUES(username),
  password = VALUES(password),
  mobile = VALUES(mobile);

-- Backfill vehicles from legacy fleet table (upsert via unique plate)
INSERT INTO `vehicles` (`plate_number`, `label`, `type`, `capacity_kg`, `status`)
SELECT
  `plate`,
  `label`,
  `type`,
  `capacity_kg`,
  CASE
    WHEN `status` IN ('available', 'in_use', 'maintenance') THEN CAST(`status` AS CHAR)
    ELSE 'available'
  END
FROM `fleet`
ON DUPLICATE KEY UPDATE
  label = VALUES(label),
  type = VALUES(type),
  capacity_kg = VALUES(capacity_kg),
  status = VALUES(status);

-- Ensure `bookings.eir_image` exists so EIR backfill won't fail
ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `eir_image` VARCHAR(512) NULL;

-- Add new booking columns
ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `user_id` INT UNSIGNED NULL AFTER `customer_id`,
  ADD COLUMN IF NOT EXISTS `vehicle_id` INT UNSIGNED NULL AFTER `driver_id`,
  ADD COLUMN IF NOT EXISTS `is_locked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `vehicle_id`,
  ADD COLUMN IF NOT EXISTS `accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_locked`;

UPDATE `bookings` SET `user_id` = `customer_id` WHERE `user_id` IS NULL;

-- Normalize statuses first, then tighten to ENUM
UPDATE `bookings` SET `status` = 'pending' WHERE `status` IN ('ready_for_assignment');
UPDATE `bookings` SET `status` = 'accepted' WHERE `status` IN ('assigned');
UPDATE `bookings` SET `status` = 'pending'
WHERE `status` NOT IN ('pending','accepted','in_transit','completed','cancelled') OR `status` IS NULL OR `status` = '';

ALTER TABLE `bookings`
  MODIFY COLUMN `status` ENUM('pending','accepted','in_transit','completed','cancelled') NOT NULL DEFAULT 'pending';

-- Driver clearance columns
ALTER TABLE `drivers`
  ADD COLUMN IF NOT EXISTS `status` ENUM('cleared','uncleared') NOT NULL DEFAULT 'uncleared',
  ADD COLUMN IF NOT EXISTS `last_cleared_at` TIMESTAMP NULL DEFAULT NULL;

-- Move eir_image into eir table
INSERT INTO `eir` (`booking_id`, `eir_file`, `uploaded_at`)
SELECT b.id, b.eir_image, CURRENT_TIMESTAMP
FROM bookings b
WHERE b.eir_image IS NOT NULL AND b.eir_image <> ''
ON DUPLICATE KEY UPDATE
  eir_file = VALUES(eir_file),
  uploaded_at = VALUES(uploaded_at);

ALTER TABLE `bookings` DROP COLUMN IF EXISTS `eir_image`;

-- ---------------------------------------------------------------------------
-- Foreign keys (conditionally added; safe to re-run)
-- ---------------------------------------------------------------------------

-- Ensure values won't violate FKs before adding them.
UPDATE `bookings` b
LEFT JOIN `users` u ON u.id = b.user_id
SET b.user_id = NULL
WHERE b.user_id IS NOT NULL AND u.id IS NULL;

UPDATE `bookings` b
LEFT JOIN `vehicles` v ON v.id = b.vehicle_id
SET b.vehicle_id = NULL
WHERE b.vehicle_id IS NOT NULL AND v.id IS NULL;

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

