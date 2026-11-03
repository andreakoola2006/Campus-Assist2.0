-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2026 at 07:37 AM
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
-- Database: `class_availability`
--

-- --------------------------------------------------------

--
-- Table structure for table `bca_bsc_classes`
--

CREATE TABLE `bca_bsc_classes` (
  `id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `class_number` int(11) NOT NULL,
  `status` enum('available','occupied') NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bca_bsc_classes`
--

INSERT INTO `bca_bsc_classes` (`id`, `floor`, `class_number`, `status`, `date`) VALUES
(3, 3, 8, 'occupied', '2026-03-25'),
(13, 4, 10, 'occupied', '2026-04-30'),
(15, 3, 16, 'occupied', '2026-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `bca_lab`
--

CREATE TABLE `bca_lab` (
  `id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `lab_number` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `btech_classes`
--

CREATE TABLE `btech_classes` (
  `id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `class_number` int(11) NOT NULL,
  `status` enum('available','occupied') NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `btech_classes`
--

INSERT INTO `btech_classes` (`id`, `floor`, `class_number`, `status`, `date`) VALUES
(8, 2, 11, 'occupied', '2026-04-29');

-- --------------------------------------------------------

--
-- Table structure for table `btech_lab`
--

CREATE TABLE `btech_lab` (
  `id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `lab_number` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bca_bsc_classes`
--
ALTER TABLE `bca_bsc_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_floor_class_date` (`floor`,`class_number`,`date`);

--
-- Indexes for table `bca_lab`
--
ALTER TABLE `bca_lab`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lab` (`floor`,`lab_number`,`date`);

--
-- Indexes for table `btech_classes`
--
ALTER TABLE `btech_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_floor_class_date` (`floor`,`class_number`,`date`);

--
-- Indexes for table `btech_lab`
--
ALTER TABLE `btech_lab`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lab` (`floor`,`lab_number`,`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bca_bsc_classes`
--
ALTER TABLE `bca_bsc_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bca_lab`
--
ALTER TABLE `bca_lab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `btech_classes`
--
ALTER TABLE `btech_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `btech_lab`
--
ALTER TABLE `btech_lab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
