# IIS project
# Description: Fill database with sample data
# Author: Michal Pospíšil (xpospi95)

USE `iis`;

# Creates a complete worker - use from highest privilege to lowest
CREATE OR REPLACE PROCEDURE iis.create_worker (
	IN login VARCHAR(32),
	IN in_first_name VARCHAR(32),
	IN in_surname VARCHAR(64),
	IN in_mail VARCHAR(256),
	IN in_sup_mail VARCHAR(256),
	IN in_role ENUM('common_worker', 'manager', 'superior', 'administrator')
	)
BEGIN
    DECLARE sup_id VARCHAR(32);
	INSERT INTO user (`id` , `first_name`, `surname`, `mail`, `hash`)
		VALUES (login, in_first_name, in_surname, in_mail, 'sample-data');

	IF in_sup_mail IS NOT NULL THEN
	    SELECT id INTO sup_id FROM user WHERE mail=in_sup_mail;
    END IF;
	INSERT INTO user_worker (`id`, `superior`, `role`)
		VALUES (login, sup_id, in_role);
END;

# Creates a complete customer
CREATE OR REPLACE PROCEDURE iis.create_customer (
	IN login VARCHAR(32),
	IN in_first_name VARCHAR(32),
	IN in_surname VARCHAR(64),
	IN in_mail VARCHAR(256),
	IN in_company VARCHAR(128)
	)
BEGIN
	INSERT INTO user (`id` , `first_name`, `surname`, `mail`, `hash`)
		VALUES (login, in_first_name, in_surname, in_mail, 'sample-data');
	INSERT INTO user_customer (`id`, `company`)
		VALUES (login, in_company);
END;

# Create users
# Admins
CALL create_worker('mrkvicka', 'Peter', 'Mrkvička', 'pmrkvicka@example.com', NULL, 'administrator');
CALL create_worker('foelden', 'Harvey', 'Foelden', 'ceo@example.com', NULL, 'administrator');

# Superiors
CALL create_worker('motak', 'Gabriel', 'Moták', 'gmotak@example.com', NULL, 'superior');
CALL create_worker('fairfield', 'Jonathan', 'Fairfield', 'jfairfield@example.com', NULL, 'superior');
CALL create_worker('morgan', 'Marina', 'Morgan', 'mmorgan@example.com', NULL, 'superior');

# Managers
CALL create_worker('kandelaber', 'Martin', 'Kandeláber', 'mkandelaber@example.com', 'gmotak@example.com', 'manager');

CALL create_worker('zrubnova', 'Jolana', 'Zrubnová', 'jzrubnova@example.com', 'jfairfield@example.com', 'manager');
CALL create_worker('kovarcik', 'Andrej', 'Kovarčík', 'akovarcik@example.com', 'jfairfield@example.com', 'manager');
CALL create_worker('bros', 'Oto', 'Bros', 'obros@example.com', 'jfairfield@example.com', 'manager'); # No common workers

CALL create_worker('urbova', 'Helena', 'Urbová', 'hurbova@example.com', 'mmorgan@example.com', 'manager');
CALL create_worker('tousel', 'Wendell', 'Tousel', 'wtousel@example.com', 'mmorgan@example.com', 'manager');
CALL create_worker('kolicka', 'Mária', 'Kolická', 'mkolicka@example.com', 'mmorgan@example.com', 'manager');
CALL create_worker('calloway', 'Ben', 'Calloway', 'bcalloway@example.com', 'mmorgan@example.com', 'manager');

# Workers
CALL create_worker('smith', 'Bartholomew', 'Smith', 'bsmith@example.com', 'mkandelaber@example.com', 'common_worker');
CALL create_worker('gelet', 'Anton', 'Gelet', 'agelet@example.com', 'mkandelaber@example.com', 'common_worker');
CALL create_worker('barotova', 'Hana', 'Barotová', 'hbarotova@example.com', 'mkandelaber@example.com', 'common_worker');
CALL create_worker('fort', 'Cyril', 'Fort', 'cfort@example.com', 'mkandelaber@example.com', 'common_worker');

CALL create_worker('mobler', 'Marek', 'Mobler', 'mmobler@example.com', 'jzrubnova@example.com', 'common_worker');
CALL create_worker('dobry', 'David', 'Dobrý', 'ddobry@example.com', 'jzrubnova@example.com', 'common_worker');

CALL create_worker('goralova', 'Adriana', 'Goralová', 'agoralova@example.com', 'akovarcik@example.com', 'common_worker');

