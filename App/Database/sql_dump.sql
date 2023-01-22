if exists drop database `marshal2_0`;

CREATE DATABASE `marshal2_0`;

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(40) NOT NULL UNIQUE,
    `email_address` VARCHAR(100) NOT NULL UNIQUE,
    `first_name` VARCHAR(40) NOT NULL,
    `last_name` VARCHAR(40) NOT NULL,
    `password` VARCHAR(136) NOT NULL,
    `status` ENUM("AVAILABLE", "UNAVAILABLE", "BUSY") DEFAULT "AVAILABLE",
    `joined_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `user_id`(`id`),
    INDEX `user_email`(`email_address`),
    INDEX `user_username`(`username`)
);

-- `verified` BOOLEAN NOT NULL,
-- `verification_code` BOOLEAN NOT NULL,

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin`(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(40) NOT NULL,
    `last_name` VARCHAR(40) NOT NULL,
    `username` VARCHAR(40) NOT NULL UNIQUE,
    `email_address` VARCHAR(100) NOT NULL UNIQUE,
    `street_address` VARCHAR(100) NOT NULL,
    `city` VARCHAR(50) NOT NULL,
    `country` VARCHAR(50) NOT NULL,
    `password` VARCHAR(136) NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `joined_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX `admin_id_index`(`id`),
    INDEX `admin_email`(`email_address`),
    INDEX `admin_username`(`username`)
);

-- `verified` BOOLEAN NOT NULL,
-- `verification_code` BOOLEAN NOT NULL

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project`(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_by` INT NOT NULL,
    `project_name` VARCHAR(60) NOT NULL UNIQUE,
    `description` VARCHAR(255) NOT NULL,
    `field` VARCHAR(20) NOT NULL,
    `start_on` TIMESTAMP NOT NULL DEFAULT NOW(),
    `end_on` TIMESTAMP NOT NULL DEFAULT NOW(),
    `created_on` TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (`created_by`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `project_name_index`(`project_name`), 
    INDEX `field`(`field`)
);

DROP TABLE IF EXISTS `project_join`;

CREATE TABLE `project_join`(
    `project_id` INT NOT NULL,
    `member_id` INT NOT NULL,
    `role` ENUM("LEADER", "MEMBER", "CLIENT") DEFAULT "MEMBER",
    `joined` TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (`member_id`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    PRIMARY KEY(`project_id`, `member_id`),
    INDEX `role_index`(`role`), 
    INDEX `joined_index`(`joined`)
);


DROP TABLE IF EXISTS `project_lists`; -- to store the data about different lists in a project => todo, review and so on

CREATE TABLE `project_list`();

DELIMITER $$

CREATE TRIGGER `join_project_leader_trigger`
AFTER INSERT 
ON `project`
FOR EACH ROW
BEGIN
    INSERT INTO `project_join`(`project_id`, `member_id`, `role`) VALUES(NEW.`id`, NEW.`created_by`, "LEADER");
END$$

DELIMITER ;

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task`(
    `id` INT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `list` VARCHAR(20) NOT NULL,
    `title` VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(255) NOT NULL,
);

-- DROP TABLE IF EXISTS `task`;
-- DROP TABLE IF EXISTS `task_join`;
