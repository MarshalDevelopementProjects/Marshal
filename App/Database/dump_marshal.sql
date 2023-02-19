DROP DATABASE IF EXISTS `marshal3_0`;

CREATE DATABASE IF NOT EXISTS `marshal3_0`;

USE `marshal3_0`;

CREATE TABLE `admin`(
	`id` INT AUTO_INCREMENT,
	`username` VARCHAR(15) NOT NULL UNIQUE,
	`first_name` VARCHAR(40) NOT NULL,
	`last_name` VARCHAR(40) NOT NULL,
	`email_address` VARCHAR(100) NOT NULL UNIQUE,
	`password` VARCHAR(136) NOT NULL,
	`street_address` VARCHAR(100) NOT NULL,
	`city` VARCHAR(50) NOT NULL,
	`country` VARCHAR(50) NOT NULL,
	`phone_number` VARCHAR(20) NOT NULL,
	`joined_datetime` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `username`, `email_address`, `street_address`, `city`, `country`, `password`, `phone_number`, `joined_datetime`) VALUES
('admin63cd29b9531c8', 'Adam', 'West', 'SysAdmin', 'adam_west@gmail.com', 'No. 23, Top street', 'York City', 'England', '$argon2id$v=19$m=65536,t=4,p=1$Vkg2Skx6MERFR0JUZ05kZQ$xvR7jk/Yo5waSWOV6/OUC3scLeNVZ2hs1mZ2YMD0xFI', '0709078923', '2023-01-22 12:20:28');

CREATE TABLE `user`(
	`id` INT AUTO_INCREMENT,
	`username` VARCHAR(20) NOT NULL UNIQUE,
	`email_address` VARCHAR(100) NOT NULL UNIQUE,
	`first_name` VARCHAR(40) NOT NULL, 
	`last_name` VARCHAR(40) NOT NULL,
	`password` VARCHAR(136) NOT NULL,
	`user_status` ENUM("Available", "Idle", "Busy") NOT NULL DEFAULT "Available",
	`phone_number` VARCHAR(20) NOT NULL DEFAULT "000-0000000",
	`position` TEXT NOT NULL,
	`bio` TEXT NOT NULL,
	`user_state` ENUM("OFFLINE", "ONLINE") NOT NULL DEFAULT "OFFLINE",
	`joined_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	`profile_picture` VARCHAR(255) NOT NULL DEFAULT "/App/Database/Uploads/ProfilePictures/default-profile-picture.jpg",
	`access` ENUM("ENABLED", "DISABLED") NOT NULL DEFAULT "ENABLED",
	`verified` ENUM("TRUE", "FALSE") NOT NULL DEFAULT "FALSE",
	`verification_code` VARCHAR(100) DEFAULT "-",
	PRIMARY KEY (`id`)
);

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

CREATE TABLE `project`(
	`id` INT AUTO_INCREMENT,
	`created_by` INT NOT NULL,
	`project_name` VARCHAR(100) NOT NULL,
	`description` TEXT NOT NULL,
	`field` VARCHAR(20) NOT NULL,
	`start_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	`end_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	`created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	`archived` BOOLEAN NOT NULL DEFAULT 0,
	FOREIGN KEY (`created_by`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY(`id`)
);

CREATE TABLE `project_join`(
	`project_id` INT,
	`member_id` INT,
	`role` ENUM('LEADER', 'MEMBER', 'CLIENT'),
	`joined` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (`project_id`) REFERENCES `project`(`id`)  ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`member_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`project_id`, `member_id`)
);

DELIMITER $$

CREATE TRIGGER `join_leader_to_project_join_on_project_creation`
AFTER INSERT ON `project`
FOR EACH ROW
BEGIN
    INSERT INTO `project_join`(`project_id`, `member_id`, `role`) VALUES(NEW.`id`, NEW.`created_by`, "LEADER");
END$$

DELIMITER ;

CREATE TABLE `task`(
	`task_id` INT AUTO_INCREMENT,
	`task_name` VARCHAR(100) NOT NULL,
	`status` ENUM("TO-DO", "DONE", "ONGOING", "REVIEW") NOT NULL DEFAULT "TO-DO",
	`description` TEXT NOT NULL,
	`deadline` DATETIME NOT NULL,
	`project_id` INT NOT NULL,
	`member_id` INT DEFAULT NULL,
	`priority` ENUM("low", "high", "medium") NOT NULL,
	`assign_type` ENUM("member", "group") NOT NULL,
	`task_type` ENUM("project", "group") NOT NULL,
	FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`task_id`)
);

-- This table needs a file location string as well to get the file that was uploaded

CREATE TABLE `completedtask`(
	`task_id` INT,
	`confirmation_type` ENUM("message", "file") NOT NULL DEFAULT "message",
	`confirmation_message` TEXT NOT NULL,
	FOREIGN KEY (`task_id`) REFERENCES `task`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`task_id`)
);

-- group_name unique?
CREATE TABLE `groups`(
	`id` INT AUTO_INCREMENT,
	`group_name` VARCHAR(100) NOT NULL,
	`task_name` VARCHAR(100) NOT NULL,
	`description` TEXT NOT NULL,
	`project_id` INT NOT NULL,
	`leader_id` INT NOT NULL,
	FOREIGN KEY (`task_name`) REFERENCES `task`(`task_name`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`leader_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY(`id`)
);

CREATE TABLE `group_join`(
	`group_id` INT,
	`member_id` INT,
	`role` ENUM("LEADER", "MEMBER") NOT NULL,
	`joined` DATETIME,
	FOREIGN KEY (`member_id`) REFERENCES `project_join`(`member_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`group_id`, `member_id`)
);

CREATE TABLE `group_task`(
	`task_id` INT,
	`group_id` INT,
	FOREIGN KEY (`task_id`) REFERENCES `task`(`task_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`task_id`, `group_id`)
);

CREATE TABLE `notification`(
	`id` INT AUTO_INCREMENT,
	`projectId` INT NOT NULL,
	`message` TEXT NOT NULL,
	`sendTime` DATETIME,
	`senderId` INT NOT NULL,
	`type` ENUM("request", "notification"),
	`sendTime` DATETIME NOT NULL,
	`url` VARCHAR(100) DEFAULT NULL,
	FOREIGN KEY (`projectId`) REFERENCES `project`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`senderId`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `notification_recievers`(
	`id` INT AUTO_INCREMENT,
	`notificationId` INT NOT NULL,
	`memberId` INT NOT NULL,
	`isRead`TINYINT(1) DEFAULT 0,
	FOREIGN KEY (`notificationId`) REFERENCES `notification`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`memberId`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `message`(
	`id` INT AUTO_INCREMENT,
	`sender_id` INT NOT NULL,
	`stamp` DATETIME NOT NULL,
	`message_type` ENUM("CLIENT_P_LEADER_M", "PROJECT_MESSAGE", "GROUP_MESSAGE") NOT NULL,
	`msg` TEXT NOT NULL,
	FOREIGN KEY (`sender_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`id`)
);

-- this is a one to one 
-- make join instead of a nested query
CREATE TABLE `client_project_message`(
	`message_id` INT NOT NULL,
	`project_id` INT NOT NULL,
	`project_leader_id` INT NOT NULL,
	FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`project_leader_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`message_id`)
);

CREATE TABLE `project_message`(
	`message_id` INT NOT NULL,
	`project_id` INT NOT NULL,
	FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`message_id`)
);

CREATE TABLE `group_message`(
	`message_id` INT NOT NULL,
	`group_id` INT NOT NULL,
	FOREIGN KEY (`message_id`) REFERENCES `message`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`message_id`)
);
