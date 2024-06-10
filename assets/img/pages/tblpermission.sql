-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 12, 2024 at 06:13 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employeeleavedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblpermission`
--

DROP TABLE IF EXISTS `tblpermission`;
CREATE TABLE IF NOT EXISTS `tblpermission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `RoleId` int NOT NULL,
  `PermissionId` int NOT NULL,
  `PermissionName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdateAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `EngName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `IconClass` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpermission`
--

INSERT INTO `tblpermission` (`id`, `RoleId`, `PermissionId`, `PermissionName`, `CreationDate`, `UpdateAt`, `EngName`, `Type`, `IconClass`, `Status`) VALUES
(1, 0, 0, 'ច្បាប់ឈប់សម្រាក', '2024-05-12 05:58:05', '2024-05-12 05:58:05', 'Leaves', 'Normal', 'bx bx-calendar', 0),
(2, 1, 1, '', '2024-05-12 05:58:18', '2024-05-12 05:58:18', '', '', '', 0),
(3, 0, 0, 'គ្រប់គ្របគណនីមន្ត្រី', '2024-05-12 06:01:26', '2024-05-12 06:01:26', 'Manage Users', 'Manage', 'bx bx-id-card', 0),
(4, 2, 1, '', '2024-05-12 06:01:56', '2024-05-12 06:01:56', '', '', '', 0),
(5, 2, 3, '', '2024-05-12 06:01:56', '2024-05-12 06:01:56', '', '', '', 0),
(6, 3, 1, '', '2024-05-12 06:03:44', '2024-05-12 06:03:44', '', '', '', 0),
(7, 3, 3, '', '2024-05-12 06:03:44', '2024-05-12 06:03:44', '', '', '', 0),
(8, 4, 1, '', '2024-05-12 06:05:53', '2024-05-12 06:05:53', '', '', '', 0),
(9, 4, 3, '', '2024-05-12 06:05:53', '2024-05-12 06:05:53', '', '', '', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
