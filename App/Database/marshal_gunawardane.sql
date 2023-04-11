-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2023 at 04:24 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
-- Table structure for table `addmessagerefference`
--

CREATE TABLE `addmessagerefference` (
  `message_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `client_feedback`
--
-- Error reading structure for table marshal2_0.client_feedback: #1932 - Table &#039;marshal2_0.client_feedback&#039; doesn&#039;t exist in engine
-- Error reading data for table marshal2_0.client_feedback: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `marshal2_0`.`client_feedback`&#039; at line 1

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
(17, 'message', 'ok', '2023-03-04', '12:30:07'),
(18, 'message', 'I have completed', '2023-02-28', '04:35:51');

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
  `leader_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `task_name`, `description`, `project_id`, `leader_id`, `start_date`) VALUES
(1, 'Build prototype from rasberipi', 'Build prototype from rasberipi', 'Build prototype from rasberipi circuit. Because we have to use little bit more power than arduino bo', 4, 1, '2023-02-25 03:39:28'),
(2, 'ss', 'ss', 'cs', 4, 0, '2023-03-09 10:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `group_announcement`
--

CREATE TABLE `group_announcement` (
  `message_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_announcement`
--

INSERT INTO `group_announcement` (`message_id`, `project_id`, `heading`) VALUES
(39, 4, 'Medicare App Development'),
(40, 4, 'Web App development');

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
(1, 1, 'LEADER', '2023-02-24 23:09:28'),
(2, 1, 'LEADER', '2023-03-09 06:08:10');

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
(23, 1),
(24, 1);

-- --------------------------------------------------------

--
-- Table structure for table `group_task_feedback_message`
--

CREATE TABLE `group_task_feedback_message` (
  `message_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_task_feedback_message`
--

INSERT INTO `group_task_feedback_message` (`message_id`, `project_id`, `task_id`, `group_id`) VALUES
(44, 4, 23, 1),
(47, 4, 23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `stamp` datetime NOT NULL,
  `message_type` enum('PROJECT_MESSAGE','PROJECT_FEEDBACK_MESSAGE','GROUP_MESSAGE','GROUP_FEEDBACK_MESSAGE','PROJECT_TASK_FEEDBACK_MESSAGE','PROJECT_ANNOUNCEMENT','GROUP_ANNOUNCEMENT','GROUP_TASK_FEEDBACK_MESSAGE') NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `sender_id`, `stamp`, `message_type`, `msg`) VALUES
(1, 1, '2023-02-24 23:32:37', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'helo'),
(2, 1, '2023-02-24 23:33:15', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'helo'),
(3, 1, '2023-02-24 23:33:22', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hello'),
(4, 1, '2023-02-24 23:34:20', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'yyy'),
(5, 1, '2023-02-24 23:34:25', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'yyy'),
(6, 1, '2023-02-24 23:34:33', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'ww'),
(7, 1, '2023-02-24 23:37:14', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'kk'),
(8, 1, '2023-02-25 02:22:37', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'ppppppppppp'),
(9, 1, '2023-02-25 02:23:27', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'ggg'),
(10, 1, '2023-02-25 02:23:33', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'yyyyy'),
(11, 1, '2023-02-25 02:38:27', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hello'),
(12, 1, '2023-02-25 02:38:36', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hell'),
(13, 1, '2023-02-25 02:40:07', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'jj'),
(14, 1, '2023-02-25 02:40:56', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'yyy'),
(15, 1, '2023-02-25 02:41:03', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'ddd'),
(16, 1, '2023-02-25 02:42:02', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'ww'),
(17, 1, '2023-02-25 02:43:45', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'oo'),
(18, 1, '2023-02-25 02:43:52', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'oo'),
(19, 1, '2023-02-25 02:44:15', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'rrr'),
(20, 1, '2023-02-25 02:45:19', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'eee'),
(21, 1, '2023-02-25 03:03:23', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hee'),
(26, 1, '2023-03-03 11:44:15', 'PROJECT_ANNOUNCEMENT', 'develop cleaning robot for cleaning the environment, So it is best thing to do for the environment as technology. Because we are in big trouble that we don\'t see. So that\'s the time change the world as nature friendly and that\'s the time to'),
(27, 1, '2023-03-03 13:19:48', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'How is going'),
(28, 1, '2023-03-04 12:47:31', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'good evening'),
(29, 1, '2023-03-04 12:47:32', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'good evening'),
(30, 1, '2023-03-04 12:47:48', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(31, 1, '2023-03-04 12:47:48', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(32, 1, '2023-03-04 12:47:48', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(33, 1, '2023-03-04 12:47:48', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(34, 1, '2023-03-04 12:47:49', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(35, 1, '2023-03-05 01:44:45', '', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own homes without visiting hospitals. convenience of their own homes without visiting hospitals.'),
(36, 1, '2023-03-05 01:46:48', 'GROUP_ANNOUNCEMENT', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own homes without visiting hospitals. convenience of their own homes without visiting hospitals.'),
(37, 1, '2023-03-05 01:47:07', 'GROUP_ANNOUNCEMENT', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own homes without visiting hospitals. convenience of their own homes without visiting hospitals.'),
(38, 1, '2023-03-05 01:48:43', 'GROUP_ANNOUNCEMENT', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own h'),
(39, 1, '2023-03-05 01:49:48', 'GROUP_ANNOUNCEMENT', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own h'),
(40, 1, '2023-03-05 01:50:24', 'GROUP_ANNOUNCEMENT', 'mHealth apps between providers and patients. Patients can access and monitor their medical records/prescription details from the convenience of their own homes without visiting hospitals. convenience of their own homes without visiting hospitals.'),
(41, 1, '2023-03-08 10:15:32', '', 'hi'),
(42, 1, '2023-03-08 23:24:09', '', 'gm'),
(43, 1, '2023-03-08 23:25:03', '', 'hi'),
(44, 1, '2023-03-08 23:26:38', 'GROUP_TASK_FEEDBACK_MESSAGE', 'gm'),
(45, 1, '2023-03-09 06:03:17', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(46, 1, '2023-03-09 06:07:05', 'PROJECT_ANNOUNCEMENT', 'develop cleaning robot for cleaning the environment, So it is best thing to do for the environment as technology. Because we are in big trouble that we don\'t see. So that\'s the time change the world as nature friendly and that\'s the time to'),
(47, 1, '2023-03-09 06:09:22', 'GROUP_TASK_FEEDBACK_MESSAGE', 'hi'),
(48, 1, '2023-04-09 05:19:18', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(49, 1, '2023-04-11 16:21:55', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hi'),
(50, 1, '2023-04-11 16:22:57', 'PROJECT_TASK_FEEDBACK_MESSAGE', 'hey');

-- --------------------------------------------------------

--
-- Table structure for table `message_notification`
--
-- Error reading structure for table marshal2_0.message_notification: #1932 - Table &#039;marshal2_0.message_notification&#039; doesn&#039;t exist in engine
-- Error reading data for table marshal2_0.message_notification: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `marshal2_0`.`message_notification`&#039; at line 1

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
  `senderId` int(11) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `projectId`, `message`, `type`, `sendStatus`, `sendTime`, `senderId`, `url`) VALUES
(1, 4, 'How is going', 'notificati', 0, '2023-03-03 13:19:48.000000', 1, 'http://localhost/public/user/project?id=4'),
(2, 4, 'I pickup Sketch the circuit design.', 'notificati', 0, '2023-03-03 13:24:25.000000', 1, 'http://localhost/public/user/project?id=4'),
(3, 4, 'ok', 'notificati', 0, '2023-03-03 20:00:10.000000', 1, 'http://localhost/public/user/project?id=4'),
(4, 4, 'I pickup Build circuit for night rider light set.', 'notificati', 0, '2023-03-03 20:03:18.000000', 1, 'http://localhost/public/user/project?id=4'),
(6, 4, 'I pickup Deploy the APP.', 'notificati', 0, '2023-03-04 05:46:06.000000', 1, 'http://localhost/public/user/project?id=4'),
(7, 4, 'good evening', 'notificati', 0, '2023-03-04 12:47:31.000000', 1, 'http://localhost/public/user/project?id=4'),
(8, 4, 'good evening', 'notificati', 0, '2023-03-04 12:47:32.000000', 1, 'http://localhost/public/user/project?id=4'),
(9, 4, 'hi', 'notificati', 0, '2023-03-04 12:47:48.000000', 1, 'http://localhost/public/user/project?id=4'),
(10, 4, 'hi', 'notificati', 0, '2023-03-04 12:47:48.000000', 1, 'http://localhost/public/user/project?id=4'),
(11, 4, 'hi', 'notificati', 0, '2023-03-04 12:47:48.000000', 1, 'http://localhost/public/user/project?id=4'),
(12, 4, 'hi', 'notificati', 0, '2023-03-04 12:47:48.000000', 1, 'http://localhost/public/user/project?id=4'),
(13, 4, 'hi', 'notificati', 0, '2023-03-04 12:47:49.000000', 1, 'http://localhost/public/user/project?id=4'),
(14, 4, 'hi', 'notificati', 0, '2023-03-08 10:15:32.000000', 1, 'http://localhost/public/projectmember/group?id=1'),
(15, 4, 'gm', 'notificati', 0, '2023-03-08 23:24:09.000000', 1, 'http://localhost/public/projectmember/group?id=1'),
(16, 4, 'hi', 'notificati', 0, '2023-03-08 23:25:03.000000', 1, 'http://localhost/public/projectmember/group?id=1'),
(17, 4, 'gm', 'notificati', 0, '2023-03-08 23:26:39.000000', 1, 'http://localhost/public/projectmember/group?id=1'),
(18, 4, 'hi', 'notificati', 0, '2023-03-09 06:03:17.000000', 1, 'http://localhost/public/user/project?id=4'),
(19, 4, 'hi', 'notificati', 0, '2023-03-09 06:09:22.000000', 1, 'http://localhost/public/projectmember/group?id=1'),
(20, 4, 'hi', 'notificati', 0, '2023-04-09 05:19:19.000000', 1, 'http://localhost/public/user/project?id=4'),
(21, 4, 'I pickup Design the model of robot.', 'notificati', 0, '2023-04-09 05:35:30.000000', 1, 'http://localhost/public/user/project?id=4'),
(22, 4, 'hey', 'notificati', 0, '2023-04-11 16:22:57.000000', 1, 'http://localhost/public/user/project?id=4');

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
(1, 1, 4, 1),
(2, 2, 1, 1),
(3, 3, 1, 1),
(4, 4, 1, 1),
(5, 5, 1, 0),
(6, 6, 1, 0),
(7, 7, 1, 0),
(8, 8, 4, 0),
(9, 9, 1, 0),
(10, 9, 4, 0),
(11, 9, 1, 0),
(12, 9, 4, 0),
(13, 13, 1, 0),
(14, 14, 1, 0),
(15, 15, 1, 0),
(16, 16, 1, 0),
(17, 17, 1, 0),
(18, 18, 1, 0),
(19, 19, 1, 0),
(20, 20, 1, 0),
(21, 21, 1, 0),
(22, 22, 1, 0);

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
(4, 1, 'Develop cleaning robot', 'develop cleaning robot for cleaning the environment, So it is best thing to do for the environment as technology. Because we are in big trouble that we don&#039;t see. So that&#039;s the time change the world as nature friendly and that&#039;s the time to', 'Robotics', '2023-02-24 22:04:29', '2023-02-24 22:04:29', '2023-02-24 22:04:29');

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
-- Table structure for table `project_announcement`
--

CREATE TABLE `project_announcement` (
  `message_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_announcement`
--

INSERT INTO `project_announcement` (`message_id`, `project_id`, `heading`) VALUES
(26, 4, 'Develop cleaning robot'),
(46, 4, 'cleaning robot');

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
(2, 1, 'LEADER', '2023-01-22 09:00:04'),
(4, 1, 'LEADER', '0000-00-00 00:00:00'),
(4, 3, 'MEMBER', '2023-02-28 00:04:41'),
(4, 4, 'MEMBER', '2023-03-03 12:52:56');

-- --------------------------------------------------------

--
-- Table structure for table `project_task_feedback_message`
--

CREATE TABLE `project_task_feedback_message` (
  `message_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_task_feedback_message`
--

INSERT INTO `project_task_feedback_message` (`message_id`, `project_id`, `task_id`) VALUES
(27, 4, 21),
(28, 4, 19),
(29, 4, 21),
(30, 4, 19),
(34, 4, 25),
(45, 4, 19),
(48, 4, 19),
(49, 4, 19),
(50, 4, 19);

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
(17, 'REVIEW', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-28 00:00:00', 'Sketch the circuit design', 'member', 1, 'high', 4, 'project'),
(18, 'DONE', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-27 00:00:00', 'Design the shape of robot', 'member', 3, 'medium', 4, 'project'),
(19, 'ONGOING', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-27 00:00:00', 'Design the cleaning hand of the rovot', 'member', 1, 'low', 4, 'project'),
(20, 'TO-DO', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-27 00:00:00', 'Analysis the metal of robot', 'member', NULL, 'high', 4, 'project'),
(21, 'ONGOING', 'Complete project info UI. And combine it with the backend part with getting support', '2023-02-27 00:00:00', 'Build prototype from tinkercad using arduino board', 'member', 4, 'low', 4, 'project'),
(22, 'ONGOING', 'Build prototype from rasberipi circuit. Because we have to use little bit more power than arduino board', '0000-00-00 00:00:00', 'Build prototype from rasberipi', 'group', 1, 'high', 4, 'project'),
(23, 'ONGOING', 'Build circuit for night rider light set using ardiuno uno board', '2023-03-26 00:00:00', 'Build circuit for night rider light set', 'member', 1, 'medium', 4, 'group'),
(24, 'ONGOING', 'Design the model of robot with in next two days', '2023-03-22 00:00:00', 'Design the model of robot', 'member', 1, 'high', 4, 'group'),
(25, 'ONGOING', 'Deploy the APP', '2023-03-15 00:00:00', 'Deploy the APP', 'member', 1, 'low', 4, 'project'),
(26, 'ONGOING', 'cs', '0000-00-00 00:00:00', 'ss', 'group', 1, 'high', 4, 'project');

-- --------------------------------------------------------

--
-- Table structure for table `task_notification`
--

CREATE TABLE `task_notification` (
  `notification_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_notification`
--

INSERT INTO `task_notification` (`notification_id`, `task_id`) VALUES
(2, 17),
(3, 17),
(7, 19),
(9, 19),
(18, 19),
(20, 19),
(22, 19),
(1, 21),
(8, 21),
(4, 23),
(14, 23),
(15, 23),
(16, 23),
(17, 23),
(19, 23),
(21, 24),
(6, 25),
(13, 25);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email_address`, `first_name`, `last_name`, `password`, `user_status`, `phone_number`, `position`, `bio`, `user_state`, `joined_datetime`, `profile_picture`, `access`, `verified`, `verification_code`) VALUES
(1, 'kylo_ren', 'kylo_ren@gmail.com', 'Kylo', 'Solo', '$argon2id$v=19$m=65536,t=4,p=1$T29TaEFWUmxMNFcwdk5xRw$fGa/V3uIzKUPWqvjhGgf0b8JH0seEruV8URDiLgwBBA', 'Busy', '0789902124', 'System architect', 'I am Kylo Ren, I work at Amazon and I am system architect there. Interested in creating well documented secure systems. And believe it or not I love Rust and Haskell.', 'ONLINE', '2023-01-24 07:39:21', '/App/Database/Uploads/ProfilePictures/unvicio_Ugly_chicken_squab_sun_glasses_aviator_under_a_shower_o_14945a3b-b486-4ec4-aa9f-a34ea5e6ae6c.png', 'ENABLED', 'TRUE', '_'),
(2, 'ed_north', 'ed_north@gmail.com', 'Edward', 'North', '$argon2id$v=19$m=65536,t=4,p=1$RTE0aEVKUjdGb3JoSHpYTA$mMVMajgYnmFXgf6a1ET+GnUX7KNsblCLlqiwnSPPm0M', 'Idle', '0789905105', 'System Architect', 'I am Edward North, I work at Amazon and I am devops engineer there.\r\n                  Interested in creating well documented secure systems.\r\n                  And believe it or not I love Rust and Haskell', 'ONLINE', '2023-01-24 07:39:35', '/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg', 'DISABLED', 'TRUE', '_'),
(3, 'mrgunawardane@gmail.com', 'mrgunawardane@gmail.com', 'Harsha', 'gunawardane', '$argon2id$v=19$m=65536,t=4,p=1$OVp5MWlIcE55dWR5ajdrQg$WY57q9g9iz1D2gSQXWlKhoWmEKoVPRwn5Aw7Hn3ZIdI', 'Available', '', 'position(s) that you hold', '', 'ONLINE', '2023-02-27 23:03:19', '/App/Database/Uploads/ProfilePictures/picture3.png', 'ENABLED', 'TRUE', '495191'),
(4, 'chathuraharsha09@gmail.com', 'chathuraharsha09@gmail.com', 'Harsha', 'Gunawardane', '$argon2id$v=19$m=65536,t=4,p=1$c0l6MFRaRW1MWURLSlFQNA$nFtpwl/YfusCzhJwfUS6m3cv3Af6PrYFGbzXEkpsSVo', 'Available', '', 'position(s) that you hold', '', 'ONLINE', '2023-03-03 11:40:43', '/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg', 'ENABLED', 'TRUE', '657532');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addmessagerefference`
--
ALTER TABLE `addmessagerefference`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `notification_id` (`notification_id`);

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
-- Indexes for table `group_announcement`
--
ALTER TABLE `group_announcement`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `project_id` (`project_id`);

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
-- Indexes for table `group_task_feedback_message`
--
ALTER TABLE `group_task_feedback_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

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
-- Indexes for table `project_announcement`
--
ALTER TABLE `project_announcement`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_join`
--
ALTER TABLE `project_join`
  ADD PRIMARY KEY (`project_id`,`member_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `role_index` (`role`),
  ADD KEY `joined_index` (`joined`);

--
-- Indexes for table `project_task_feedback_message`
--
ALTER TABLE `project_task_feedback_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `task_notification`
--
ALTER TABLE `task_notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notification_recievers`
--
ALTER TABLE `notification_recievers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addmessagerefference`
--
ALTER TABLE `addmessagerefference`
  ADD CONSTRAINT `addmessagerefference_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `addmessagerefference_ibfk_2` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_announcement`
--
ALTER TABLE `group_announcement`
  ADD CONSTRAINT `group_announcement_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_announcement_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group_task_feedback_message`
--
ALTER TABLE `group_task_feedback_message`
  ADD CONSTRAINT `group_task_feedback_message_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_task_feedback_message_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_task_feedback_message_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `group_task_feedback_message_ibfk_4` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_announcement`
--
ALTER TABLE `project_announcement`
  ADD CONSTRAINT `project_announcement_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_announcement_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_task_feedback_message`
--
ALTER TABLE `project_task_feedback_message`
  ADD CONSTRAINT `project_task_feedback_message_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_task_feedback_message_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_task_feedback_message_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task_notification`
--
ALTER TABLE `task_notification`
  ADD CONSTRAINT `task_notification_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_notification_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
