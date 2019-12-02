# IIS project 2019
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

CREATE TABLE `user_customer` (
    `id` VARCHAR(32) NOT NULL,
    `company` VARCHAR(64) DEFAULT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id`)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE   # If I will delete a record from `user` table, the record with same `id` will be deleted from this table.
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
    `name` VARCHAR(128) NOT NULL,
    PRIMARY KEY(`id`)
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
    `id` INTEGER NOT NULL,
    `ticket` INTEGER NOT NULL,
    `author` VARCHAR(32) NOT NULL,
    `content` TEXT NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(id)
        REFERENCES event(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
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
    `id` INTEGER NOT NULL,
    `task` INTEGER NOT NULL,
    `worker` VARCHAR(32) NOT NULL,
    `description` TEXT NOT NULL,
    `time_from` TIMESTAMP NOT NULL,
    `time_to` TIMESTAMP NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(id)
        REFERENCES event(`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY(task)
        REFERENCES task(`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    FOREIGN KEY(worker)
        REFERENCES user(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

# Events.

# Triggers for creating record in table 'event' when is inserted to table 'event_progress_update' or
# 'event_ticket_comment'.
CREATE OR REPLACE TRIGGER `create_event_from_progress`
    BEFORE INSERT ON event_progress_update FOR EACH ROW
    BEGIN
        INSERT INTO event (creation_date, modify_date) VALUES(CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
        SET NEW.id := LAST_INSERT_ID();
    END;

CREATE OR REPLACE TRIGGER `create_event_from_comment`
    BEFORE INSERT ON event_ticket_comment FOR EACH ROW
    BEGIN
        INSERT INTO event (creation_date, modify_date) VALUES(CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
        SET NEW.id := LAST_INSERT_ID();
    END;

# Triggers updating event modify time
CREATE OR REPLACE TRIGGER `update_modify_date_for_progress`
    AFTER UPDATE ON event_progress_update FOR EACH ROW
    BEGIN
        UPDATE event, event_progress_update SET event.modify_date=CURRENT_TIMESTAMP WHERE event.id = event_progress_update.id;
    END;

CREATE OR REPLACE TRIGGER `update_modify_date_for_comment`
    AFTER UPDATE ON event_ticket_comment FOR EACH ROW
    BEGIN
        UPDATE event, event_ticket_comment SET event.modify_date=CURRENT_TIMESTAMP WHERE event.id = event_ticket_comment.id;
    END;