CALL create_worker('nelmer', 'Wayne', 'Nelmer', 'wnelmer@example.com', 'hurbova@example.com', 'common_worker');
CALL create_worker('obertova', 'Elena', 'Obertová', 'eobertova@example.com', 'hurbova@example.com', 'common_worker');
CALL create_worker('zilava', 'Jana', 'Žilavá', 'jzilava@example.com', 'hurbova@example.com', 'common_worker');

CALL create_worker('gatesee', 'William', 'Gatesee', 'wgatesee@example.com', 'wtousel@example.com', 'common_worker');
CALL create_worker('borg', 'Venus', 'Borg', 'wborg@example.com', 'wtousel@example.com', 'common_worker');
CALL create_worker('tougley', 'Jillian', 'Tougley', 'jtougley@example.com', 'wtousel@example.com', 'common_worker');
CALL create_worker('zlevsky', 'Šimon', 'Zlevský', 'szlevsky@example.com', 'wtousel@example.com', 'common_worker');
CALL create_worker('novak', 'Ján', 'Novák', 'jnovak@example.com', 'wtousel@example.com', 'common_worker');

CALL create_worker('novak2', 'Ján', 'Novák', 'jnovak2@example.com', 'mkolicka@example.com', 'common_worker');
CALL create_worker('ligot', 'Adam', 'Ligot', 'aligot@example.com', 'mkolicka@example.com', 'common_worker');
CALL create_worker('ducka', 'Renáta', 'Ducká', 'rducka@example.com', 'mkolicka@example.com', 'common_worker');
CALL create_worker('titto', 'Aurel', 'Titto', 'atitto@example.com', 'mkolicka@example.com', 'common_worker');
CALL create_worker('golem', 'František', 'Golem', 'fgolem@example.com', 'mkolicka@example.com', 'common_worker');

CALL create_worker('mackley', 'Torina', 'Mackley', 'tmackley@example.com', 'bcalloway@example.com', 'common_worker');
CALL create_worker('ridley', 'Davina', 'Ridley', 'dridley@example.com', 'bcalloway@example.com', 'common_worker');
CALL create_worker('suston', 'Camilla', 'Suston', 'csuston@example.com', 'bcalloway@example.com', 'common_worker');

# Customers
CALL create_customer('barclay', 'Gregory', 'Barclay', 'gbarclay@contoso.com', 'Contoso');
CALL create_customer('grenstone', 'Montgomery', 'Grenstone', 'mgrensto@contoso.com', 'Contoso');
CALL create_customer('harrison', 'Jamala', 'Harrison', 'jharriso@contoso.com', 'Contoso');
CALL create_customer('handon', 'Gowen', 'Handon', 'ghandon@contoso.com', 'Contoso');
CALL create_customer('ridley', 'Barbara', 'Ridley', 'bridley@contoso.com', 'Contoso');

CALL create_customer('mckeen', 'Elizabeth', 'McKeen', 'elizabeth.mckeen@rencomp.com', 'Renowned Company');

CALL create_customer('su', 'Chen', 'Su', 'chen.su@avrgprdcts.cn', 'Average Products');
CALL create_customer('jian', 'Li', 'Jian', 'li.jian@avrgprdcts.cn', 'Average Products');

CALL create_customer('patoprsty', 'Peter', 'Pätoprstý', 'peter@goliath.sk', 'GOLIATH, s.r.o.');

CALL create_customer('bean', 'Paul', 'Bean', 'bean@it.bapts.com', 'Blissful Apartments');
CALL create_customer('geeslow', 'Howard', 'Geeslow', 'geeslow@it.bapts.com', 'Blissful Apartments');
CALL create_customer('clowken', 'Cane', 'Clowken', 'clowken@it.bapts.com', 'Blissful Apartments');

CALL create_customer('aileen', 'Dean', 'Aileen', 'aileen@5coders.com', '5CODERS');
CALL create_customer('brock', 'Nina', 'Brock', 'brock@5coders.com', '5CODERS');
CALL create_customer('peery', 'Clara', 'Peery', 'peery@5coders.com', '5CODERS');
CALL create_customer('plow', 'Pierre', 'Plow', 'plow@5coders.com', '5CODERS');

CALL create_customer('kraubacherova', 'Jolana', 'Kraubacherová', 'jkraub@pohadkovepostele.cz', 'Pohádkové postele, spol. s.r.o.');
CALL create_customer('spiz', 'Eduard', 'Spíž', 'espiz@pohadkovepostele.cz', 'Pohádkové postele, spol. s.r.o.');

CALL create_customer('porubec', 'Ivan', 'Porubec', 'riaditel@vasestavebniny.sk', 'Stavebniny Horec');
