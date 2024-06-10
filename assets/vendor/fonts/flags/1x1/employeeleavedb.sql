-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 02, 2024 at 10:43 AM
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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Password` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fullname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(55) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Profiles` varchar(255) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `fullname`, `email`, `Profiles`, `updationDate`) VALUES
(1, 'supperadmin', 'd00f5d5217896fb7fd601412cb890830', 'chamreun poth', 'admin@mail.com', '', '2024-04-02 08:49:17'),
(2, 'bruno', '5f4dcc3b5aa765d61d8327deb882cf99', 'Bruno Den', 'itsbruno@mail.com', '', '2022-02-09 15:15:50'),
(3, 'greenwood', '5f4dcc3b5aa765d61d8327deb882cf99', 'Johnny Greenwood', 'greenwood@mail.com', '', '2022-02-09 15:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `leave_type_id` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `comments` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `leave_type_id` (`leave_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblallsystems`
--

DROP TABLE IF EXISTS `tblallsystems`;
CREATE TABLE IF NOT EXISTS `tblallsystems` (
  `id` int NOT NULL AUTO_INCREMENT,
  `SystemName` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Link` varchar(400) COLLATE utf8mb4_general_ci NOT NULL,
  `Content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Status` int NOT NULL,
  `Pictures` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `CreationDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UpdateAt` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblallsystems`
--

INSERT INTO `tblallsystems` (`id`, `SystemName`, `Link`, `Content`, `Status`, `Pictures`, `CreationDate`, `UpdateAt`) VALUES
(1, 'ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យមន្ត្រី', 'https://hrms.iauoffsa.us', 'sadfsadfasdf', 1, 'IMG_system1.png', '03-Apr-2024 09:26 AM', ''),
(2, 'ប្រព័ន្ធគ្រប់គ្រងទិន្នន័យក្លោដ', 'https://cloud.iauoffsa.us', 'adfadsf', 1, 'IMG_system6.png', '03-Apr-2024 09:27 AM', ''),
(3, 'ប្រព័ន្ធគ្រប់គ្រងការកក់បន្ទប់ប្រជុំ', 'https://forms.gle/yDAoZiqrX5hKFtGA6', 'ថាដសថាដសថ', 1, 'IMG_system3.png', '03-Apr-2024 09:28 AM', ''),
(5, 'ប្រព័ន្ធបង្កើតរបាយការណ៍សវនកម្ម', 'http://audit.iauoffsa.gov.kh', 'asdfasdf', 1, 'IMG_system2.png', '03-Apr-2024 09:30 AM', ''),
(6, 'ប្រព័ន្ធគ្រប់គ្រងឯកសារចេញចូល', 'http://audit.iauoffsa.gov.kh', 'adfasdfasdf', 1, 'IMG_system5.png', '03-Apr-2024 09:31 AM', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartments`
--

DROP TABLE IF EXISTS `tbldepartments`;
CREATE TABLE IF NOT EXISTS `tbldepartments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `HeadOfDepartment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_croatian_ci NOT NULL,
  `DepHeadOfDepartment` varchar(255) NOT NULL,
  `CreationDate` varchar(255) NOT NULL,
  `UpdateAt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tbldepartments`
--

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `HeadOfDepartment`, `DepHeadOfDepartment`, `CreationDate`, `UpdateAt`) VALUES
(1, 'កិច្ចការទូទៅ', 'លោក នួន សំរតនា', 'លោក ថៅ គីមរុង', '17-Apr-2024 04:01 PM', ''),
(2, 'សវនកម្មទី១', '', '', '17-Apr-2024 04:40 PM', ''),
(3, 'សវនកម្មទី២', '', '', '17-Apr-2024 04:40 PM', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

DROP TABLE IF EXISTS `tblemployees`;
CREATE TABLE IF NOT EXISTS `tblemployees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `EmpId` varchar(100) NOT NULL,
  `UserName` varchar(255) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(180) NOT NULL,
  `Gender` varchar(100) NOT NULL,
  `HeadofDirector` varchar(255) NOT NULL,
  `DeputyHeadofDirector` varchar(255) NOT NULL,
  `DeputyHeadofOffice` varchar(255) NOT NULL,
  `HeadofOffice` varchar(255) NOT NULL,
  `Dob` varchar(100) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Positions` varchar(255) NOT NULL,
  `Offices` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(200) NOT NULL,
  `Country` varchar(150) NOT NULL,
  `Phonenumber` char(11) NOT NULL,
  `Status` int NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Profiles` varchar(255) NOT NULL,
  `rolename` int NOT NULL,
  `permission` int NOT NULL,
  `leavemanage` int NOT NULL,
  `latein` int NOT NULL,
  `lateout` int NOT NULL,
  `docin` int NOT NULL,
  `docout` int NOT NULL,
  `device_token` varchar(300) NOT NULL,
  `approveuser` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`id`, `EmpId`, `UserName`, `FirstName`, `LastName`, `EmailId`, `Password`, `Gender`, `HeadofDirector`, `DeputyHeadofDirector`, `DeputyHeadofOffice`, `HeadofOffice`, `Dob`, `Department`, `Positions`, `Offices`, `Address`, `City`, `Country`, `Phonenumber`, `Status`, `RegDate`, `Profiles`, `rolename`, `permission`, `leavemanage`, `latein`, `lateout`, `docin`, `docout`, `device_token`, `approveuser`) VALUES
(2, 'chamreun', 'លោក ពុធ ចំរើន', '', '', 'pothchamreun@gmail.com', '25d55ad283aa400af464c76d713c07ad', '1', '', '', '', '', '13-Mar-2024', '1', '7', '1', 'adf', '', '', '23434567878', 1, '2024-03-13 01:49:01', 'photo_2024-03-20_16-25-49.jpg', 3, 0, 0, 1, 0, 0, 0, '', 0),
(3, 'chenveasna', 'លោក​ ចិន​ វាសនា', '', '', 'chenveasna@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '13-Mar-2024', '1', '6', '1', 'asdfasdf', '', '', '23434567878', 1, '2024-03-13 02:02:32', 'photo_2024-03-25_12-45-42.jpg', 2, 0, 0, 1, 0, 0, 0, '', 1),
(4, 'meiching', 'លោកស្រី ស៊ាប ម៉ីជីង', '', '', 'meiching@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '2', '', '', '', '', '13-Mar-2024', '1', '7', '1', 'errrr', '', '', '23434567878', 1, '2024-03-13 03:02:52', 'photo_2024-03-25_12-47-47.jpg', 3, 0, 0, 1, 0, 0, 0, '', 0),
(5, 'chanthy', 'កញ្ញា សេង ចន្ធី', '', '', 'chanthy@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '2', '', '', '', '', '13-Mar-2024', '1', '6', '6', 'kk', '', '', '', 1, '2024-03-13 04:27:29', 'photo_2023-05-11_15-11-49.jpg', 1, 1, 1, 1, 0, 1, 0, '', 1),
(6, 'bondeth', 'លោក ជា សេរីបណ្ឌិត', '', '', 'cheasereibondeth@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '13-Mar-2024', '1', '5', '1', 'ok', '', '', '235r34tgrsg', 1, '2024-03-13 05:50:01', 'photo_2024-03-25_09-28-19.jpg', 2, 0, 0, 1, 0, 0, 0, '', 1),
(7, 'kimrong', 'លោក ថៅ គីមរុង', '', '', 'thavkimrong@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '13-Mar-2024', '1', '4', '6', 'okay', '', '', 'asdf', 1, '2024-03-13 06:10:24', 'photo_2024-03-26_07-00-58.jpg', 1, 0, 0, 1, 0, 0, 0, '', 1),
(8, 'nounsamratana', 'លោក នួន សំរតនា', '', '', 'nounsamratana@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '13-Mar-2024', 'ប្រធាននាយកដ្ឋាន', 'ប្រធាននាយកដ្ឋាន', '', 'sadljf', '', '', '235r34tgrsg', 1, '2024-03-13 06:11:55', 'photo_2024-03-25_09-57-18.jpg', 1, 0, 0, 1, 0, 0, 0, '', 1),
(9, 'channa', 'លោក លឹម ចាន់ណា', '', '', 'limchanna@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '25-Mar-2024', '1', '2', '', 'pnhom penh', '', '', '098765432', 1, '2024-03-25 10:48:12', 'photo_2024-03-25_17-45-38.jpg', 1, 0, 0, 1, 0, 0, 0, '', 1),
(10, 'HE.Chunsambath', 'ឯកឧត្តម ឈុន សម្បត្តិ', '', '', 'chhunsambath@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '1', '', '', '', '', '25-Mar-2024', '', '1', '', 'dsfasdfsadfsadf', '', '', '0987654', 1, '2024-03-25 11:53:32', 'photo_2024-03-25_18-50-41.jpg', 1, 0, 1, 1, 0, 0, 0, '', 1),
(11, 'theara', 'កញ្ញា ធាង សុធារា', '', '', 'theara@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '2', '', '', '', '', '25-Mar-2024', '1', '7', '6', 'asdf', '', '', '098765432', 1, '2024-03-25 16:32:52', 'IMG_8119.PNG', 3, 0, 0, 1, 0, 0, 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbllateinout`
--

DROP TABLE IF EXISTS `tbllateinout`;
CREATE TABLE IF NOT EXISTS `tbllateinout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `datetime` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `reasons` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL,
  `aid` int NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `approvedate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `eid` int NOT NULL,
  `creationdate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `latesec` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `latemin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `latehour` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbllateinout`
--

INSERT INTO `tbllateinout` (`id`, `username`, `type`, `datetime`, `time`, `reasons`, `status`, `aid`, `comment`, `approvedate`, `eid`, `creationdate`, `latesec`, `latemin`, `latehour`) VALUES
(1, '', '1', '2024-03-26', '09:10', 'why ', 1, 5, 'okay', '26-Mar-2024 10:30 AM', 2, '26-Mar-2024 10:29 AM', '', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tbllatetype`
--

DROP TABLE IF EXISTS `tbllatetype`;
CREATE TABLE IF NOT EXISTS `tbllatetype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `LateName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Note` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `StartTime` time NOT NULL DEFAULT '09:00:00',
  `EndTime` time NOT NULL DEFAULT '04:00:00',
  `CreationDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UpdateAt` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbllatetype`
--

INSERT INTO `tbllatetype` (`id`, `LateName`, `Note`, `StartTime`, `EndTime`, `CreationDate`, `UpdateAt`) VALUES
(2, 'ចូលបម្រើការងារយឺត', 'សលកា្ដលថក្ាសដថ', '09:00:00', '04:00:00', '21-Apr-2024 03:17 PM', ''),
(3, 'ចេញពីបម្រើការងារយឺត', 'ត្រូវលកាស្ដថល្ាសលដថលាសដ្ថ្លាសដ្ថលក្ាលសដថាសដថ                                      ', '09:00:00', '04:00:00', '21-Apr-2024 03:18 PM', '21-Apr-2024 03:33 PM');

-- --------------------------------------------------------

--
-- Table structure for table `tblleaves`
--

DROP TABLE IF EXISTS `tblleaves`;
CREATE TABLE IF NOT EXISTS `tblleaves` (
  `id` int NOT NULL AUTO_INCREMENT,
  `LeaveType` varchar(110) NOT NULL,
  `LeaveDocs` varchar(255) NOT NULL,
  `ToDate` varchar(120) NOT NULL,
  `FromDate` varchar(120) NOT NULL,
  `Description` longtext NOT NULL,
  `PostingDate` varchar(255) NOT NULL,
  `PostingTime` varchar(100) NOT NULL,
  `Position` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `AdminRemark` longtext,
  `AdminRemarkDate` varchar(120) DEFAULT NULL,
  `AdminUserName` varchar(255) NOT NULL,
  `HPositions` varchar(255) NOT NULL,
  `HAdminRemark` varchar(255) NOT NULL,
  `HAdminRamarkDate` varchar(255) NOT NULL,
  `HUserName` varchar(255) NOT NULL,
  `HProfiles` varchar(255) NOT NULL,
  `HAdminSign` varchar(255) NOT NULL,
  `AdminProfiles` varchar(255) NOT NULL,
  `DiPositions` varchar(255) NOT NULL,
  `DiRemark` varchar(255) NOT NULL,
  `DiRemarkDate` varchar(255) NOT NULL,
  `DiProfiles` varchar(255) NOT NULL,
  `DiUserName` varchar(255) NOT NULL,
  `DUserName` varchar(255) NOT NULL,
  `DProfiles` varchar(255) NOT NULL,
  `DPositions` varchar(255) NOT NULL,
  `DRemark` varchar(255) NOT NULL,
  `DRemarkDate` varchar(255) NOT NULL,
  `DUUserName` varchar(255) NOT NULL,
  `DUProfiles` varchar(255) NOT NULL,
  `DURemark` varchar(255) NOT NULL,
  `DURemarkDate` varchar(255) NOT NULL,
  `DUPosition` varchar(255) NOT NULL,
  `UUserName` varchar(255) NOT NULL,
  `UPositions` varchar(255) NOT NULL,
  `URemark` varchar(255) NOT NULL,
  `URemarkDate` varchar(255) NOT NULL,
  `UProfiles` varchar(255) NOT NULL,
  `Status` int NOT NULL,
  `IsRead` int NOT NULL,
  `eIsRead` int NOT NULL,
  `NumDate` varchar(255) NOT NULL,
  `empid` int DEFAULT NULL,
  `CreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `UserEmail` (`empid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tblleaves`
--

INSERT INTO `tblleaves` (`id`, `LeaveType`, `LeaveDocs`, `ToDate`, `FromDate`, `Description`, `PostingDate`, `PostingTime`, `Position`, `AdminRemark`, `AdminRemarkDate`, `AdminUserName`, `HPositions`, `HAdminRemark`, `HAdminRamarkDate`, `HUserName`, `HProfiles`, `HAdminSign`, `AdminProfiles`, `DiPositions`, `DiRemark`, `DiRemarkDate`, `DiProfiles`, `DiUserName`, `DUserName`, `DProfiles`, `DPositions`, `DRemark`, `DRemarkDate`, `DUUserName`, `DUProfiles`, `DURemark`, `DURemarkDate`, `DUPosition`, `UUserName`, `UPositions`, `URemark`, `URemarkDate`, `UProfiles`, `Status`, `IsRead`, `eIsRead`, `NumDate`, `empid`, `CreateDate`) VALUES
(1, 'ច្បាប់ឈប់រយៈពេលខ្លី', 'Agenda on-28-March-2024-final.pdf', '01-Apr-24', '29-Mar-24', 'go to hometown', '26-Mar-24 10:18 AM', '', '6', 'have a nice day', '26-Mar-2024 10:20 AM ', 'លោក​ ចិន​ វាសនា', '5', 'okay', '26-Mar-2024 10:22 AM ', 'លោក ជា សេរីបណ្ឌិត', 'photo_2024-03-25_09-28-19.jpg', '', 'photo_2024-03-25_12-45-42.jpg', '4', 'okay', '26-Mar-2024 10:22 AM ', 'photo_2024-03-26_07-00-58.jpg', 'លោក ថៅ គីមរុង', 'លោក នួន សំរតនា', 'photo_2024-03-25_09-57-18.jpg', '3', 'okay', '26-Mar-2024 10:23 AM ', '', '', '', '', '', '', '', '', '', '', 4, 0, 0, '2', 2, '2024-03-26 03:18:37'),
(2, 'ច្បាប់ឈប់រយៈពេលខ្លី', 'ត្រីមាសទិ១_របាយការណ៍ទៅក្រសួង.docx', '29-Mar-24', '26-Mar-24', 'sick', '26-Mar-24 10:24 AM', '', '6', 'ok', '26-Mar-2024 10:25 AM ', 'លោក​ ចិន​ វាសនា', '5', 'ok', '26-Mar-2024 10:25 AM ', 'លោក ជា សេរីបណ្ឌិត', 'photo_2024-03-25_09-28-19.jpg', '', 'photo_2024-03-25_12-45-42.jpg', '4', 'ok', '26-Mar-2024 10:26 AM ', 'photo_2024-03-26_07-00-58.jpg', 'លោក ថៅ គីមរុង', 'លោក នួន សំរតនា', 'photo_2024-03-25_09-57-18.jpg', '3', '12345', '26-Mar-2024 10:26 AM ', 'លោក លឹម ចាន់ណា', 'photo_2024-03-25_17-45-38.jpg', 'ok', '26-Mar-2024 10:27 AM ', '2', 'ឯកឧត្តម ឈុន សម្បត្តិ', '1', 'ok', '26-Mar-2024 10:28 AM ', 'photo_2024-03-25_18-50-41.jpg', 7, 0, 0, '4', 2, '2024-03-26 03:24:55'),
(3, 'ច្បាប់ឈប់រយៈពេលខ្លី', '', '27-Mar-24', '26-Mar-24', 'សក្ថលស', '26-Mar-24 02:20 PM', '', '6', 'ឯកភាព', '26-Mar-2024 02:21 PM ', 'លោក​ ចិន​ វាសនា', '', '', '', '', '', '', 'photo_2024-03-25_12-45-42.jpg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 0, '2', 2, '2024-03-26 07:20:06'),
(4, 'ច្បាប់ឈប់រយៈពេលខ្លី', 'attendances (6).xlsx', '18-Apr-24', '17-Apr-24', 'asdfasdf', '17-Apr-24 09:01 AM', '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, '2', 2, '2024-04-17 02:01:48'),
(5, 'ឈប់សម្រាកដោយជម្ងឺ ឬគ្រោះថ្នាក់', 'CP&O - Salary Increment Letter - Kav Sothearoth.pdf', '24-Apr-24', '23-Apr-24', 'adsfasdf', '23-Apr-24 01:38 PM', '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, '2', 2, '2024-04-23 06:38:46');

-- --------------------------------------------------------

--
-- Table structure for table `tblleavetype`
--

DROP TABLE IF EXISTS `tblleavetype`;
CREATE TABLE IF NOT EXISTS `tblleavetype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `LeaveType` varchar(200) DEFAULT NULL,
  `DocReq` tinyint(1) NOT NULL,
  `Description` mediumtext,
  `Duration` varchar(255) NOT NULL,
  `CreationDate` varchar(255) NOT NULL,
  `UpdateAt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tblleavetype`
--

INSERT INTO `tblleavetype` (`id`, `LeaveType`, `DocReq`, `Description`, `Duration`, `CreationDate`, `UpdateAt`) VALUES
(1, 'ឈប់សម្រាកដោយជម្ងឺ ឬគ្រោះថ្នាក់', 1, 'ត្រូវមានឯកសារភ្ជាប់', '', '21-Apr-2024 01:06 PM', ''),
(3, 'សម្រាកលំហែមាតុភាព', 0, 'ឈប់សម្រាកបាន៣ខែ', '90', '21-Apr-2024 01:18 PM', ''),
(4, 'សម្រាកបិតុភាព', 0, 'សម្រាកបាន៥ថ្ងៃ', '5', '21-Apr-2024 01:24 PM', ''),
(5, 'រៀបអាពាហ៍ពិពាហ៍ផ្ទាល់ខ្លួន', 0, 'សម្រាកបាន៥ថ្ងៃ', '5', '21-Apr-2024 01:25 PM', ''),
(6, 'មរណៈភាពញាតិលោហិត', 0, 'សម្រាកបាន៥ថ្ងៃ', '5', '21-Apr-2024 01:25 PM', ''),
(8, 'ផ្សេងៗ', 0, 'ក្នុងករណីមានធុរៈផ្ទាល់ខ្លួន ដូចជាការរៀបអាពាហ៍ពិពាហ៍កូនបង្កើត កូនចិញ្ចឹម បងប្អូន ឬអ្នកនៅក្នុងបន្ទុកផ្ទាល់របស់សមីជន ជាដើម។', '3', '21-Apr-2024 01:57 PM', '21-Apr-2024 02:35 PM');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotification`
--

DROP TABLE IF EXISTS `tblnotification`;
CREATE TABLE IF NOT EXISTS `tblnotification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userId` int NOT NULL,
  `tblleaveId` int NOT NULL,
  `approvedate` varchar(120) NOT NULL,
  `isread` int NOT NULL DEFAULT '1',
  `adminId` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tblnotification`
--

INSERT INTO `tblnotification` (`id`, `userId`, `tblleaveId`, `approvedate`, `isread`, `adminId`) VALUES
(1, 2, 3, '26-Mar-2024 07:09 AM ', 1, 3),
(2, 2, 3, '26-Mar-2024 07:18 AM ', 1, 6),
(3, 2, 3, '26-Mar-2024 07:20 AM ', 1, 7),
(4, 2, 3, '26-Mar-2024 07:22 AM ', 1, 8),
(5, 2, 3, '26-Mar-2024 07:24 AM ', 1, 9),
(6, 2, 3, '26-Mar-2024 07:31 AM ', 1, 10),
(7, 2, 1, '26-Mar-2024 10:20 AM ', 1, 3),
(8, 2, 1, '26-Mar-2024 10:22 AM ', 1, 6),
(9, 2, 1, '26-Mar-2024 10:22 AM ', 1, 7),
(10, 2, 1, '26-Mar-2024 10:23 AM ', 1, 8),
(11, 2, 2, '26-Mar-2024 10:25 AM ', 1, 3),
(12, 2, 2, '26-Mar-2024 10:25 AM ', 1, 6),
(13, 2, 2, '26-Mar-2024 10:26 AM ', 1, 7),
(14, 2, 2, '26-Mar-2024 10:26 AM ', 1, 8),
(15, 2, 2, '26-Mar-2024 10:27 AM ', 1, 9),
(16, 2, 2, '26-Mar-2024 10:28 AM ', 1, 10),
(17, 2, 3, '26-Mar-2024 02:21 PM ', 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tbloffices`
--

DROP TABLE IF EXISTS `tbloffices`;
CREATE TABLE IF NOT EXISTS `tbloffices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `OfficeName` varchar(255) NOT NULL,
  `HeadOfOffice` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `DepHeadOffice` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `CreationDate` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `UpdateAt` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tbloffices`
--

INSERT INTO `tbloffices` (`id`, `OfficeName`, `HeadOfOffice`, `DepHeadOffice`, `CreationDate`, `UpdateAt`) VALUES
(5, 'រដ្ឋបាល និងហិរញ្ញវត្ថុ', '', '', '04-Wed-2024 16:07 PM', ''),
(6, 'ផែនការ និងបណ្តុះបណ្តាល', '', '', '04-Wed-2024 16:40 PM', ''),
(7, 'សវនកម្មទី១', '', '', '04-Wed-2024 16:41 PM', ''),
(8, 'សវនកម្មទី២', '', '', '04-Wed-2024 16:41 PM', ''),
(9, 'សវនកម្មទី៣', '', '', '04-Wed-2024 16:41 PM', ''),
(10, 'សវនកម្មទី៤', '', '', '04-Wed-2024 16:41 PM', ''),
(11, 'គ្រប់គ្រងព័ត៌មានវិទ្យា', 'លោក ជា សេរីបណ្ឌិត', 'លោក​ ចិន​ វាសនា', '04-Thu-2024 14:10 PM', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblpermission`
--

DROP TABLE IF EXISTS `tblpermission`;
CREATE TABLE IF NOT EXISTS `tblpermission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `PermissionName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Assign_to` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `EngPerName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Status` int NOT NULL,
  `Icons` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `UserId` int NOT NULL,
  `CreateDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpermission`
--

INSERT INTO `tblpermission` (`id`, `PermissionName`, `Assign_to`, `EngPerName`, `Status`, `Icons`, `Category`, `UserId`, `CreateDate`) VALUES
(1, 'គ្រប់គ្រងគណនីមន្ត្រី', '', 'Manage Accounts', 0, 'bx bxs-user-account', 'Manage', 0, '19-Apr-2024'),
(2, 'គ្រប់គ្រងច្បាប់ឈប់សម្រាក', '', 'Manage Leaves', 0, 'bx bx-calendar-event', 'Manage', 0, '19-Apr-2024'),
(3, 'គ្រប់គ្រងលិខិតចេញ និងចូលយឺត', '', 'Manage Late', 0, 'bx bx-time', 'Manage', 0, '19-Apr-2024'),
(4, 'គ្រប់គ្រងឯកសារចេញ ចូល', '', 'Manage In Out Documents', 0, 'bx bx-calendar', 'Manage', 0, '19-Apr-2024'),
(5, 'គ្រប់គ្រងរបាយការសវនកម្មឌីជីថល', '', 'Manage Audit', 0, 'bx bx-file', 'Manage', 0, '19-Apr-2024'),
(6, 'ច្បាប់ឈប់សម្រាក', '', 'Leaves', 0, 'bx bx-calendar', '', 1, '19-Apr-2024'),
(7, 'លិខិតយឺត', '', 'Late Documents', 0, 'bx bx-time-five', '', 1, '19-Apr-2024'),
(9, 'គ្រប់គ្រងឯកសារចេញចូល (នាយកដ្ឋាន)', '', 'Manage In Out Documents (Department)', 0, 'bx bx-notepad', 'Manage', 0, '02-May-2024');

-- --------------------------------------------------------

--
-- Table structure for table `tblposition`
--

DROP TABLE IF EXISTS `tblposition`;
CREATE TABLE IF NOT EXISTS `tblposition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `PositionName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RoleId` int NOT NULL,
  `Permission` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `AssignTo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `CreationDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UpdateAt` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblposition`
--

INSERT INTO `tblposition` (`id`, `PositionName`, `RoleId`, `Permission`, `AssignTo`, `CreationDate`, `UpdateAt`) VALUES
(1, 'ប្រធានអង្គភាព', 2, '', '', '04-Mon-2024 14:10 PM', ''),
(2, 'អនុប្រធានអង្គភាព', 2, '', '', '04-Mon-2024 14:10 PM', ''),
(3, 'មន្ត្រីលក្ខន្តិកៈ', 3, '', '', '04-Mon-2024 14:11 PM', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblrole`
--

DROP TABLE IF EXISTS `tblrole`;
CREATE TABLE IF NOT EXISTS `tblrole` (
  `id` int NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Colors` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Rid` int NOT NULL,
  `Permission` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UserId` int NOT NULL,
  `CreationDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UpdateAt` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblrole`
--

INSERT INTO `tblrole` (`id`, `RoleName`, `Colors`, `Rid`, `Permission`, `UserId`, `CreationDate`, `UpdateAt`) VALUES
(1, 'Supper Admin', 'bg-label-primary', 7, '1, 2, 3, 4, 5, 6, 7', 0, '04-Mon-2024 14:07 PM', ''),
(2, 'Admin', 'bg-label-success', 7, '6, 7', 0, '04-Mon-2024 14:09 PM', ''),
(3, 'users', 'bg-label-warning', 7, '6, 7', 1, '04-Mon-2024 14:09 PM', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

DROP TABLE IF EXISTS `tbluser`;
CREATE TABLE IF NOT EXISTS `tbluser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Honorific` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `FirstName` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `LastName` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `UserName` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Profile` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `DateofBirth` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Gender` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Department` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Office` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Position` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `RoleId` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Permission` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Status` int NOT NULL,
  `CreationDate` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `UpdateAt` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `Honorific`, `FirstName`, `LastName`, `UserName`, `Profile`, `Email`, `Password`, `Contact`, `Address`, `DateofBirth`, `Gender`, `Department`, `Office`, `Position`, `RoleId`, `Permission`, `Status`, `CreationDate`, `UpdateAt`) VALUES
(4, 'កញ្ញា', 'សេង', 'ចន្ធី', 'chanthy', 'IMG_7718.JPG', 'chanthy@gmail.com', '25d55ad283aa400af464c76d713c07ad', '09876543', 'fsadfasdf', '30-Apr-2024', 'ស្រី', 'កិច្ចការទូទៅ', '5567c12b260815799de0b99d821d3707', 'មន្ត្រីលក្ខន្តិកៈ', '3', '1,2,3,4,5,6,7,9', 1, '30-Apr-2024 04:36 PM', ''),
(3, 'លោកស្រី', 'ស៊ាប', 'ម៉ីជីង', 'meiching', 'IMG_8317.JPG', 'meiching@gmail.com', '25d55ad283aa400af464c76d713c07ad', '09876543', 'wefwfasdf', '30-Apr-2024', 'ស្រី', 'កិច្ចការទូទៅ', '4ed17c4148120400befe3ceaec11e4fc', 'មន្ត្រីលក្ខន្តិកៈ', '3', '6,7', 1, '30-Apr-2024 04:34 PM', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
