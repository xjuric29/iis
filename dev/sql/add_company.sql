# IIS project
# Description: Add table for companies
# Author: Michal Pospíšil (xpospi95)

USE `iis`;

CREATE OR REPLACE TABLE `company` (
    `id` INTEGER AUTO_INCREMENT,
    `name` VARCHAR(256) NOT NULL,
    `deleted` BOOLEAN DEFAULT 0 NOT NULL,
    PRIMARY KEY(`id`)
);

ALTER TABLE `user_customer`
	ADD id_company INTEGER NOT NULL,
	DROP COLUMN company,
	FOREIGN KEY (`id_company`)
		REFERENCES company(`id`);

ALTER TABLE `product`
	ADD id_company INTEGER NOT NULL,
	FOREIGN KEY (`id_company`)
		REFERENCES company(`id`);