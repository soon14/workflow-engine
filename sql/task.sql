-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2017 at 08:42 PM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `workflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `TAS_UID` int(11) NOT NULL AUTO_INCREMENT,
  `step_name` varchar(100) NOT NULL,
  `PRO_UID` int(11) DEFAULT NULL,
  `TAS_DESCRIPTION` text,
  `TAS_TYPE` enum('NORMAL','SUBPROCESS','WEBENTRYEVENT','SERVICE-TASK','INTERMEDIATE-CATCH-TIMER-EVENT','INTERMEDIATE-THROW-EMAIL-EVENT','START-TIMER-EVENT','START-MESSAGE-EVENT','END-MESSAGE-EVENT','INTERMEDIATE-THROW-MESSAGE-EVENT','INTERMEDIATE-CATCH-MESSAGE-EVENT') DEFAULT NULL,
  `TAS_DURATION` double DEFAULT NULL,
  `TAS_DELAY_TYPE` varchar(30) DEFAULT NULL,
  `TAS_TYPE_DAY` char(1) DEFAULT NULL,
  `TAS_TIMEUNIT` enum('MINUTES','HOURS','DAYS','WEEKS','MONTHS') DEFAULT NULL,
  `TAS_PRIORITY_VARIABLE` varchar(100) DEFAULT NULL,
  `TAS_ASSIGN_TYPE` enum('BALANCED','MANUAL','EVALUATE','REPORT_TO','SELF_SERVICE','MULTIPLE_INSTANCE_VALUE_BASED','MULTIPLE_INSTANCE','AUTO_ASSIGN') DEFAULT NULL,
  `TAS_CAN_UPLOAD` varchar(20) DEFAULT NULL,
  `TAS_USER` varchar(32) DEFAULT NULL,
  `TAS_VIEW_UPLOAD` varchar(20) DEFAULT NULL,
  `TAS_CAN_CANCEL` varchar(20) DEFAULT NULL,
  `TAS_CAN_PAUSE` varchar(20) DEFAULT NULL,
  `TAS_CAN_SEND_MESSAGE` varchar(20) DEFAULT NULL,
  `TAS_CAN_DELETE_DOCS` varchar(20) DEFAULT NULL,
  `TAS_SELF_SERVICE` varchar(20) DEFAULT NULL,
  `TAS_START` varchar(20) DEFAULT NULL,
  `TAS_SELFSERVICE_TIMEOUT` int(11) DEFAULT NULL,
  `TAS_SELFSERVICE_TIME` int(11) DEFAULT NULL,
  `TAS_SELFSERVICE_TIME_UNIT` varchar(15) DEFAULT NULL,
  `TAS_SELFSERVICE_TRIGGER_UID` varchar(32) DEFAULT NULL,
  `TAS_SELFSERVICE_EXECUTION` varchar(15) DEFAULT NULL,
  `TAS_TRANSFER_FLY` varchar(25) NOT NULL,
  PRIMARY KEY (`TAS_UID`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`TAS_UID`, `step_name`, `PRO_UID`, `TAS_DESCRIPTION`, `TAS_TYPE`, `TAS_DURATION`, `TAS_DELAY_TYPE`, `TAS_TYPE_DAY`, `TAS_TIMEUNIT`, `TAS_PRIORITY_VARIABLE`, `TAS_ASSIGN_TYPE`, `TAS_CAN_UPLOAD`, `TAS_USER`, `TAS_VIEW_UPLOAD`, `TAS_CAN_CANCEL`, `TAS_CAN_PAUSE`, `TAS_CAN_SEND_MESSAGE`, `TAS_CAN_DELETE_DOCS`, `TAS_SELF_SERVICE`, `TAS_START`, `TAS_SELFSERVICE_TIMEOUT`, `TAS_SELFSERVICE_TIME`, `TAS_SELFSERVICE_TIME_UNIT`, `TAS_SELFSERVICE_TRIGGER_UID`, `TAS_SELFSERVICE_EXECUTION`, `TAS_TRANSFER_FLY`) VALUES
(1, 'Brand Hopper', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(2, 'Account', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(3, 'Profile', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(4, 'Warning', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(5, 'Finish', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(6, 'MRF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(7, 'New Project', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(8, 'In Progress', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(9, 'Complete', 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(10, 'Start Sample', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(11, 'Received', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(12, 'In Test', 7, NULL, 'NORMAL', 1, '', '1', 'DAYS', NULL, 'AUTO_ASSIGN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, 'HOURS', '1', '1', 'FALSE'),
(13, 'Test Results', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(14, 'Complete', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(27, 'Start', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(29, 'Service\nTask', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(32, 'User Task', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(33, 'Task 1', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(34, 'Task 2', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(35, 'Start', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(36, 'Task 3', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(37, 'Parallel', 44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(38, 'Start', 8, NULL, 'NORMAL', 15, '', '1', 'HOURS', NULL, 'AUTO_ASSIGN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 1, 32, 'MINUTES', '1', '1', 'FALSE'),
(39, 'Received', 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(40, 'In Progress', 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(41, 'Complete', 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(42, 'Task', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(43, 'Task', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(44, 'Task', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(46, 'Start', 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(47, 'Task1', 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(48, 'Message Task', 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(49, 'Task 2', 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(50, 'Timer Task 1', 47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(51, 'Timer Task 2', 47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(52, 'Timer Task 3', 47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(53, 'Start', 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(54, 'Task', 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(55, 'Task', 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(56, 'Task', 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(57, 'Task', 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(58, 'Start', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(59, 'Task 1 mike', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(60, 'Task 2 lexi', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(61, 'Task 3 uan', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(66, 'Start', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(67, 'Task 1 mike', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(68, 'Task 2 lexi', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(69, 'Task 3 uan', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(74, 'Task 4', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(82, 'Task 3', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(83, 'Start', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(84, 'Task 1', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(85, 'Task 2', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE'),
(86, 'Task 3', 55, '', 'NORMAL', 1, '', '', 'DAYS', NULL, 'BALANCED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'FALSE', NULL, 0, 0, '', '', 'EVERY_TIME', 'FALSE');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
