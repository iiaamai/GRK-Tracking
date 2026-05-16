-- Production / legacy DB patch: booking columns required by the app.
-- Run against express_logistics (see config/config.php DB_NAME).
-- Safe to re-run (IF NOT EXISTS). Full fresh install: use schemas.sql instead.

USE `express_logistics`;

ALTER TABLE `bookings`
  ADD COLUMN IF NOT EXISTS `user_id` INT UNSIGNED NULL AFTER `customer_id`,
  ADD COLUMN IF NOT EXISTS `vehicle_id` INT UNSIGNED NULL AFTER `driver_id`,
  ADD COLUMN IF NOT EXISTS `is_locked` BOOLEAN NOT NULL DEFAULT FALSE AFTER `vehicle_id`,
  ADD COLUMN IF NOT EXISTS `accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_locked`,
  ADD COLUMN IF NOT EXISTS `payment_receipt_reference` VARCHAR(13) NULL AFTER `payout`,
  ADD COLUMN IF NOT EXISTS `driver_completion_status` ENUM('clear','unclear') NOT NULL DEFAULT 'unclear' AFTER `payment_receipt_reference`,
  ADD COLUMN IF NOT EXISTS `cancel_message` TEXT NULL AFTER `driver_completion_status`;

UPDATE `bookings` SET `user_id` = `customer_id` WHERE `user_id` IS NULL;
