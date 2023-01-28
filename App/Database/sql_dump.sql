-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2023 at 01:58 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` varchar(18) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `username`, `email_address`, `street_address`, `city`, `country`, `password`, `phone_number`, `joined_datetime`) VALUES
('admin63cd29b9531c8', 'Adam', 'West', 'SysAdmin', 'adam_west@gmail.com', 'No. 23, Top street', 'York City', 'England', '$argon2id$v=19$m=65536,t=4,p=1$Vkg2Skx6MERFR0JUZ05kZQ$xvR7jk/Yo5waSWOV6/OUC3scLeNVZ2hs1mZ2YMD0xFI', '0709078923', '2023-01-22 12:20:28');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `created_by`, `project_name`, `description`, `field`, `start_on`, `end_on`, `created_on`) VALUES
(1, 1, 'Test the project creating functionality', 'Test the newly added project creating functionality, which will have potential bugs', 'IT and Technological', '2023-01-21 05:18:12', '2023-01-21 05:18:12', '2023-01-21 05:18:12'),
(2, 1, 'Add new functions to user controller and project controller', 'Create the Project controller class and add the additional functionlities to the user controller class', 'IT and Technological', '2023-01-21 05:33:21', '2023-01-21 05:33:21', '2023-01-21 05:33:21');

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
  `joined` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `project_join`
--

INSERT INTO `project_join` (`project_id`, `member_id`, `role`, `joined`) VALUES
(1, 1, 'LEADER', '2023-01-21 05:18:12'),
(2, 1, 'LEADER', '2023-01-21 05:33:21'),
(1, 2, 'MEMBER', '2023-01-22 07:16:34');

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
  `access` enum('ENABLED','DISABLED') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email_address`, `first_name`, `last_name`, `password`, `user_status`, `phone_number`, `position`, `bio`, `user_state`, `joined_datetime`, `profile_picture`, `access`) VALUES
(1, 'kylo_ren', 'kylo_ren@gmail.com', 'Kylo', 'Solo', '$argon2id$v=19$m=65536,t=4,p=1$T29TaEFWUmxMNFcwdk5xRw$fGa/V3uIzKUPWqvjhGgf0b8JH0seEruV8URDiLgwBBA', 'Busy', '0789902124', 'System architect', 'I am Kylo Ren, I work at Amazon and I am system architect there. Interested in creating well documented secure systems. And believe it or not I love Rust and Haskell.', 'OFFLINE', '2023-01-24 13:09:21', '/App/Database/Uploads/ProfilePictures/unvicio_Ugly_chicken_squab_sun_glasses_aviator_under_a_shower_o_14945a3b-b486-4ec4-aa9f-a34ea5e6ae6c.png', 'ENABLED'),
(2, 'ed_north', 'ed_north@gmail.com', 'Edward', 'North', '$argon2id$v=19$m=65536,t=4,p=1$RTE0aEVKUjdGb3JoSHpYTA$mMVMajgYnmFXgf6a1ET+GnUX7KNsblCLlqiwnSPPm0M', 'Idle', '0789905105', 'System Architect', 'I am Edward North, I work at Amazon and I am devops engineer there.\r\n                  Interested in creating well documented secure systems.\r\n                  And believe it or not I love Rust and Haskell', 'ONLINE', '2023-01-24 13:09:35', '/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg', 'ENABLED');

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `add_default_value_to_bio_if_null` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
  IF NEW.`bio` IS NULL THEN
    SET NEW.`bio` = 'Introduce yourself to the others.';
  END IF;
END
$$
DELIMITER ;

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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `user_id` (`id`),
  ADD KEY `user_email` (`email_address`),
  ADD KEY `user_phone_number` (`phone_number`),
  ADD KEY `user_position` (`position`),
  ADD KEY `user_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
