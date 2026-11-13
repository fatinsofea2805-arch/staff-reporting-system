-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Feb 07, 2025 at 01:41 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ptjpm`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `ADMIN_ID` int NOT NULL AUTO_INCREMENT,
  `ADMIN_EMAIL` varchar(200) NOT NULL,
  `ADMIN_PASSWORD` varchar(200) NOT NULL,
  `LOGGED_IN` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ADMIN_ID`),
  UNIQUE KEY `ADMIN_EMAIL` (`ADMIN_EMAIL`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ADMIN_ID`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `LOGGED_IN`) VALUES
(1, 'admin2025@gmail.com', '$2y$10$VvSjOroKt20UrAp4Y4FpmOeAs3sTOGYw3M3cBEtNc2LwWXv4L4fCS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `emergency`
--

DROP TABLE IF EXISTS `emergency`;
CREATE TABLE IF NOT EXISTS `emergency` (
  `LEAVE_ID` char(8) NOT NULL,
  `LEAVE_DATE` date NOT NULL,
  `LEAVE_TIME` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LEAVE_TYPE` enum('Medical','Family Emergency','Personal','Other') NOT NULL,
  `LEAVE_DURATION` enum('Full Day','Half Day','Hourly') NOT NULL,
  `LEAVE_HOURS` int DEFAULT NULL,
  `OTHER_LEAVE_DETAILS` text,
  `FILE_NAME` varchar(255) DEFAULT NULL,
  `FILE_TYPE` varchar(50) DEFAULT NULL,
  `LEAVE_STATUS` enum('Not Seen','Seen') DEFAULT 'Not Seen',
  `STAFF_ID` char(8) NOT NULL,
  PRIMARY KEY (`LEAVE_ID`),
  UNIQUE KEY `LEAVE_ID` (`LEAVE_ID`),
  KEY `STAFF_ID` (`STAFF_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emergency`
--

INSERT INTO `emergency` (`LEAVE_ID`, `LEAVE_DATE`, `LEAVE_TIME`, `LEAVE_TYPE`, `LEAVE_DURATION`, `LEAVE_HOURS`, `OTHER_LEAVE_DETAILS`, `FILE_NAME`, `FILE_TYPE`, `LEAVE_STATUS`, `STAFF_ID`) VALUES
('LV4eecaa', '2025-02-04', '2025-02-04 08:07:57', 'Family Emergency', 'Full Day', 0, '-', 'Sijil Kematian Hj Idris Hj Zen.jpg', 'image/jpeg', 'Not Seen', '5446AEC9'),
('LVd74def', '2025-02-03', '2025-02-03 13:36:16', 'Other', 'Hourly', 5, '-', 'no file', 'no file', 'Not Seen', '623B9B8B'),
('LV2a774e', '2025-02-03', '2025-02-03 13:43:40', 'Personal', 'Half Day', 0, '-', '0b70c3dd05725119cc94225f31f762bc.jpg', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LV21de49', '2025-02-03', '2025-02-03 13:44:04', 'Other', 'Hourly', 2, '-', 'no file', 'no file', 'Not Seen', 'BE28904E'),
('LVe5c8ab', '2025-02-02', '2025-02-02 06:55:39', 'Other', 'Full Day', 0, 'kursus', 'no file', 'no file', 'Not Seen', '77AA8DC6'),
('LVa5af50', '2025-02-06', '2025-02-07 13:19:54', 'Other', 'Hourly', 7, 'kursus', '', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LV9e54be', '2025-02-02', '2025-02-02 06:55:13', 'Family Emergency', 'Hourly', 7, '-', '825814baf8bd9c85f3b55d962a89158a.jpg', 'image/jpeg', 'Not Seen', '77AA8DC6'),
('LV32d650', '2025-02-03', '2025-02-03 03:06:24', 'Personal', 'Hourly', 9, '-', '', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LVe84867', '2025-02-03', '2025-02-03 13:33:38', 'Medical', 'Full Day', 0, '-', 'no file', 'no file', 'Not Seen', '623B9B8B'),
('LV4484e7', '2025-02-03', '2025-02-03 13:35:50', 'Personal', 'Hourly', 3, '-', 'no file', 'no file', 'Not Seen', '623B9B8B'),
('LV0a2d71', '2025-02-06', '2025-02-06 16:17:03', 'Family Emergency', 'Hourly', 6, '-', '', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LV20e3fc', '2025-02-07', '2025-02-07 13:33:47', 'Personal', 'Half Day', 0, '-', '', 'no file', 'Not Seen', 'BE28904E'),
('LVd923eb', '2025-02-06', '2025-02-07 13:40:13', 'Personal', 'Half Day', 0, '-', '', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LV1899cc', '2025-02-07', '2025-02-07 13:33:26', 'Other', 'Full Day', 0, '-', '', 'application/pdf', 'Not Seen', 'BE28904E'),
('LV5f26ae', '2025-02-07', '2025-02-07 13:09:22', 'Other', 'Hourly', 8, 'kursus', 'DLI-04 Report Format.docx.pdf', 'application/pdf', 'Not Seen', 'BE28904E');

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

DROP TABLE IF EXISTS `holiday`;
CREATE TABLE IF NOT EXISTS `holiday` (
  `HOLIDAY_ID` char(36) NOT NULL,
  `HOLIDAY_NAME` varchar(255) NOT NULL,
  `HOLIDAY_TIME` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL,
  `HOLIDAY_DURATION` int DEFAULT NULL,
  `ADMIN_ID` int NOT NULL,
  PRIMARY KEY (`HOLIDAY_ID`),
  KEY `ADMIN_ID` (`ADMIN_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`HOLIDAY_ID`, `HOLIDAY_NAME`, `HOLIDAY_TIME`, `START_DATE`, `END_DATE`, `HOLIDAY_DURATION`, `ADMIN_ID`) VALUES
('ec3bad97', 'Nuzul Al-Quran', '2025-02-03 03:13:50', '2025-03-18', '2025-03-18', 1, 0),
('717e39a5', 'Chinese New Year', '2025-02-06 23:25:22', '2025-01-29', '2025-01-30', 2, 0),
('141fb32b', 'Malaysia Day', '2025-02-03 13:40:13', '2025-09-16', '2025-09-16', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lateness`
--

DROP TABLE IF EXISTS `lateness`;
CREATE TABLE IF NOT EXISTS `lateness` (
  `LATENESS_ID` char(8) NOT NULL,
  `LATENESS_TIME` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LATENESS_DATE` date NOT NULL,
  `LATENESS_REASON` text,
  `FILE_NAME` varchar(255) DEFAULT NULL,
  `FILE_TYPE` varchar(50) DEFAULT NULL,
  `LATENESS_STATUS` enum('Seen','Not Seen') DEFAULT 'Not Seen',
  `STAFF_ID` char(8) DEFAULT NULL,
  PRIMARY KEY (`LATENESS_ID`),
  KEY `STAFF_ID` (`STAFF_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lateness`
--

INSERT INTO `lateness` (`LATENESS_ID`, `LATENESS_TIME`, `LATENESS_DATE`, `LATENESS_REASON`, `FILE_NAME`, `FILE_TYPE`, `LATENESS_STATUS`, `STAFF_ID`) VALUES
('LR4db229', '2025-02-04 08:08:58', '2025-02-04', 'Traffic Jam', '67a1cb1a-Free-Apology-Letter-for-Late-Coming-to-Work-Template-1.jpg', 'image/jpeg', 'Seen', '5446AEC9'),
('LR3f9c4f', '2025-02-02 06:56:07', '2025-02-02', 'Bad Weather Conditions', 'no file', 'no file', 'Not Seen', '77AA8DC6'),
('LR8a8147', '2025-02-07 13:05:26', '2025-02-07', 'traffic jam', 'Gantt Chart (1).pdf', 'application/pdf', 'Not Seen', 'BE28904E'),
('LRe47aa5', '2025-02-02 06:53:08', '2025-02-02', 'Health Issues', 'image-removebg-preview (20).png', 'image/png', 'Not Seen', '77AA8DC6'),
('LRb82607', '2025-02-02 06:52:48', '2025-02-02', 'Car Breakdown', 'image-removebg-preview (21).png', 'image/png', 'Not Seen', '77AA8DC6'),
('LR2f8d78', '2025-02-03 13:31:36', '2025-02-03', 'Bad weather', '38fb57b8f3c12ea0fef74118580ef79e.jpg', 'image/jpeg', 'Not Seen', '623B9B8B'),
('LRe6de74', '2025-02-03 13:30:24', '2025-02-03', 'Traffic jam', '693013567-Free-Apology-Letter-for-Late-Coming-to-Work-Template.docx', 'application/vnd.openxmlformats-officedocument.word', 'Not Seen', '623B9B8B'),
('LR5fec8f', '2025-02-03 03:07:06', '2025-02-03', 'traffic jam', '', 'image/jpeg', 'Not Seen', 'BE28904E'),
('LR41b688', '2025-02-06 14:10:22', '2025-02-06', 'Car Breakdown', '', 'no file', 'Not Seen', 'BE28904E'),
('LRbac40c', '2025-02-06 23:04:12', '2025-02-07', 'Bad Weather', '', 'application/vnd.openxmlformats-officedocument.word', 'Not Seen', 'BE28904E'),
('LR9a6040', '2025-02-06 14:10:57', '2025-02-06', 'Personal Health Issues', '', 'no file', 'Not Seen', 'BE28904E'),
('LRbb12a2', '2025-02-06 13:23:25', '2025-02-06', 'Family Emergency', 'Free-Apology-Letter-for-Late-Coming-to-Work-Template-1.jpg', 'image/jpeg', 'Not Seen', 'BE28904E');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `STAFF_ID` char(8) NOT NULL,
  `STAFF_NAME` varchar(100) NOT NULL,
  `DEPARTMENT` varchar(100) DEFAULT NULL,
  `UNIT` varchar(100) DEFAULT NULL,
  `POSITION` varchar(100) DEFAULT NULL,
  `PHONE_NUM` varchar(15) DEFAULT NULL,
  `STAFF_EMAIL` varchar(100) NOT NULL,
  `STAFF_PASSWORD` varchar(255) NOT NULL,
  `PROFILE_PIC` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`STAFF_ID`),
  UNIQUE KEY `STAFF_EMAIL` (`STAFF_EMAIL`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`STAFF_ID`, `STAFF_NAME`, `DEPARTMENT`, `UNIT`, `POSITION`, `PHONE_NUM`, `STAFF_EMAIL`, `STAFF_PASSWORD`, `PROFILE_PIC`) VALUES
('77AA8DC6', 'John Doe', 'management_services', 'administration_unit', 'Accountant', '+6017-9096722', 'John@gmail.com', '$2y$10$1rA3iVe6h//B6nHMjYEqk.rChfGyRVF1nsz6HIhx/ev8KEligi0Ku', 'uploads/profile_pictures/679e4463c3cf6.jpg'),
('6D7D7E76', 'John Doe1', 'management_services', 'finance', 'Accountant', '+6017-9096722', 'JohnDoe@gmail.com', '$2y$10$QQeE1JshqYDI0qJ6WkU.iOQh76Xa4MISF7n0sM.dAwiSgKkauEfVO', NULL),
('722C1955', 'John Doe2', 'management_services', 'administration', 'Accountant', '+6017-9096722', 'JohnDoe2@gmail.com', '$2y$10$7UuBvKuvU2Zggg/csThQnuESMvBFIopgkLpsMurC4amgWh1i9cNUq', NULL),
('5446AEC9', 'Nurul Amira Binti Ali', 'land', 'enforcement_&_technical_unit', 'Planning and Development Officer', '+6013-6789012', 'nurulamira@gmail.com', '$2y$10$oXRIxHq6sFDHhq06xCf0reHfr6pZ0S5zTbqptrz5nK1U7HqtsUIkm', 'uploads/profile_pictures/67a1ca8412b27.jpg'),
('6DBFC0ED', 'Sofia Imani', 'management_services', 'finance', 'Finance Officer', '+6017-1122334', 'sofia.imani@email.com', '$2y$10$nEm5D44/3iTU3x8mq.rb0Owi3AKW985VtJl9sHJJ1Yo3dvbEnhQE.', NULL),
('3408F315', 'Nur Aisyah', 'land', 'technical', 'Land Surveyor', '+6019-3344556', 'nur.aisyah@email.com', '$2y$10$NTQ4Q2RyzGJaOz9NvwSeKu5n9hSgKtgKbdcz8N1199jVHsem2lY1W', NULL),
('0286BD14', 'Aaliyah Farhana', 'management_services', 'administration', 'P/O', '+6012-3456789', 'aaliyah.farhana@email.com', '$2y$10$RphqTPzIdeQ00JuEhnXFWOljdpqjCTzfRBJs6iH6x1bPYYmKE4fSq', NULL),
('BE28904E', 'Aiman Hakim Bin Roslan', 'management_services', 'technology_&_information_unit', 'IT Support Specialist', '+6012-3456909', 'aimanhakim98@gmail.com', '$2y$10$EJ5WMSta6LoY4iiHojjCJedlpiIXxKU0PGo2QwzYu6mWQSVl0uddi', 'uploads/profile_pictures/67a4a97fa789e.jpg'),
('623B9B8B', 'Siti Khadijah Binti Mohd Zain', 'district_development', 'physical_development_unit', 'Infrastructure Development Officer', '+6011-2345678', 'sitikhadijah@gmail.com', '$2y$10$icaJkMXs339bcgM2d2Eagu3BHofZ8wYoGXhKTwVYVA4EI5IB9CuuS', 'uploads/profile_pictures/67a0c43dddf27.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
