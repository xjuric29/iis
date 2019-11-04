# ISS project 2019
# Description: Create project db user.
# Author: Jiri Jurica (xjuric29)

CREATE USER 'iis'@'localhost' IDENTIFIED BY 'the_best_project_ever';
GRANT ALL PRIVILEGES ON `iis`.* TO 'iis'@localhost IDENTIFIED BY 'the_best_project_ever';
FLUSH PRIVILEGES;

# If you need to remove this user.
# DELETE FROM `mysql`.`user` WHERE `user` = 'iis' and `host` = 'localhost';