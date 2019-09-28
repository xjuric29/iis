# ISS project
# Description: Init database scheme.
# Author: Jiri Jurica (xjuric29)

# DB creation.
DROP DATABASE IF EXISTS `iis`;
CREATE DATABASE `iis` CHARACTER SET = 'utf8' COLLATE = 'utf8_general_ci';
USE `iis`;
SET storage_engine=INNODB;

# User creation.
CREATE USER 'iis'@'localhost' IDENTIFIED BY 'the_best_project_ever';
GRANT ALL PRIVILEGES ON `iis`.* TO 'iis'@localhost IDENTIFIED BY 'the_best_project_ever';
FLUSH PRIVILEGES;