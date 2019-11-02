# IIS project
# Description: Init database scheme.
# Author: Jiri Jurica (xjuric29)

# DB creation.
DROP DATABASE IF EXISTS `iis`;
CREATE DATABASE `iis` CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';
USE `iis`;
SET storage_engine=INNODB;

# Tables.
CREATE TABLE `user` (
    `id` VARCHAR(32) NOT NULL,
    `first_name` VARCHAR(32) NOT NULL,
    `surname` VARCHAR(64) NOT NULL,
    `mail` VARCHAR(256) DEFAULT NULL,
    `hash` CHAR(60) NOT NULL,
    `deleted` BOOLEAN DEFAULT 0 NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE `company` (
    `id` INTEGER AUTO_INCREMENT,
    `name` VARCHAR(256) NOT NULL,
    `deleted` BOOLEAN DEFAULT 0 NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE `user_customer` (
    `id` VARCHAR(32) NOT NULL,
    `id_company` INTEGER,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id`)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,  # If I will delete a record from `user` table, the record with same `id` will be deleted from this table.
	FOREIGN KEY  (`id_company`)
		REFERENCES company(`id`)
);

CREATE TABLE `user_worker` (
    `id` VARCHAR(32) NOT NULL,
    `superior` VARCHAR(32) DEFAULT NULL,
    `role` ENUM('common_worker', 'manager', 'superior', 'administrator'),
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id`)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY(superior)
        REFERENCES user_worker(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE `product` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `id_company` INTEGER NOT NULL,
	`name` VARCHAR(128) NOT NULL,
    PRIMARY KEY(`id`),
	FOREIGN KEY (`id_company`)
		REFERENCES company(`id`)
);

CREATE TABLE `sub_product` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `product` INTEGER NOT NULL,
    `leader` VARCHAR(32) NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`product`)
        REFERENCES product(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY(`leader`)
        REFERENCES user_worker(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE `event` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `creation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `modify_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`)
);

CREATE TABLE `ticket` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `author` VARCHAR(32) NOT NULL,
    `sub_product` INTEGER NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT NOT NULL,
    `state` ENUM('new', 'in_progress', 'closed') DEFAULT 'new',
    `creation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `modify_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    FOREIGN KEY(author)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY(sub_product)
        REFERENCES sub_product(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
);

CREATE TABLE `event_ticket_comment` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `ticket` INTEGER NOT NULL,
    `author` VARCHAR(32) NOT NULL,
    `content` TEXT NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`ticket`)
        REFERENCES ticket(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY(author)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE `task` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `author` VARCHAR(32) NOT NULL,
    `ticket` INTEGER DEFAULT NULL,
    `worker` VARCHAR(32) NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT NOT NULL,
    `estimated_time` TIME NOT NULL,
    `state` ENUM('to_do', 'in_progress', 'done') DEFAULT 'to_do',
    PRIMARY KEY(`id`),
    FOREIGN KEY(author)
        REFERENCES user_worker(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY(ticket)
        REFERENCES ticket(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY(worker)
        REFERENCES user_worker(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE `event_progress_update` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `task` INTEGER NOT NULL,
    `worker` VARCHAR(32) NOT NULL,
    `description` TEXT NOT NULL,
    `time_from` TIMESTAMP NOT NULL,
    `time_to` TIMESTAMP NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(task)
        REFERENCES task(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY(worker)
        REFERENCES user_worker(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
)

# Events.