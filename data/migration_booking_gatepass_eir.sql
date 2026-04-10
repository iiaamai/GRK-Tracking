-- Run once on existing `express_logistics` DB (after pulling new code):
-- mysql -u root -p express_logistics < data/migration_booking_gatepass_eir.sql

ALTER TABLE `bookings`
  ADD COLUMN `gatepass_image` VARCHAR(512) NULL AFTER `payout`,
  ADD COLUMN `eir_image` VARCHAR(512) NULL AFTER `gatepass_image`;
