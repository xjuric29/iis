# IIS project
# Description: Fill database with sample data
# Author: Michal Pospíšil (xpospi95)

USE 'iis';

# Creates a complete worker - use from highest privilege to lowest
CREATE OR REPLACE PROCEDURE create_worker (
	first_name IN VARCHAR,
	surname IN VARCHAR,
	mail IN VARCHAR,
	sup_mail IN VARCHAR,
	role IN ENUM('common_worker', 'manager', 'superior', 'administrator'),
	)
BEGIN
	INSERT INTO user ('id' , 'first_name', 'surname', 'mail', 'hash')
		VALUES (default, first_name, surname, mail, NULL);
	INSERT INTO user_worker ('id', 'superior', 'role') 
		VALUES (LAST_INSERT_ID(), IF(sup_mail=NULL, NULL, SELECT id FROM user WHERE mail=mail_in), role);
END;

# Creates a complete customer
CREATE OR REPLACE PROCEDURE create_customer (
	first_name IN VARCHAR,
	surname IN VARCHAR,
	mail IN VARCHAR,
	company IN VARCHAR,
	)
BEGIN
	INSERT INTO user ('id' , 'first_name', 'surname', 'mail', 'hash')
		VALUES (default, first_name, surname, mail, NULL);
	INSERT INTO user_customer ('id', 'company') 
		VALUES (LAST_INSERT_ID(), company);
END;

# Create users
# Admins
create_worker("Peter", "Mrkvička", "pmrkvicka@example.com", NULL, "administrator");
create_worker("Harvey", "Foelden", "ceo@example.com", NULL, "administrator")

# Superiors
create_worker("Gabriel", "Moták", "gmotak@example.com", NULL, "superior");
create_worker("Jonathan", "Fairfield", "jfairfield@example.com", NULL, "superior");
create_worker("Marina", "Morgan", "mmorgan@example.com", NULL, "superior");

# Managers
create_worker("Martin", "Kandeláber", "mkandelaber@example.com", 'gmotak@example.com', "manager");

create_worker("Jolana", "Zrubnová", "jzrubnova@example.com", "jfairfield@example.com", "manager");
create_worker("Andrej", "Kovarčík", "akovarcik@example.com", "jfairfield@example.com", "manager");
create_worker("Oto", "Bros", "obros@example.com", "jfairfield@example.com", "manager"); # No common workers

create_worker("Helena", "Urbová", "hurbova@example.com", "mmorgan@example.com", "manager");
create_worker("Wendell", "Tousel", "wtousel@example.com", "mmorgan@example.com", "manager");
create_worker("Mária", "Kolická", "mkolicka@example.com", "mmorgan@example.com", "manager");
create_worker("Ben", "Calloway", "bcalloway@example.com", "mmorgan@example.com", "manager");

# Workers
create_worker("Bartholomew", "Smith", "bsmith@example.com", "mkandelaber@example.com", "common_worker");
create_worker("Anton", "Gelet", "agelet@example.com", "mkandelaber@example.com", "common_worker");
create_worker("Hana", "Barotová", "hbarotova@example.com", "mkandelaber@example.com", "common_worker");
create_worker("Cyril", "Fort", "cfort@example.com", "mkandelaber@example.com", "common_worker");

create_worker("Marek", "Mobler", "mmobler@example.com", "jzrubnova@example.com", "common_worker");
create_worker("David", "Dobrý", "ddobry@example.com", "jzrubnova@example.com", "common_worker");

create_worker("Adriana", "Goralová", "agoralova@example.com", "akovarcik@example.com", "common_worker");

create_worker("Wayne", "Nelmer", "wnelmer@example.com", "hurbova@example.com", "common_worker");
create_worker("Elena", "Obertová", "eobertova@example.com", "hurbova@example.com", "common_worker");
create_worker("Jana", "Žilavá", "jzilava@example.com", "hurbova@example.com", "common_worker");

create_worker("William", "Gatesee", "wgatesee@example.com", "wtousel@example.com", "common_worker");
create_worker("Venus", "Borg", "wborg@example.com", "wtousel@example.com", "common_worker");
create_worker("Jillian", "Tougley", "jtougley@example.com", "wtousel@example.com", "common_worker");
create_worker("Šimon", "Zlevský", "szlevsky@example.com", "wtousel@example.com", "common_worker");
create_worker("Ján", "Novák", "jnovak@example.com", "wtousel@example.com", "common_worker");

create_worker("Ján", "Novák", "jnovak2@example.com", "mkolicka@example.com", "common_worker");
create_worker("Adam", "Ligot", "aligot@example.com", "mkolicka@example.com", "common_worker");
create_worker("Renáta", "Ducká", "rducka@example.com", "mkolicka@example.com", "common_worker");
create_worker("Aurel", "Titto", "atitto@example.com", "mkolicka@example.com", "common_worker");
create_worker("František", "Golem", "fgolem@example.com", "mkolicka@example.com", "common_worker");

create_worker("Torina", "Mackley", "tmackley@example.com", "bcalloway@example.com", "common_worker");
create_worker("Davina", "Ridley", "dridley@example.com", "bcalloway@example.com", "common_worker");
create_worker("Camilla", "Suston", "csuston@example.com", "bcalloway@example.com", "common_worker");


# Customers
create_customer("Gregory", "Barclay", "gbarclay@contoso.com", "Contoso");
create_customer("Montgomery", "Grenstone", "mgrensto@contoso.com", "Contoso");
create_customer("Jamala", "Harrison", "jharriso@contoso.com", "Contoso");
create_customer("Gowen", "Handon", "ghandon@contoso.com", "Contoso");
create_customer("Barbara", "Ridley", "bridley@contoso.com", "Contoso");

create_customer("Elizabeth", "McKeen", "elizabeth.mckeen@rencomp.com", "Renowned Company");

create_customer("Chen", "Su", "chen.su@avrgprdcts.cn", "Average Products");
create_customer("Li", "Jian", "li.jian@avrgprdcts.cn", "Average Products");

create_customer("Peter", "Pätoprstý", "peter@goliath.sk", "GOLIATH, s.r.o.");

create_customer("Paul", "Bean", "bean@it.bapts.com", "Blissful Apts.");
create_customer("Howard", "Geeslow", "geeslow@it.bapts.com", "Blissful Apts.");
create_customer("Cane", "Clowken", "clowken@it.bapts.com", "Blissful Apts.");

create_customer("Dean", "Aileen", "aileen@5coders.com", "5CODERS");
create_customer("Nina", "Brock", "brock@5coders.com", "5CODERS");
create_customer("Clara", "Peery", "peery@5coders.com", "5CODERS");
create_customer("Pierre", "Plow", "plow@5coders.com", "5CODERS");

create_customer("Jolana", "Kraubacherová", "jkraub@pohadkovepostele.cz", "Pohádkové postele, spol. s.r.o.");
create_customer("Eduard", "Spíž", "espiz@pohadkovepostele.cz", "Pohádkové postele, spol. s.r.o.");

create_customer("Ivan", "Porubec", "riaditel@vasestavebniny.sk", "Stavebniny Horec");

