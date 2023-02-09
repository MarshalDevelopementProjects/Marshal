-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2023 at 02:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marshal2_0`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `username` varchar(40) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `street_address` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `password` varchar(136) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `joined_datetime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `username`, `email_address`, `street_address`, `city`, `country`, `password`, `phone_number`, `joined_datetime`) VALUES
(0, 'Adam', 'West', 'SysAdmin', 'adam_west@gmail.com', 'No. 23, Top street', 'York City', 'England', '$argon2id$v=19$m=65536,t=4,p=1$Vkg2Skx6MERFR0JUZ05kZQ$xvR7jk/Yo5waSWOV6/OUC3scLeNVZ2hs1mZ2YMD0xFI', '0709078923', '2023-01-22 12:20:28');

-- --------------------------------------------------------

--
-- Table structure for table `completedtask`
--

CREATE TABLE `completedtask` (
  `taskId` int(11) NOT NULL,
  `confirmation_type` varchar(10) NOT NULL,
  `confirmation_message` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `completedtask`
--

INSERT INTO `completedtask` (`taskId`, `confirmation_type`, `confirmation_message`, `date`, `time`) VALUES
(1, 'message', 'I am leader and I am done it', '2023-02-08', '06:51:44'),
(2, 'message', 'I am Done', '2023-02-03', '11:29:17'),
(3, 'message', 'Done this as well', '2023-02-03', '11:29:54'),
(4, 'message', 'Done it man', '2023-02-03', '02:47:30'),
(5, 'file', '', '2023-02-08', '03:52:38'),
(6, 'file', '', '2023-02-08', '03:48:18'),
(7, 'message', 'hh', '2023-02-08', '07:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `project_id` int(11) NOT NULL,
  `leader_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `task_name`, `description`, `project_id`, `leader_id`) VALUES
(1, 'group_description', 'group_description', 'group_description', 1, 1),
(2, '', '', '', 1, 1),
(3, '', '', '', 1, 1),
(4, 'Develop report generator', 'build report generator', 'Develop report generator using mPDF library', 1, 1),
(5, 'Draw sketch for 3d design', 'Design 3d art', 'Design 3d art as rotating', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_join`
--

CREATE TABLE `group_join` (
  `group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `role` varchar(10) NOT NULL,
  `joined` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_join`
--

INSERT INTO `group_join` (`group_id`, `member_id`, `role`, `joined`) VALUES
(1, 1, 'LEADER', '2023-02-08 03:11:07'),
(1, 4, 'MEMBER', '2023-02-08 03:54:12'),
(2, 1, 'LEADER', '2023-02-08 03:11:07'),
(3, 1, 'LEADER', '2023-02-08 03:11:46'),
(4, 1, 'LEADER', '2023-02-08 03:11:46'),
(5, 1, 'LEADER', '2023-02-08 03:29:56');

-- --------------------------------------------------------

--
-- Table structure for table `group_task`
--

CREATE TABLE `group_task` (
  `task_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_task`
--

INSERT INTO `group_task` (`task_id`, `group_id`) VALUES
(9, 1),
(10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `sendStatus` int(11) NOT NULL DEFAULT 0,
  `sendTime` datetime(6) NOT NULL,
  `senderId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `projectId`, `message`, `type`, `sendStatus`, `sendTime`, `senderId`) VALUES
(1, 1, 'Invitation', 'request', 0, '2023-01-24 14:51:10.000000', 1),
(2, 1, 'I accept your invitation, So now I will a member of your project', 'notificati', 0, '2023-01-24 14:51:36.000000', 3),
(3, 1, 'You are assigned to Build API for meeting site by project leader', 'notificati', 0, '2023-01-25 02:16:17.000000', 1),
(4, 1, 'You are assigned to Build API by project leader', 'notificati', 0, '2023-01-25 02:20:39.000000', 1),
(5, 1, 'Invitation', 'request', 0, '2023-01-28 15:22:11.000000', 1),
(7, 1, 'I pickup .', 'notificati', 0, '2023-01-29 13:34:43.000000', 4),
(8, 1, 'I pickup Test notification.', 'notificati', 0, '2023-01-29 13:41:33.000000', 4),
(9, 1, 'I pickup Test Again.', 'notificati', 0, '2023-01-29 15:26:00.000000', 4),
(10, 1, 'I pickup again again test.', 'notificati', 0, '2023-01-29 15:28:46.000000', 4),
(11, 1, 'I pickup cant.', 'notificati', 0, '2023-01-29 15:31:41.000000', 4),
(12, 1, 'I pickup lllllllllllllllllllllllll.', 'notificati', 0, '2023-01-29 15:38:32.000000', 4),
(13, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-01-30 01:51:46.000000', 4),
(14, 1, 'I pickup Build Meeting app.', 'notificati', 0, '2023-01-30 02:07:48.000000', 4),
(15, 1, 'I pickup Build Meeting app.', 'notificati', 0, '2023-01-30 02:07:50.000000', 4),
(16, 1, 'I pickup Build meeting.', 'notificati', 0, '2023-01-30 02:09:56.000000', 4),
(17, 1, 'I pickup task.', 'notificati', 0, '2023-01-30 02:12:23.000000', 4),
(18, 1, 'I pickup Meeting Site.', 'notificati', 0, '2023-01-30 02:14:48.000000', 4),
(19, 1, 'I pickup Microsoft Edge.', 'notificati', 0, '2023-01-30 02:19:42.000000', 4),
(20, 1, 'Invitation', 'request', 0, '2023-01-30 07:48:28.000000', 1),
(21, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-30 07:56:41.000000', 5),
(22, 1, 'I pickup Meeting Site.', 'notificati', 0, '2023-01-30 16:00:58.000000', 4),
(23, 1, 'I pickup todo task.', 'notificati', 0, '2023-01-30 18:24:09.000000', 5),
(24, 1, 'I pickup leader task.', 'notificati', 0, '2023-01-30 18:47:21.000000', 1),
(25, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 03:05:12.000000', 4),
(26, 1, 'I pickup Laeder task 2.', 'notificati', 0, '2023-01-31 03:05:33.000000', 5),
(27, 1, 'I pickup Microsoft Edge.', 'notificati', 0, '2023-01-31 04:47:20.000000', 1),
(28, 1, 'I pickup todo task.', 'notificati', 0, '2023-01-31 04:48:20.000000', 1),
(29, 1, 'I pickup Laeder task 2.', 'notificati', 0, '2023-01-31 04:50:20.000000', 1),
(30, 1, 'I pickup Meeting Site.', 'notificati', 0, '2023-01-31 04:50:34.000000', 1),
(31, 1, 'I pickup Microsoft Edge.', 'notificati', 0, '2023-01-31 04:51:27.000000', 1),
(32, 1, 'I pickup todo task.', 'notificati', 0, '2023-01-31 04:51:29.000000', 1),
(33, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 04:53:15.000000', 1),
(34, 1, 'I pickup Laeder task 2.', 'notificati', 0, '2023-01-31 04:53:57.000000', 1),
(35, 1, 'I pickup Meeting Site.', 'notificati', 0, '2023-01-31 04:54:42.000000', 1),
(36, 1, 'I pickup Microsoft Edge.', 'notificati', 0, '2023-01-31 04:55:59.000000', 1),
(37, 1, 'I pickup todo task.', 'notificati', 0, '2023-01-31 04:56:00.000000', 1),
(38, 1, 'Leader assigned you to Profile Pic check.', 'notificati', 0, '2023-01-31 05:14:18.000000', 1),
(39, 1, 'Leader assigned you to Laeder task 2.', 'notificati', 0, '2023-01-31 05:15:31.000000', 1),
(40, 1, 'I pickup Laeder task 2.', 'notificati', 0, '2023-01-31 05:24:58.000000', 5),
(41, 1, 'I pickup Laeder task 2.', 'notificati', 0, '2023-01-31 05:25:03.000000', 5),
(42, 1, 'I pickup Meeting Site.', 'notificati', 0, '2023-01-31 06:58:50.000000', 4),
(43, 1, 'I pickup Microsoft Edge.', 'notificati', 0, '2023-01-31 06:58:53.000000', 4),
(44, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 06:59:06.000000', 4),
(45, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 06:59:09.000000', 4),
(46, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 06:59:11.000000', 4),
(47, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 06:59:15.000000', 4),
(48, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 06:59:19.000000', 4),
(49, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:02:56.000000', 4),
(50, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:02:57.000000', 4),
(51, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:03:00.000000', 4),
(52, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:03:18.000000', 4),
(53, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:03:23.000000', 4),
(54, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:03:26.000000', 4),
(55, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:03:28.000000', 4),
(56, 1, 'I pickup Profile Pic check.', 'notificati', 0, '2023-01-31 07:04:09.000000', 4),
(57, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-01-31 07:44:05.000000', 5),
(58, 1, 'I pickup Build API.', 'notificati', 0, '2023-01-31 16:32:37.000000', 5),
(59, 1, 'I pickup Test Pickup feature.', 'notificati', 0, '2023-01-31 18:34:03.000000', 5),
(60, 1, 'I pickup Too late.', 'notificati', 0, '2023-02-03 02:18:39.000000', 5),
(61, 1, 'I pickup Create Message.', 'notificati', 0, '2023-02-03 02:20:26.000000', 5),
(62, 1, 'I pickup Build web socket.', 'notificati', 0, '2023-02-03 02:33:01.000000', 4),
(63, 1, 'I pickup Check finish task.', 'notificati', 0, '2023-02-03 03:43:08.000000', 4),
(64, 1, 'I pickup Reverse the task.', 'notificati', 0, '2023-02-03 05:04:17.000000', 1),
(65, 1, 'I pickup Build a submarine.', 'notificati', 0, '2023-02-03 05:04:59.000000', 5),
(66, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-02-03 06:51:36.000000', 4),
(67, 1, 'I pickup Test Pickup feature.', 'notificati', 0, '2023-02-03 06:51:37.000000', 4),
(68, 1, 'I pickup Build web socket.', 'notificati', 0, '2023-02-03 06:54:28.000000', 4),
(69, 1, 'I pickup Fix bug #1.', 'notificati', 0, '2023-02-03 06:57:07.000000', 4),
(70, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-02-03 06:59:16.000000', 4),
(71, 1, 'I pickup Too late.', 'notificati', 0, '2023-02-03 06:59:53.000000', 4),
(72, 1, 'I pickup task.', 'notificati', 0, '2023-02-03 07:39:21.000000', 1),
(73, 1, 'I pickup task.', 'notificati', 0, '2023-02-03 07:39:29.000000', 1),
(74, 1, 'I pickup task.', 'notificati', 0, '2023-02-03 07:42:25.000000', 1),
(75, 1, 'I pickup task.', 'notificati', 0, '2023-02-03 10:06:29.000000', 1),
(76, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-02-03 10:13:28.000000', 1),
(77, 1, 'I pickup Complete Project details UI.', 'notificati', 0, '2023-02-03 10:13:34.000000', 1),
(78, 1, 'I pickup TODO task.', 'notificati', 0, '2023-02-03 10:17:28.000000', 4),
(79, 1, 'Invitation', 'request', 0, '2023-02-04 08:34:40.000000', 1),
(80, 1, 'I pickup Develop API 1.', 'notificati', 0, '2023-02-05 01:44:06.000000', 1),
(81, 1, 'I pickup Develop API 1.', 'notificati', 0, '2023-02-05 01:44:08.000000', 1),
(82, 1, 'I pickup Group task 2.', 'notificati', 0, '2023-02-08 07:27:41.000000', 1),
(83, 1, 'I pickup Build message page.', 'notificati', 0, '2023-02-08 11:18:16.000000', 5),
(84, 1, 'I pickup Build Group tasks Handler.', 'notificati', 0, '2023-02-08 11:22:35.000000', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notification_recievers`
--

CREATE TABLE `notification_recievers` (
  `id` int(11) NOT NULL,
  `notificationId` int(11) NOT NULL,
  `memberId` int(11) NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_recievers`
--

INSERT INTO `notification_recievers` (`id`, `notificationId`, `memberId`, `isRead`) VALUES
(1, 1, 3, 1),
(2, 2, 1, 1),
(3, 4, 3, 1),
(4, 5, 4, 1),
(6, 10, 1, 1),
(7, 13, 1, 1),
(8, 14, 1, 0),
(9, 15, 1, 0),
(10, 16, 1, 0),
(11, 17, 1, 0),
(12, 18, 1, 0),
(13, 19, 1, 0),
(14, 20, 5, 1),
(15, 21, 1, 0),
(16, 22, 1, 0),
(17, 23, 1, 0),
(18, 24, 1, 0),
(19, 25, 1, 0),
(20, 26, 1, 0),
(21, 27, 1, 0),
(22, 28, 1, 0),
(23, 29, 1, 0),
(24, 30, 1, 0),
(25, 31, 1, 0),
(26, 32, 1, 0),
(27, 33, 1, 0),
(28, 34, 1, 0),
(29, 35, 1, 0),
(30, 36, 1, 0),
(31, 37, 1, 0),
(32, 38, 4, 1),
(33, 39, 5, 1),
(34, 40, 1, 0),
(35, 41, 1, 0),
(36, 42, 1, 0),
(37, 43, 1, 0),
(38, 44, 1, 0),
(39, 45, 1, 0),
(40, 46, 1, 0),
(41, 47, 1, 0),
(42, 48, 1, 0),
(43, 49, 1, 0),
(44, 50, 1, 0),
(45, 51, 1, 0),
(46, 52, 1, 0),
(47, 53, 1, 0),
(48, 54, 1, 0),
(49, 55, 1, 0),
(50, 56, 1, 0),
(51, 57, 1, 0),
(52, 58, 1, 0),
(53, 59, 1, 0),
(54, 60, 1, 0),
(55, 61, 1, 0),
(56, 62, 1, 0),
(57, 63, 1, 0),
(58, 64, 1, 0),
(59, 65, 1, 0),
(60, 66, 1, 0),
(61, 67, 1, 0),
(62, 68, 1, 0),
(63, 69, 1, 0),
(64, 70, 1, 0),
(65, 71, 1, 0),
(66, 72, 1, 0),
(67, 73, 1, 0),
(68, 74, 1, 0),
(69, 75, 1, 0),
(70, 76, 1, 0),
(71, 77, 1, 0),
(72, 78, 1, 0),
(73, 79, 4, 0),
(74, 80, 1, 0),
(75, 81, 1, 0),
(76, 82, 1, 0),
(77, 83, 1, 0),
(78, 84, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `project_name` varchar(60) NOT NULL,
  `description` varchar(255) NOT NULL,
  `field` varchar(20) NOT NULL,
  `start_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `created_by`, `project_name`, `description`, `field`, `start_on`, `end_on`, `created_on`) VALUES
(1, 1, 'mHealth App development', 'mHealth apps facilitate engagement through effective patient-focused care, personalized experiences & knowledge sharing between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of thei', 'web', '2023-01-22 01:49:25', '2023-05-30 01:49:25', '2023-01-22 01:49:25'),
(2, 1, 'project 2', 'sd', 'web', '2023-01-22 03:30:04', '2023-02-16 03:29:39', '2023-01-22 03:30:04'),
(3, 1, 'sdadasd', 'sdfasasgdfhdhfrghs', 'asdasfsdf', '2023-02-09 13:06:38', '2023-02-09 13:06:38', '2023-02-09 13:06:38');

--
-- Triggers `project`
--
DELIMITER $$
CREATE TRIGGER `join_project_leader_trigger` AFTER INSERT ON `project` FOR EACH ROW BEGIN
    INSERT INTO `project_join`(`project_id`, `member_id`, `role`) VALUES(NEW.`id`, NEW.`created_by`, "LEADER");
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_join`
--

CREATE TABLE `project_join` (
  `project_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `role` enum('LEADER','MEMBER','CLIENT') DEFAULT 'MEMBER',
  `joined` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_join`
--

INSERT INTO `project_join` (`project_id`, `member_id`, `role`, `joined`) VALUES
(1, 1, 'LEADER', '2023-01-22 07:20:26'),
(1, 3, 'MEMBER', '2023-01-24 14:51:36'),
(1, 4, 'MEMBER', '2023-01-28 15:22:46'),
(1, 5, 'MEMBER', '2023-01-30 07:48:48'),
(2, 1, 'LEADER', '2023-01-22 09:00:04'),
(3, 1, 'LEADER', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'TO-DO',
  `description` varchar(255) NOT NULL,
  `deadline` datetime NOT NULL,
  `task_name` varchar(100) NOT NULL,
  `assign_type` varchar(20) NOT NULL DEFAULT 'member',
  `memberId` int(11) DEFAULT NULL,
  `priority` varchar(6) NOT NULL DEFAULT 'low',
  `project_id` int(11) NOT NULL,
  `task_type` varchar(7) NOT NULL DEFAULT 'project'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `status`, `description`, `deadline`, `task_name`, `assign_type`, `memberId`, `priority`, `project_id`, `task_type`) VALUES
(1, 'REVIEW', 'taskdescription', '2023-02-24 00:00:00', 'Complete Project details UI', 'member', 1, 'low', 1, 'project'),
(2, 'REVIEW', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-25 00:00:00', 'Complete Project details UI', 'member', 1, 'high', 1, 'project'),
(3, 'REVIEW', 'taskdescription', '2023-02-23 00:00:00', 'Too late', 'member', 4, 'medium', 1, 'project'),
(4, 'REVIEW', 'taskdescription', '2023-02-25 00:00:00', 'TODO task', 'member', 4, 'medium', 1, 'project'),
(5, 'DONE', 'Build Group tasks Handler', '2023-02-28 00:00:00', 'Build Group tasks Handler', 'member', 5, 'high', 1, 'project'),
(6, 'REVIEW', 'Build message page', '2023-02-28 00:00:00', 'Build message page', 'member', 5, 'medium', 1, 'project'),
(7, 'REVIEW', 'Develop report generator using mPDF library', '0000-00-00 00:00:00', 'build report generator', 'group', 1, 'high', 1, 'project'),
(8, 'ONGOING', 'Design 3d art as rotating', '0000-00-00 00:00:00', 'Design 3d art', 'group', 1, 'high', 1, 'project'),
(9, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'group task 1', 'member', NULL, 'low', 1, 'group'),
(10, 'ONGOING', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-15 00:00:00', 'Group task 2', 'member', 1, 'medium', 1, 'group'),
(11, 'TO-DO', 'taskdescription', '2023-02-22 00:00:00', 'Group Task 1', 'member', NULL, 'high', 1, 'group'),
(12, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'Connect the Client page', 'member', NULL, 'low', 1, 'project'),
(13, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-27 00:00:00', 'Connect PDF generator', 'member', NULL, 'medium', 1, 'project'),
(14, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'Build controller for charts', 'member', NULL, 'high', 1, 'project'),
(15, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'build the search bar', 'member', NULL, 'low', 1, 'project'),
(16, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'build Meeting app', 'member', NULL, 'high', 1, 'project');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `password` varchar(136) NOT NULL,
  `user_status` enum('Available','Idle','Busy') DEFAULT 'Available',
  `phone_number` varchar(20) NOT NULL,
  `position` varchar(40) DEFAULT 'position(s) that you hold',
  `bio` text NOT NULL,
  `user_state` enum('OFFLINE','ONLINE') NOT NULL DEFAULT 'OFFLINE',
  `joined_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) NOT NULL DEFAULT '/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg',
  `access` enum('ENABLED','DISABLED') NOT NULL,
  `verified` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `verification_code` varchar(100) NOT NULL DEFAULT '_'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email_address`, `first_name`, `last_name`, `password`, `user_status`, `phone_number`, `position`, `bio`, `user_state`, `joined_datetime`, `profile_picture`, `access`, `verified`, `verification_code`) VALUES
(1, 'kylo_ren', 'kylo_ren@gmail.com', 'Kylo', 'Solo', '$argon2id$v=19$m=65536,t=4,p=1$T29TaEFWUmxMNFcwdk5xRw$fGa/V3uIzKUPWqvjhGgf0b8JH0seEruV8URDiLgwBBA', 'Busy', '0789902124', 'System architect', 'I am Kylo Ren, I work at Amazon and I am system architect there. Interested in creating well documented secure systems. And believe it or not I love Rust and Haskell.', 'OFFLINE', '2023-01-24 07:39:21', '/App/Database/Uploads/ProfilePictures/unvicio_Ugly_chicken_squab_sun_glasses_aviator_under_a_shower_o_14945a3b-b486-4ec4-aa9f-a34ea5e6ae6c.png', 'ENABLED', 'TRUE', '_'),
(2, 'ed_north', 'ed_north@gmail.com', 'Edward', 'North', '$argon2id$v=19$m=65536,t=4,p=1$RTE0aEVKUjdGb3JoSHpYTA$mMVMajgYnmFXgf6a1ET+GnUX7KNsblCLlqiwnSPPm0M', 'Idle', '0789905105', 'System Architect', 'I am Edward North, I work at Amazon and I am devops engineer there.\r\n                  Interested in creating well documented secure systems.\r\n                  And believe it or not I love Rust and Haskell', 'ONLINE', '2023-01-24 07:39:35', '/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg', 'DISABLED', 'TRUE', '_');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `admin_id_index` (`id`),
  ADD KEY `admin_email` (`email_address`),
  ADD KEY `admin_username` (`username`);

--
-- Indexes for table `completedtask`
--
ALTER TABLE `completedtask`
  ADD PRIMARY KEY (`taskId`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_join`
--
ALTER TABLE `group_join`
  ADD PRIMARY KEY (`group_id`,`member_id`);

--
-- Indexes for table `group_task`
--
ALTER TABLE `group_task`
  ADD PRIMARY KEY (`task_id`,`group_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_recievers`
--
ALTER TABLE `notification_recievers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_name` (`project_name`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_name_index` (`project_name`),
  ADD KEY `field` (`field`);

--
-- Indexes for table `project_join`
--
ALTER TABLE `project_join`
  ADD PRIMARY KEY (`project_id`,`member_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `role_index` (`role`),
  ADD KEY `joined_index` (`joined`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `notification_recievers`
--
ALTER TABLE `notification_recievers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
