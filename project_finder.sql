-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 06, 2026 at 09:58 AM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_finder`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`t_id`, `username`, `password`) VALUES
(1, 'Admin', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dep_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `dep_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `is_archived` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dep_id` (`dep_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `dep_id`, `dep_name`, `username`, `password`, `email`, `is_archived`) VALUES
(1, 'Dep 001', 'Test department', 'Test', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'test@gmail.com', 0),
(3, 'dep 002', 'Department Of Transport', 'DOT', '$2y$10$RQkDXSNbymdRRaPSqV9nlOZINGXCmBRFbdQ79H6B84sRBtGr2CwV.', 'pesati6362@futebr.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments_archive`
--

DROP TABLE IF EXISTS `departments_archive`;
CREATE TABLE IF NOT EXISTS `departments_archive` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dep_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `dep_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `is_archived` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dep_id` (`dep_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dep_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `synopsis` text COLLATE utf8mb4_general_ci NOT NULL,
  `year` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `dep_id`, `title`, `synopsis`, `year`, `created_at`, `file_path`) VALUES
(10, 'Dep 001', 'Online Auction System', 'Online Auction System for Regional maritime University Staff', 2025, '2025-11-17 22:48:10', '1763636396_Acceptance_Letter_RMU_IAS.pdf'),
(11, 'Dep 001', 'efrgthyjukil', 'wdefrgthyjuki', 2021, '2025-11-17 23:17:16', '1763421436_first_resume.pdf'),
(9, 'Dep 001', 'Online Cadet Management System', 'test description', 2025, '2025-11-17 22:20:32', ''),
(12, 'Dep 001', 'Online Library System', 'defrg', 2025, '2025-11-19 14:31:15', '1763562675_Acceptance_Letter_RMU_IAS.pdf'),
(8, 'Dep 001', 'Online Accomodation system', 'Accomodation system for Gambia hostel', 2009, '2025-11-17 21:22:04', ''),
(13, 'Dep 001', 'Online Evaluation System', 'test descrption', 2020, '2025-11-19 14:55:34', ''),
(14, 'Dep 001', 'test one', 'test project', 2025, '2026-01-01 15:46:34', '1767282394_Assignment_Papers_cephas.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

DROP TABLE IF EXISTS `project_members`;
CREATE TABLE IF NOT EXISTS `project_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `index_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `student_name`, `index_number`) VALUES
(32, 10, 'AKOTO GODSWAY', 'tyu678999'),
(25, 9, 'Rexford hammond', 'BIT1000567'),
(24, 9, 'Annetta Kuma', 'BIT00023490'),
(23, 8, 'prince geraldo', 'BIT00019312'),
(22, 8, 'princess lantsu', 'BCS0000827'),
(21, 11, 'baba', 'biw103043'),
(31, 10, 'Ismail Abdulai-Saiku', 'BIT0000923'),
(29, 12, 'baba spirit', 'BIT00019312'),
(34, 13, 'AKOTO GODSWAY', 'bit12345678'),
(33, 14, 'eric banzy', 'biw22044');

-- --------------------------------------------------------

--
-- Table structure for table `project_supervisors`
--

DROP TABLE IF EXISTS `project_supervisors`;
CREATE TABLE IF NOT EXISTS `project_supervisors` (
  `project_id` int NOT NULL,
  `supervisor_id` int NOT NULL,
  PRIMARY KEY (`project_id`,`supervisor_id`),
  KEY `supervisor_id` (`supervisor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_supervisors`
--

INSERT INTO `project_supervisors` (`project_id`, `supervisor_id`) VALUES
(8, 2),
(9, 3),
(9, 4),
(10, 4),
(11, 1),
(12, 2),
(13, 2),
(14, 4);

-- --------------------------------------------------------

--
-- Table structure for table `project_tags`
--

DROP TABLE IF EXISTS `project_tags`;
CREATE TABLE IF NOT EXISTS `project_tags` (
  `project_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`project_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_tags`
--

INSERT INTO `project_tags` (`project_id`, `tag_id`) VALUES
(8, 13),
(8, 14),
(9, 15),
(9, 16),
(9, 17),
(10, 20),
(10, 21),
(10, 22),
(10, 23),
(11, 13),
(11, 19),
(12, 24),
(12, 25),
(13, 26),
(14, 27),
(14, 28);

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

DROP TABLE IF EXISTS `supervisors`;
CREATE TABLE IF NOT EXISTS `supervisors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dep_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','retired') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `dep_id` (`dep_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `first_name`, `last_name`, `email`, `dep_id`, `status`) VALUES
(1, 'Denzel', 'Arneson', 'h@gmail.com', 'Dep 001', 'active'),
(2, 'Eric', 'Arneson', 'j@gmail.com', 'Dep 001', 'active'),
(3, 'Ismail', 'Abdulai-Saiku', 'ismail.abdulai-saiku@st.rmu.edu.gh', 'Dep 001', 'active'),
(4, 'Ebenezer', 'ernests', 'ern@gmail.com', 'Dep 001', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors_archive`
--

DROP TABLE IF EXISTS `supervisors_archive`;
CREATE TABLE IF NOT EXISTS `supervisors_archive` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `dep_id` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('active','retired') COLLATE utf8mb4_general_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `dep_id` (`dep_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(25, 'book'),
(24, 'library'),
(23, 'rmu'),
(22, 'ecommerce'),
(21, 'bidding'),
(20, 'bid'),
(19, '7iik.yyrew'),
(18, '7iik'),
(17, 'cadet students'),
(16, 'MOWCA'),
(15, 'cadet'),
(13, 'hostel'),
(14, 'apartment'),
(26, 'eval'),
(27, 'test'),
(28, 'trial');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
