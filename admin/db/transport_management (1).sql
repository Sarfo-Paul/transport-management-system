-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: May 07, 2025 at 07:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transport_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ID of the user making the booking',
  `route_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `booking_date` datetime NOT NULL,
  `travel_date` date NOT NULL,
  `pickup_point` varchar(100) NOT NULL,
  `dropoff_point` varchar(100) NOT NULL,
  `passenger_count` int(11) NOT NULL DEFAULT 1,
  `purpose` varchar(100) DEFAULT NULL COMMENT 'Purpose of trip (academic, administrative, etc.)',
  `status` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `route_id`, `schedule_id`, `booking_date`, `travel_date`, `pickup_point`, `dropoff_point`, `passenger_count`, `purpose`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 4, '2025-04-24 19:20:31', '2025-04-24', 'sd', 'dsd', 1, 'scv', 'Pending', '', '2025-04-24 19:20:31', '2025-04-24 19:20:31'),
(3, 2, 1, 1, '2025-04-30 07:21:35', '2025-04-30', 'legon annex A', 'legon annex B', 1, '', 'Pending', 'visit', '2025-04-30 07:21:35', '2025-04-30 07:21:35'),
(4, 3, 1, 1, '2025-04-30 07:23:31', '2025-04-30', 'akuafo hall', 'sarbah hall', 1, 'study purpose', 'Pending', '', '2025-04-30 07:23:31', '2025-04-30 07:23:31'),
(5, 4, 1, 1, '2025-04-30 18:23:30', '2025-04-30', 'commonwealth hall', 'Limann hall', 4, 'Going to my hostel', 'Pending', '', '2025-04-30 18:23:30', '2025-04-30 18:23:30'),
(8, 6, 9, NULL, '2025-05-02 19:44:04', '2025-05-02', 'sxdf', 'asdf', 1, 'qwd', 'Pending', '', '2025-05-02 19:44:04', '2025-05-02 19:44:04'),
(9, 6, 9, NULL, '2025-05-02 19:44:35', '2025-05-02', '2', '5', 1, 'asds', 'Pending', '', '2025-05-02 19:44:35', '2025-05-02 19:44:35');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `license_number` varchar(20) NOT NULL,
  `license_expiry` date NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hire_date` date NOT NULL,
  `status` enum('Active','On Leave','Suspended','Terminated') DEFAULT 'Active',
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `first_name`, `last_name`, `license_number`, `license_expiry`, `contact_number`, `email`, `address`, `hire_date`, `status`, `photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Kwame', 'Amponsah', 'DL-001-2018', '2025-06-30', '+233244556677', 'kwame.amponsah@ug.edu.gh', '12 Legon Avenue, Accra', '2018-03-15', 'Active', NULL, '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(3, 'Yaw', 'Boateng', 'DL-003-2020', '2026-03-15', '+233244112233', 'yaw.boateng@ug.edu.gh', '8 Academic Close, Legon', '2020-02-10', 'Active', 'uploads/driver_1746036288.jpg', '2025-04-14 20:20:20', '2025-04-30 18:04:48'),
(5, 'Kofi', 'Asare', 'DL-005-2017', '2023-11-30', '+233544778899', 'kofi.asare@ug.edu.gh', '22 Campus Street, Legon', '2017-11-15', 'Active', NULL, '2025-04-14 20:20:20', '2025-04-16 23:49:36'),
(7, ' Dekay ', 'ofori', '3', '2025-04-15', '12', 'd@f.cpm', 'p.o.box legon 34', '2025-04-15', 'Active', NULL, '2025-04-15 00:47:29', '2025-04-16 23:49:49'),
(8, 'Agyemang', 'Manu', 'GT-1324-23', '2029-10-30', '0002344354', 'agyenu@gmail.com', 'adsds', '2025-04-15', 'Active', 'uploads/drivers/67feb93894a1c_IMG_9713 (1).jpg', '2025-04-15 19:53:28', '2025-04-30 18:04:22'),
(9, 'Atta', 'Kakra', 'GT-6789-24', '2029-01-30', '0906781234', 'atka23@gmail.com', 'P.O.Box lg 23', '2021-02-28', 'On Leave', 'uploads/driver_1746036959.jpg', '2025-04-30 18:15:25', '2025-04-30 18:15:59'),
(10, 'Sarfo', 'Yaw', '3235465768', '2028-05-16', '02356799', 'Sa34@gmail.com', 'P.o.box 13030 madina', '2022-04-08', 'On Leave', 'uploads/driver_1746209269.jpeg', '2025-05-02 18:07:18', '2025-05-02 18:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `start_point` varchar(100) NOT NULL,
  `end_point` varchar(100) NOT NULL,
  `distance` decimal(6,2) DEFAULT NULL COMMENT 'Distance in kilometers',
  `estimated_duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive','Under Review') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_name`, `start_point`, `end_point`, `distance`, `estimated_duration`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Legon Main Campus Shuttle', 'Great Hall', 'Balme Library', 2.50, 10, 'Main campus circular shuttle service', 'Active', '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(2, 'Accra City Route', 'Legon Main Gate', 'Kwame Nkrumah Circle', 12.30, 35, 'Daily commuter route to central Accra', 'Active', '2025-04-14 20:20:20', '2025-04-23 16:11:41'),
