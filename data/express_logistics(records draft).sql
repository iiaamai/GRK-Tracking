-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 04:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS `express_logistics`;
CREATE DATABASE `express_logistics`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `express_logistics`;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `express_logistics`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `name`, `created_at`) VALUES
(1, 'admin', 'admin@express.test', 'admin123', 'System Admin', '2026-04-14 12:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_number` varchar(32) NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(40) NOT NULL,
  `booking_datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `posting_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vehicle_type` varchar(64) NOT NULL,
  `pickup` varchar(255) NOT NULL,
  `dropoff` varchar(255) NOT NULL,
  `cargo_desc` text NOT NULL,
  `additional_requirements` text NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `driver_id` int(10) UNSIGNED DEFAULT NULL,
  `payout` decimal(12,2) DEFAULT NULL,
  `gatepass_image` varchar(512) DEFAULT NULL,
  `eir_image` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_number`, `customer_id`, `username`, `name`, `email`, `mobile`, `booking_datetime`, `posting_date`, `vehicle_type`, `pickup`, `dropoff`, `cargo_desc`, `additional_requirements`, `status`, `driver_id`, `payout`, `gatepass_image`, `eir_image`) VALUES
(1, 'EXP-2026-0001', 1, 'acme_corp', 'Acme Trading', 'orders@acme.test', '+63 917 000 0001', '2026-03-26 01:30:00', '2026-03-25 16:00:00', '6-wheeler', 'Quezon City Warehouse', 'Makati CBD', 'Palletized goods — 40 pallets', 'Liftgate required; morning slot only.', 'assigned', 1, 5200.00, NULL, NULL),
(2, 'EXP-2026-0002', 2, 'metro_retail', 'Metro Retail', 'logistics@metro.test', '+63 918 000 0002', '2026-03-27 06:00:00', '2026-03-26 16:00:00', 'L300', 'Parañaque Hub', 'BGC', 'Retail cartons — 200 boxes', '', 'ready_for_assignment', NULL, NULL, 'uploads/bookings/demo-gatepass.png', NULL),
(6, 'EXP-2026-0004', 2, 'metro_retail', 'Metro Retail', 'logistics@metro.test', '+63 918 679 0002', '2026-04-20 09:00:00', '2026-04-16 16:00:00', '6-wheeler (Isuzu / Fuso)', '1335-C Camba Ext. Tondo Manila', 'Shaw Blvd, Ortigas Center, 1552 Metro Manila', '', '', 'pending', NULL, NULL, NULL, NULL),
(7, 'EXP-2026-0005', 2, 'metro_retail', 'Metro Retail', 'logistics@metro.test', '+63 918 1045 6232', '2026-04-24 01:30:00', '2026-04-16 16:00:00', '4-wheeler truck', '1319 Asuncion Ext., Tondo, Manila', 'Natividad Almeda-lopez Corner A. Villegas And San Marcelino Streets, Manila, 1018, PH', '', '', 'pending', NULL, NULL, NULL, NULL),
(8, 'EXP-2026-0006', 2, 'metro_retail', 'Metro Retail', 'logistics@metro.test', '+63 918 000 0002', '2026-04-30 06:00:00', '2026-04-16 16:00:00', 'Reefer / specialized', 'Manila Bulletin Building, Muralla corner Recoletos Sts., Intramuros, Manila', 'Seaside Boulevard, 1300 J.W. Diokno Blvd, Pasay City, Philippines', '', '', 'pending', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(120) NOT NULL,
  `mobile` varchar(40) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `email`, `password`, `name`, `mobile`, `created_at`) VALUES
(1, 'acme_corp', 'orders@acme.test', 'demo123', 'Acme Trading', '+63 917 000 0001', '2026-04-14 12:16:04'),
(2, 'metro_retail', 'logistics@metro.test', 'demo123', 'Metro Retail', '+63 918 000 0002', '2026-04-14 12:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(120) NOT NULL,
  `mobile` varchar(40) NOT NULL,
  `vehicle_type` varchar(120) NOT NULL,
  `plate` varchar(20) NOT NULL,
  `capacity_kg` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `username`, `email`, `password`, `name`, `mobile`, `vehicle_type`, `plate`, `capacity_kg`, `created_at`) VALUES
(1, 'driver_juan', 'juan@fleet.test', 'demo123', 'Juan Dela Cruz', '+63 919 111 1111', '6-wheeler (Isuzu / Fuso)', 'ABC-1234', 8000, '2026-04-14 12:16:04'),
(2, 'driver_maria', 'maria@fleet.test', 'demo123', 'Maria Santos', '+63 920 222 2222', 'L300 van', 'XYZ-5678', 1500, '2026-04-14 12:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `fleet`
--

CREATE TABLE `fleet` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(120) NOT NULL,
  `type` varchar(64) NOT NULL,
  `plate` varchar(20) NOT NULL,
  `capacity_kg` int(10) UNSIGNED NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fleet`
--

INSERT INTO `fleet` (`id`, `label`, `type`, `plate`, `capacity_kg`, `status`) VALUES
(1, 'Isuzu F-Series 6-wheeler', '6-wheeler', 'FUS-1001', 7500, 'available'),
(2, 'Fuso Canter 4-wheeler', '4-wheeler', 'CAN-2002', 3500, 'in_use'),
(3, 'L300 Van', 'L300', 'L30-3003', 1200, 'available'),
(4, 'Motorcycle courier', '2-wheeler', 'MC-4004', 50, 'maintenance'),
(5, 'Refrigerated 6-wheeler', '6-wheeler', 'REF-5005', 7000, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(64) NOT NULL,
  `setting_value` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('booking_seq', '7'),
('company_name', 'Express Urban Logistics'),
('default_region', 'NCR + Calabarzon'),
('fleet_seq', '6'),
('support_email', 'support@express.test');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admins_username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bookings_number` (`booking_number`),
  ADD KEY `idx_bookings_customer` (`customer_id`),
  ADD KEY `idx_bookings_driver` (`driver_id`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customers_username` (`username`),
  ADD KEY `idx_customers_email` (`email`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_drivers_username` (`username`),
  ADD KEY `idx_drivers_email` (`email`);

--
-- Indexes for table `fleet`
--
ALTER TABLE `fleet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fleet_status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fleet`
--
ALTER TABLE `fleet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