(3, 'Staff Housing Route', 'Legon', 'East Legon Staff Housing', 8.70, 25, 'Morning and evening staff transport', 'Active', '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(4, 'Night Security Patrol', 'Security Post 1', 'Security Post 8', 15.20, 45, 'Nighttime campus security patrol route', 'Active', '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(5, 'Special Events Shuttle', 'Great Hall', 'ISSER Conference Center', 3.80, 12, 'Route for conferences and special events', 'Active', '2025-04-14 20:20:20', '2025-04-24 20:03:37'),
(6, 'Medical Emergency Route', 'UG Hospital', 'Korle-Bu Teaching Hospital', 14.50, 30, 'Emergency medical transfer route', 'Active', '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(8, 'asdf', 'sd', 'acsdf', 2.00, 3, '', 'Active', '2025-04-24 19:17:06', '2025-04-24 19:17:06'),
(9, 'Main Campus lane', 'Commonwealth hall', 'Akuafo hall', 4.00, 17, '', 'Active', '2025-05-02 18:10:09', '2025-05-02 18:10:09');

-- --------------------------------------------------------

--
-- Table structure for table `route_coordinates`
--

CREATE TABLE `route_coordinates` (
  `coordinate_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `sequence_order` int(11) NOT NULL,
  `landmark` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_coordinates`
--

INSERT INTO `route_coordinates` (`coordinate_id`, `route_id`, `latitude`, `longitude`, `sequence_order`, `landmark`, `created_at`) VALUES
(1, 1, 5.65000000, -0.18600000, 1, 'Great Hall', '2025-04-14 20:20:20'),
(2, 1, 5.65050000, -0.18650000, 2, 'Department of Mathematics', '2025-04-14 20:20:20'),
(3, 1, 5.65100000, -0.18700000, 3, 'JQB Building', '2025-04-14 20:20:20'),
(4, 1, 5.65150000, -0.18750000, 4, 'Balme Library', '2025-04-14 20:20:20'),
(21, 2, 5.65000000, -0.18600000, 1, 'Legon Main Gate', '2025-04-23 16:11:41'),
(22, 2, 5.64000000, -0.18000000, 2, 'Okponglo Junction', '2025-04-23 16:11:41'),
(23, 2, 5.63000000, -0.17500000, 3, '37 Military Hospital', '2025-04-23 16:11:41'),
(24, 2, 5.62000000, -0.17000000, 4, 'Kwame Nkrumah Circle', '2025-04-23 16:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `route_schedules`
--

CREATE TABLE `route_schedules` (
  `schedule_id` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(10) DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_schedules`
--

INSERT INTO `route_schedules` (`schedule_id`, `route_id`, `vehicle_id`, `driver_id`, `day_of_week`, `departure_time`, `arrival_time`, `status`) VALUES
(1, 1, 1, 1, 'Monday', '07:00:00', '07:10:00', 'Completed'),
(2, 1, 1, 1, 'Monday', '07:20:00', '07:30:00', 'Completed'),
(3, 1, 2, 2, 'Tuesday', '07:00:00', '07:10:00', 'Scheduled'),
(4, 2, 4, 4, 'Wednesday', '06:30:00', '07:05:00', 'Scheduled'),
(5, 3, 5, 6, 'Thursday', '16:00:00', '16:25:00', 'Scheduled'),
(6, 4, NULL, NULL, 'Friday', '20:00:00', '20:45:00', 'Scheduled'),
(7, 6, 1, 1, 'Saturday', '09:00:00', '09:30:00', 'Scheduled'),
(8, 1, 2, 2, 'Sunday', '08:00:00', '08:10:00', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `university_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `user_type` enum('student','staff','driver','administrator') NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `reset_token`, `reset_token_expires_at`, `created_at`, `updated_at`, `university_id`, `first_name`, `last_name`, `user_type`, `phone`) VALUES
(2, 'Ama432', 'amaya23@st.ug.com', '$2y$10$lZtOGfPNnxAZUXyRYWYIweIObPUCwZ1/HVYnknzGXD9vQuAQffkO2', NULL, NULL, '2025-04-27 16:20:49', '2025-04-27 16:20:49', '23456', 'Ama', 'Yaabea', 'student', '0556671234'),
(3, 'kose32', 'ko65@st.ug.com', '$2y$10$GCpTqAqjXYnvEZRwGqxQBejNvGpiK.dgiB5k4TAHyUymkUhQrk.9W', NULL, NULL, '2025-04-27 16:28:26', '2025-04-27 16:28:26', '21345', 'kofi', 'boa', 'staff', '0223345531'),
(4, 'efua12', 'efua12@st.ug.com', '$2y$10$abc123EfuaHashPass...', 'NULL', NULL, '2025-04-27 17:00:00', '2025-04-27 17:00:00', '27654', 'Efua', 'Mensah', 'student', '0241234567'),
(5, 'kwabena21', 'kwabena21@st.ug.com', '$2y$10$xyz789KwabenaPass...', NULL, NULL, '2025-04-27 17:05:00', '2025-04-27 17:05:00', '19876', 'Kwabena', 'Owusu', 'staff', '0209876543'),
(6, 'akua44', 'akua44@st.ug.com', '$2y$10$akuaPassEncrypted...', NULL, NULL, '2025-04-27 17:10:00', '2025-04-27 17:10:00', '28765', 'Akua', 'Asante', 'student', '0546781234');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type` enum('Bus','Minibus','Van','Truck') NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `registration_number` varchar(20) NOT NULL,
  `vin` varchar(17) NOT NULL,
  `purchase_date` date NOT NULL,
  `capacity` int(11) NOT NULL,
  `fuel_type` enum('Petrol','Diesel','Electric','Hybrid') NOT NULL,
  `current_mileage` decimal(10,2) DEFAULT 0.00,
  `status` enum('Active','Maintenance','Out of Service') DEFAULT 'Active',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `vehicle_type`, `make`, `model`, `year`, `registration_number`, `vin`, `purchase_date`, `capacity`, `fuel_type`, `current_mileage`, `status`, `image_path`, `created_at`, `updated_at`) VALUES
(3, 'Van', 'Mercedes', 'Sprinter', 2019, 'UG-003-19', 'WDB9066331S123456', '2019-05-22', 12, 'Petrol', 65432.10, 'Active', NULL, '2025-04-14 20:20:20', '2025-04-22 07:14:55'),
(4, 'Bus', 'Yutong', 'ZK6128', 2022, 'UG-004-22', 'LZYTATE26A1001234', '2022-02-18', 45, 'Petrol', 12345.60, 'Out of Service', NULL, '2025-04-14 20:20:20', '2025-04-22 07:15:10'),
(5, 'Minibus', 'Nissan', 'Urvan', 2018, 'UG-005-18', 'JN8BU08K18W123456', '2018-11-05', 12, '', 87654.30, 'Active', NULL, '2025-04-14 20:20:20', '2025-04-22 08:01:06'),
(8, 'Bus', 'toyota', 'coaster', 2022, 'GT-1234-21', '12345678912345678', '2025-04-22', 4, '', 4.00, 'Active', NULL, '2025-04-22 05:28:16', '2025-04-24 20:03:00'),
(9, 'Minibus', 'corolla', 'runner', 2020, 'GT-5678-19', '12344567891234567', '2025-04-22', 3, '', 2.00, 'Active', NULL, '2025-04-22 07:38:24', '2025-04-30 18:41:46'),
(11, 'Bus', 'toyota', 'corol', 2018, 'GT-1234-23', '12345678912345676', '2017-05-02', 4, '', 7.00, 'Maintenance', NULL, '2025-05-02 18:03:09', '2025-05-02 18:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_maintenance`
--

CREATE TABLE `vehicle_maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_type` enum('Routine','Repair','Inspection','Other') NOT NULL,
  `description` text DEFAULT NULL,
  `maintenance_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` enum('Scheduled','In Progress','Completed','Cancelled') DEFAULT 'Scheduled',
  `technician` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_maintenance`
--

INSERT INTO `vehicle_maintenance` (`maintenance_id`, `vehicle_id`, `maintenance_type`, `description`, `maintenance_date`, `completion_date`, `cost`, `status`, `technician`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 'Repair', 'Engine overhaul and transmission service', '2023-05-15', '2023-05-18', 8500.00, 'Completed', 'Kwame AutoWorks', NULL, '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(5, 5, 'Repair', '20,000km service and brake system check', '2025-04-14', '2025-04-14', 1500.00, 'Scheduled', 'UG Transport Workshop', '', '2025-04-14 20:20:20', '2025-04-14 21:46:17'),
(6, 5, 'Repair', 'AC system repair and refrigerant recharge', '2023-05-28', '2023-05-29', 1850.00, 'Completed', 'CoolTech Auto AC', NULL, '2025-04-14 20:20:20', '2025-04-14 20:20:20'),
(9, 5, 'Inspection', 'deflated tires', '2025-04-15', '2025-04-15', 40.00, 'In Progress', 'Dekay', '', '2025-04-15 19:17:12', '2025-04-30 18:01:08'),
(10, 8, 'Routine', 'Fuel checker', '2025-04-30', '2025-05-10', 60.00, 'Scheduled', 'Yaw Asamoah', NULL, '2025-04-30 18:02:06', '2025-04-30 18:02:06'),
(11, 11, 'Other', 'Flat tyres', '2025-05-02', '2025-05-08', 200.00, 'Scheduled', 'Kwame', NULL, '2025-05-02 18:04:56', '2025-05-02 18:04:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_booking_user` (`user_id`),
  ADD KEY `idx_booking_route` (`route_id`),
  ADD KEY `idx_booking_schedule` (`schedule_id`),
  ADD KEY `idx_booking_date` (`travel_date`),
  ADD KEY `idx_booking_status` (`status`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_driver_license` (`license_number`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `idx_route_name` (`route_name`);

--
-- Indexes for table `route_coordinates`
--
ALTER TABLE `route_coordinates`
  ADD PRIMARY KEY (`coordinate_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `route_schedules`
--
ALTER TABLE `route_schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `university_id` (`university_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD UNIQUE KEY `vin` (`vin`),
  ADD KEY `idx_vehicle_registration` (`registration_number`);

--
-- Indexes for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `route_coordinates`
--
ALTER TABLE `route_coordinates`
  MODIFY `coordinate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `route_schedules`
--
ALTER TABLE `route_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `route_schedules` (`schedule_id`);

--
-- Constraints for table `route_coordinates`
--
ALTER TABLE `route_coordinates`
  ADD CONSTRAINT `route_coordinates_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_maintenance`
--
ALTER TABLE `vehicle_maintenance`
  ADD CONSTRAINT `vehicle_maintenance_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
