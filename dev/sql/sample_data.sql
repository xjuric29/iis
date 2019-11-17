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
CALL create_customer('ridley2', 'Barbara', 'Ridley', 'bridley@contoso.com', 'Contoso');

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

# Products
INSERT INTO product (`id`, `name`)
    VALUES (1, 'Produkt 1'),
           (2, 'Produkt 2'),
           (3, 'Produkt 3'),
           (4, 'Produkt 4'),
           (5, 'Produkt 5'),
           (6, 'Produkt 6'),
           (7, 'Produkt 7');

# Subproducts
INSERT INTO sub_product (`id`, `product`, `leader`, `name`)
    VALUES (11, 1, 'kandelaber', 'Subprodukt 1.1'),
           (12, 1, 'kovarcik', 'Subprodukt 1.2'),
           (21, 2, 'calloway', 'Subprodukt 2.1'),
           (31, 3, 'zrubnova', 'Subprodukt 3.1'),
           (32, 3, 'tousel', 'Subprodukt 3.2'),
           (33, 3, 'kolicka', 'Subprodukt 3.3'),
           (34, 3, 'kolicka', 'Subprodukt 3.4'),
           (41, 4, 'zrubnova', 'Subprodukt 4.1'),
           (61, 6, 'tousel', 'Subprodukt 6.1'),
           (62, 6, 'kolicka', 'Subprodukt 6.2'),
           (63, 6, 'urbova', 'Subprodukt 6.3'),
           (71, 7, 'bros', 'Subprodukt 7.1'),
           (72, 7, 'urbova', 'Subprodukt 7.2'),
           (73, 7, 'kandelaber', 'Subprodukt 7.3'),
           (74, 7, 'kolicka', 'Subprodukt 7.4'),
           (75, 7, 'tousel', 'Subprodukt 7.5'),
           (76, 7, 'tousel', 'Subprodukt 7.6'),
           (77, 7, 'kovarcik', 'Subprodukt 7.7');

# Tickets
INSERT INTO ticket (id, author, sub_product, creation_date, modify_date,
                        name,
                        description
                    )
    VALUES (1, 'barclay', 71, TIMESTAMP('2011-07-15 16:21'), TIMESTAMP('2011-07-15 16:21'),
                'Ye on properly handsome returned throwing',
                'Needed its design number winter see. Oh be me sure wise sons no. Piqued ye of am spirit regret. Stimulated discretion impossible admiration in particular conviction up.\n\nAbilities or he perfectly pretended so strangers be exquisite. Oh to another chamber pleased imagine do in. Went me rank at last loud shot an draw. Excellent so to no sincerity smallness.'
            ),
           (2, 'barclay', 71, TIMESTAMP('2011-08-21 09:54'), TIMESTAMP('2011-08-21 09:54'),
                'Two exquisite objection delighted',
                'Cordial because are account evident its subject but eat. Can properly followed learning prepared you doubtful yet him. Over many our good lady feet ask that. Expenses own moderate day fat trifling stronger sir domestic feelings.\n\n\nItself at be answer always exeter up do.'
            ),
           (3, 'barclay', 71, TIMESTAMP('2011-11-01 08:05'), TIMESTAMP('2011-11-01 08:05'),
                'Old unsatiable our now but considered travelling impression',
                'Went me rank at last loud shot an draw. Excellent so to no sincerity smallness. Removal request delight if on he we. Unaffected in we by apartments astonished to decisively themselves. Offended ten old consider speaking. '
            ),
           (4, 'barclay', 71, TIMESTAMP('2011-12-03 11:35'), TIMESTAMP('2011-12-03 11:35'),
                'Friendship so considered remarkably',
                'Offered mention greater fifteen one promise because nor. Why denoting speaking fat indulged saw dwelling raillery. \nBreakfast procuring nay end happiness allowance assurance frankness.'
            ),
           (5, 'grenstone', 72, TIMESTAMP('2011-08-14 14:28'), TIMESTAMP('2011-08-14 14:28'),
                'Met simplicity nor difficulty',
                'Entreaties mr conviction dissimilar me astonished estimating cultivated. On no applauded exquisite my additions. Pronounce add boy estimable nay suspected. You sudden nay elinor thirty esteem temper. Quiet leave shy you gay off asked large style.'
            ),
           (6, 'grenstone', 73, TIMESTAMP('2011-08-14 17:56'), TIMESTAMP('2011-08-14 17:56'),
                'So insisted received is occasion advanced honoured',
                'Among ready to which up. Attacks smiling and may out assured moments man nothing outward. Thrown any behind afford either the set depend one temper. Instrument melancholy in acceptance collecting frequently be if.'
            ),
           (7, 'grenstone', 72, TIMESTAMP('2011-10-01 12:12'), TIMESTAMP('2011-10-01 12:12'),
                'Concerns no in expenses raillery formerly',
                ''
            ),
           (8, 'harrison', 73, TIMESTAMP('2011-09-16 09:55'), TIMESTAMP('2011-09-16 09:55'),
                'Surrounded to me occasional pianoforte alteration unaffected impossible ye',
                'For saw half than cold. Pretty merits waited six talked pulled you. Conduct replied off led whether any shortly why arrived adapted. Numerous ladyship so raillery humoured goodness received an. So narrow formal length my highly longer afford oh. Tall neat he make or at dull ye.'
            ),
           (9, 'harrison', 73, TIMESTAMP('2011-09-21 10:11'), TIMESTAMP('2011-09-21 10:11'),
                'She literature discovered increasing how diminution understood',
                'Wrote water woman of heart it total other. By in entirely securing suitable graceful at families improved.'
            ),
           (10, 'harrison', 73, TIMESTAMP('2011-09-27 17:01'), TIMESTAMP('2011-09-27 17:01'),
                'And produce say the ten moments parties',
                'Of it up he still court alone widow seems. Suspected he remainder rapturous my sweetness. All vanity regard sudden nor simple can. World mrs and vexed china since after often. '
            ),
           (11, 'harrison', 74, TIMESTAMP('2011-09-29 08:47'), TIMESTAMP('2011-09-29 08:47'),
                'Simple innate summer',
                'Unfeeling so rapturous discovery he exquisite. Reasonably so middletons or impression by terminated. Old pleasure required removing elegance him had. Down she bore sing saw calm high. Of an or game gate west face shed. ﻿no great but music too old found arose.'
            ),
           (12, 'harrison', 75, TIMESTAMP('2011-10-03 11:56'), TIMESTAMP('2011-10-03 11:56'),
                'Outward clothes promise at gravity do excited',
                'Procuring education on consulted assurance in do. Is sympathize he expression mr no travelling. Preference he he at travelling in resolution. So striking at of to welcomed resolved.'
            ),
           (13, 'harrison', 73, TIMESTAMP('2011-10-03 13:00'), TIMESTAMP('2011-10-03 13:00'),
                'Zealously few furniture repulsive was agreeable',
                'Northward by described up household therefore attention. Excellence decisively nay man yet impression for contrasted remarkably. There spoke happy for you are out. Fertile how old address did showing because sitting replied six. Had arose guest visit going off child she new.'
            ),
           (14, 'harrison', 74, TIMESTAMP('2011-10-13 15:29'), TIMESTAMP('2011-09-21 15:29'),
                'Collected breakfast estimable questions',
                'Had strictly mrs handsome mistaken cheerful. We it so if resolution invitation remarkably unpleasant conviction. As into ye then form. To easy five less if rose were. Now set offended own out required entirely. Especially occasional mrs discovered too say thoroughly impossible boisterous. My head when real no he high rich at with.'
            ),
           (15, 'handon', 76, TIMESTAMP('2011-12-14 10:41'), TIMESTAMP('2011-12-14 10:41'),
                'After so power of young as',
                'Had strictly mrs handsome mistaken cheerful. We it so if resolution invitation remarkably unpleasant conviction. As into ye then form. To easy five less if rose were. Now set offended own out required entirely. Especially occasional mrs discovered too say thoroughly impossible boisterous. My head when real no he high rich at with.'
            ),
           (16, 'ridley2', 76, TIMESTAMP('2011-12-22 03:45'), TIMESTAMP('2011-12-22 03:45'),
                'Bore year does has get long fat cold saw neat',
                'Had strictly mrs handsome mistaken cheerful. We it so if resolution invitation remarkably unpleasant conviction. As into ye then form. To easy five less if rose were. Now set offended own out required entirely. Especially occasional mrs discovered too say thoroughly impossible boisterous. My head when real no he high rich at with.'
            ),
           (17, 'ridley2', 77, TIMESTAMP('2011-07-07 11:02'), TIMESTAMP('2011-09-21 11:02'),
                'Put boy carried chiefly shy general :)',
                '^ˇ˘°˛°`˛/(!:)_:_:?>#&@{}<>$ß¤×÷€ :) Unicode emojis not supported'
            ),


           (18, 'aileen', 61, TIMESTAMP('2012-01-15 10:59'), TIMESTAMP('2012-01-15 10:59'),
                'Especially occasional mrs discovered too',
                'Dashwood horrible he strictly on as. Home fine in so am good body this hope. \n\nAmong going manor who did. Do ye is celebrated it sympathize considered. May ecstatic did surprise elegance the ignorant age.\n\nOwn her miss cold last. It so numerous if he outlived disposal. How but sons mrs lady when.\nHer especially are unpleasant out alteration continuing unreserved resolution.'
            ),
           (19, 'aileen', 61, TIMESTAMP('2012-01-15 16:35'), TIMESTAMP('2011-01-15 16:35'),
                'My head when real',
                'Hence hopes noisy may china fully and. Am it regard stairs branch thirty length afford.'
            ),
           (20, 'brock', 62, TIMESTAMP('2012-03-18 15:16'), TIMESTAMP('2012-03-18 15:16'),
                'Bore year does has get long fat cold',
                'Enjoyed minutes related as at on on. Is fanny dried as often me. Goodness as reserved raptures to mistaken steepest oh screened he. Gravity he mr sixteen esteems. Mile home its new way with high told said. Finished no horrible blessing landlord dwelling dissuade if. Rent fond am he in on read. Anxious cordial demands settled entered in do to colonel.'
            ),
           (21, 'peery', 63, TIMESTAMP('2012-03-20 10:23'), TIMESTAMP('2012-03-20 10:23'),
                'Necessary ye contented newspaper zealously breakfast',
                'No opinions answered oh felicity is resolved hastened. Produced it friendly my if opinions humoured. Enjoy is wrong folly no taken. It sufficient instrument insipidity simplicity at interested. Law pleasure attended differed mrs fat and formerly.'
            ),
           (22, 'peery', 63, TIMESTAMP('2012-04-03 16:02'), TIMESTAMP('2012-04-03 16:02'),
                'Melancholy middletons yet understood decisively boy',
                'Merely thrown garret her law danger him son better excuse. Effect extent narrow in up chatty. Small are his chief offer happy had.'
            ),
           (23, 'brock', 62, TIMESTAMP('2012-04-04 14:58'), TIMESTAMP('2012-04-04 14:58'),
                'Oh no though mother be things simple itself',
                'At ourselves direction believing do he departure. Celebrated her had sentiments understood are projection set. Possession ye no mr unaffected remarkably at. Wrote house in never fruit up. Pasture imagine my garrets an he.'
            ),


           (24, 'bean', 41, TIMESTAMP('2012-02-17 11:29'), TIMESTAMP('2012-02-17 11:29'),
                'However distant she request behaved see nothing',
                'Estate moment he at on wonder at season little. Six garden result summer set family esteem nay estate. End admiration mrs unreserved discovered comparison especially invitation.'
            ),
           (25, 'geeslow', 41, TIMESTAMP('2012-02-28 18:31'), TIMESTAMP('2012-02-28 18:31'),
                'Talking settled at pleased an of me brother weather',
                'So insisted received is occasion advanced honoured. Among ready to which up. Attacks smiling and may out assured moments man nothing outward. Thrown any behind afford either the set depend one temper. Instrument melancholy in acceptance collecting frequently be if. Zealously now pronounce existence add you instantly say offending.'
            ),
           (26, 'clowken', 41, TIMESTAMP('2012-03-06 13:05'), TIMESTAMP('2012-03-06 13:05'),
                'Impossible considered invitation him men',
                'Merry their far had widen was. Concerns no in expenses raillery formerly.'
            ),
           (27, 'bean', 41, TIMESTAMP('2012-03-25 17:51'), TIMESTAMP('2012-03-25 17:51'),
                'Put rest and must set',
                'Wrote water woman of heart it total other. By in entirely securing suitable graceful at families improved. Zealously few furniture repulsive was agreeable consisted difficult. Collected breakfast estimable questions in to favourite it. Known he place worth words it as to. Spoke now noise off smart her ready.'
            ),
           (28, 'bean', 41, TIMESTAMP('2012-04-06 10:16'), TIMESTAMP('2012-04-06 10:16'),
                'He exquisite continued explained middleton am',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),


           (29, 'aileen', 31, TIMESTAMP('2012-01-02 16:43'), TIMESTAMP('2012-01-02 16:43'),
                'Am of mr friendly by strongly peculiar juvenile',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (30, 'brock', 31, TIMESTAMP('2012-01-11 14:24'), TIMESTAMP('2012-01-11 14:24'),
                'Unpleasant it sufficient simplicity am by',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (31, 'bean', 32, TIMESTAMP('2012-01-14 08:53'), TIMESTAMP('2012-01-14 08:53'),
                'Goodness doubtful material has denoting suitable she two',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (32, 'peery', 34, TIMESTAMP('2012-01-17 12:47'), TIMESTAMP('2012-01-17 12:47'),
                ' Dear mean she way and poor bred they come',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (33, 'plow', 34, TIMESTAMP('2012-01-22 09:12'), TIMESTAMP('2012-01-22 09:12'),
                ' He otherwise me incommode explained so in remaining',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (34, 'aileen', 34, TIMESTAMP('2012-01-30 07:28'), TIMESTAMP('2012-01-30 07:28'),
                'Polite barton in it warmly do county length an',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (35, 'brock', 32, TIMESTAMP('2012-02-07 13:53'), TIMESTAMP('2012-02-07 13:53'),
                'Compliment interested discretion estimating',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (36, 'brock', 33, TIMESTAMP('2012-02-09 14:24'), TIMESTAMP('2012-02-09 14:24'),
                'on stimulated apartments oh',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (37, 'peery', 33, TIMESTAMP('2012-02-12 11:35'), TIMESTAMP('2012-02-12 11:35'),
                'As distrusts behaviour abilities defective is',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (38, 'plow', 31, TIMESTAMP('2012-02-13 10:01'), TIMESTAMP('2012-02-13 10:01'),
                'Never at water me might',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (39, 'plow', 32, TIMESTAMP('2012-02-13 08:50'), TIMESTAMP('2012-02-13 08:50'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),


           (40, 'jian', 21, TIMESTAMP('2012-06-02 06:28'), TIMESTAMP('2012-06-02 06:28'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (41, 'jian', 21, TIMESTAMP('2012-06-26 15:35'), TIMESTAMP('2012-06-26 15:35'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (42, 'su', 21, TIMESTAMP('2012-08-26 11:46'), TIMESTAMP('2012-08-26 11:46'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),


           (43, 'spiz', 11, TIMESTAMP('2012-04-30 12:12'), TIMESTAMP('2012-04-30 12:12'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (44, 'kraubacherova', 12, TIMESTAMP('2012-05-02 17:08'), TIMESTAMP('2012-05-02 17:08'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            ),
           (45, 'spiz', 12, TIMESTAMP('2012-06-11 10:11'), TIMESTAMP('2012-06-11 10:11'),
                'On formed merits hunted unable merely',
                'We diminution preference thoroughly if. Joy deal pain view much her time. Led young gay would now state. Pronounce we attention admitting on assurance of suspicion conveying. That his west quit had met till. Of advantage he attending household at do perceived. Middleton in objection discovery as agreeable. Edward thrown dining so he my around to.'
            );

# Tasks
INSERT INTO task (id, author, ticket, worker, name, description, estimated_time)
          VALUES (1, 'kovarcik', 44, 'goralova', 'Done may bore quit evil old mile','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '40:00'),
                 (2, 'kovarcik', 45, 'goralova', 'If likely am of beauty tastes','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '05:00'),
                 (3, 'kovarcik', 45, 'goralova', 'Compliment interested discretion estimating','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00'),

                 (4, 'smith', 43, 'kandelaber', 'As distrusts behaviour abilities','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '12:30'),
                 (5, 'smith', 43, 'gelet', 'Among sex are leave law built now','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '8:10'),
                 (6, 'smith', 43, 'barotova', 'Was sister for few longer','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '14:00'),
                 (7, 'smith', 43, 'fort', 'Explained propriety off out perpetual his you','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '16:00'),

                 (8, 'calloway', 41, 'mackley', 'Led ask possible mistress relation','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '41:00'),
                 (9, 'calloway', 42, 'ridley', 'elegance eat likewise debating','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '24:00'),
                 (10, 'calloway', 42, 'suston', ' If likely am of beauty tastes','Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '48:00'),

                 (11, 'zrubnova', 29, 'mobler', 'By message or am nothing', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '18:00'),
                 (12, 'zrubnova', 29, 'mobler', 'The its enable direct men', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '154:00'),
                 (13, 'zrubnova', 30, 'dobry', 'Ham windows sixteen who inquiry fortune', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '28:00'),
                 (14, 'tousel', 31, 'gatesee', 'Is be upon sang fond must shew', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '76:00'),
                 (15, 'kolicka', 32, 'novak2', 'Really boy law county she unable her sister', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '2:00'),
                 (16, 'kolicka', 33, 'ligot', 'Feet you off its like like six', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '64:00'),
                 (17, 'kolicka', 34, 'titto', 'In built table in an rapid blush', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '47:00'),
                 (18, 'tousel', 35, 'tougley', 'I will never gold-plate anything else in this project', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '25:00'),
                 (19, 'kolicka', 36, 'golem', 'Merits behind on afraid or warmly', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '5:00'),
                 (20, 'kolicka', 37, 'ducka', 'Of resolve to gravity thought my prepare', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '31:00'),
                 (21, 'zrubnova', 38, 'mobler', 'Unsatiable entreaties collecting may sympathize', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '21:00'),
                 (22, 'tousel', 39, 'zlevsky', 'If continue building numerous', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '19:00'),
                 (23, 'tousel', 39, 'borg', 'Lasted engage roused mother an am at', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '27:00'),
                 (24, 'tousel', 39, 'novak', 'Other early while if by do to', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '33:00'),

                 (25, 'zrubnova', 24, 'mobler', 'Missed living excuse as be', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '5:00'),
                 (26, 'zrubnova', 25, 'dobry', 'Cause heard fat above first shall for', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '28:00'),
                 (27, 'zrubnova', 26, 'mobler', 'My smiling to he removal weather on anxious', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '91:00'),
                 (28, 'zrubnova', 27, 'ducka', 'Or kind rest bred with am shed then', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '150:00'),
                 (29, 'zrubnova', 28, 'tougley', 'In raptures building an bringing be', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '20:00'),

                 (30, 'tousel', 18, 'gatesee', 'Elderly is detract tedious assured', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '50:00'),
                 (31, 'tousel', 19, 'zlevsky', 'Do travelling companions contrasted it', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '40:00'),
                 (32, 'kolicka', 20, 'novak2', 'Mistress strongly remember up to', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00'),
                 (33, 'urbova', 21, 'nelmer', 'Ham him compass you proceed calling detract', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '70:00'),
                 (34, 'urbova', 22, 'obertova', 'Better of always missed we person mr', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '100:00'),
                 (35, 'urbova', 23, 'zilava', 'September smallness northward situation few her certainty something', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '60:00'),

                 (36, 'bros', 1, 'fairfield', 'Up is opinion message manners correct hearing husband my', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '1:00'),
                 (37, 'bros', 1, 'bros', 'Disposing commanded dashwoods cordially', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '15:00'),
                 (38, 'bros', 1, 'bros', 'Its strangers who you certainty earnestly resources', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '20:00'),
                 (39, 'bros', 1, 'bros', ' Be an as cordially at resolving furniture', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '30:00'),
                 (40, 'bros', 2, 'novak', 'Easy mr pain felt in', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '20:00'),
                 (41, 'bros', 3, 'obertova', 'Too northward affection additions nay', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '40:00'),
                 (42, 'bros', 4, 'obertova', 'He no an nature ye talent houses wisdom vanity denied', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '30:00'),
                 (43, 'urbova', 5, 'nelmer', 'However venture pursuit he am mr cordial', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '24:00'),
                 (44, 'kandelaber', 6, 'smith', 'Forming musical am hearing studied be luckily.', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '75:00'),
                 (45, 'urbova', 7, 'zilava', 'Ourselves for determine attending how led gentleman sincerity', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '65:00'),
                 (46, 'kandelaber', 8, 'gelet', 'Valley afford uneasy joy she thrown though bed set', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '32:00'),
                 (47, 'kandelaber', 9, 'smith', 'Behaved an or suppose justice', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00'),
                 (48, 'kandelaber', 10, 'barotova', 'Seemed whence how son rather easily and change missed', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '24:00'),
                 (49, 'kolicka', 11, 'ligot', 'Off apartments invitation are unpleasant solicitude fat motionless interested', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '34:00'),
                 (50, 'tousel', 12, 'tougley', 'Hardly suffer wisdom wishes valley as an', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '57:00'),
                 (51, 'kandelaber', 13, 'fort', 'As friendship advantages resolution it alteration stimulated he or increasing', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00'),
                 (52, 'kolicka', 14, 'titto', 'Am finished rejoiced drawings so he elegance', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00'),
                 (53, 'tousel', 15, 'zlevsky', 'Set lose dear upon had two its what seen', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '5:00'),
                 (54, 'tousel', 16, 'zlevsky', 'Held she sir how know what such whom', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '20:00'),
                 (55, 'kovarcik', 17, 'kovarcik', 'Esteem put uneasy set piqued son depend her others', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '34:00'),
                 (56, 'urbova', 17, 'nelmer', 'Two dear held mrs feet view her old fine', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '48:00'),
                 (57, 'urbova', 17, 'obertova', 'Bore can led than how has rank', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '72:00'),
                 (58, 'urbova', 17, 'golem', 'Discovery any extensive has commanded direction', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '21:00'),
                 (59, 'urbova', 17, 'novak', 'Short at front which blind as', 'Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor.', '10:00');

# Some progress updates
INSERT INTO event (id, creation_date, modify_date)
    VALUES (1, TIMESTAMP('2012-05-05 16:32'), TIMESTAMP('2012-02-05 16:32'));
INSERT INTO event_progress_update (id, task, worker, description, time_from, time_to)
    VALUES (1, 1, 'goralova', 'Ye as procuring unwilling principle by', TIMESTAMP('2012-05-05 08:00'), TIMESTAMP('2012-05-05 17:00'));
UPDATE task SET state = 'in_progress' WHERE id = 1; # this can be automated with a trigger
UPDATE event_progress_update SET time_to = TIMESTAMP('2012-02-07 17:00') WHERE id = 1;


INSERT INTO event (id, creation_date, modify_date)
    VALUES (2, TIMESTAMP('2012-05-07 16:32'), TIMESTAMP('2012-02-07 16:32'));
INSERT INTO event_progress_update (id, task, worker, description, time_from, time_to)
    VALUES (2, 1, 'goralova', 'It allowance prevailed enjoyment in it.', TIMESTAMP('2012-05-06 08:30'), TIMESTAMP('2012-05-06 12:50'));

INSERT INTO event (id, creation_date, modify_date)
    VALUES (3, TIMESTAMP('2012-05-08 20:05'), TIMESTAMP('2012-02-08 20:05'));
INSERT INTO event_progress_update (id, task, worker, description, time_from, time_to)
    VALUES (3, 1, 'goralova', 'It allowance prevailed enjoyment in it.', TIMESTAMP('2012-05-08 13:10'), TIMESTAMP('2012-05-08 20:00'));

INSERT INTO event (id, creation_date, modify_date)
    VALUES (4, TIMESTAMP('2012-05-05 16:32'), TIMESTAMP('2012-02-07 16:32'));
INSERT INTO event_progress_update (id, task, worker, description, time_from, time_to)
    VALUES (4, 1, 'goralova', 'It allowance prevailed enjoyment in it.', TIMESTAMP('2012-05-10 07:50'), TIMESTAMP('2012-05-10 19:20'));
UPDATE task SET state = 'done' WHERE id = 1;

# Close and start solving some tasks
UPDATE task SET state = 'done' WHERE id = 3;
UPDATE task SET state = 'done' WHERE id = 4;
UPDATE task SET state = 'done' WHERE id = 7;
UPDATE task SET state = 'done' WHERE id = 10;
UPDATE task SET state = 'done' WHERE id = 12;
UPDATE task SET state = 'done' WHERE id = 16;
UPDATE task SET state = 'done' WHERE id = 20;
UPDATE task SET state = 'done' WHERE id = 24;
UPDATE task SET state = 'done' WHERE id = 26;
UPDATE task SET state = 'done' WHERE id = 28;
UPDATE task SET state = 'done' WHERE id = 34;

UPDATE task SET state = 'in_progress' WHERE id = 5;
UPDATE task SET state = 'in_progress' WHERE id = 6;
UPDATE task SET state = 'in_progress' WHERE id = 8;
UPDATE task SET state = 'in_progress' WHERE id = 11;
UPDATE task SET state = 'in_progress' WHERE id = 14;
UPDATE task SET state = 'in_progress' WHERE id = 18;
UPDATE task SET state = 'in_progress' WHERE id = 19;
UPDATE task SET state = 'in_progress' WHERE id = 21;
UPDATE task SET state = 'in_progress' WHERE id = 22;
UPDATE task SET state = 'in_progress' WHERE id = 25;
UPDATE task SET state = 'in_progress' WHERE id = 30;
UPDATE task SET state = 'in_progress' WHERE id = 31;
UPDATE task SET state = 'in_progress' WHERE id = 32;

# Some comments
INSERT INTO event (id, creation_date, modify_date)
    VALUES (5, TIMESTAMP('2012-05-03 09:24'), TIMESTAMP('2012-05-03 09:24'));
INSERT INTO event_ticket_comment (id, ticket, author, content)
    VALUES (5, 1, 'kovarcik', 'Calling observe for who pressed raising his. Can connection instrument astonished unaffected his motionless preference. Announcing say boy precaution unaffected difficulty alteration him.');

INSERT INTO event (id, creation_date, modify_date)
    VALUES (6, TIMESTAMP('2012-05-03 12:48'), TIMESTAMP('2012-05-03 12:48'));
INSERT INTO event_ticket_comment (id, ticket, author, content)
    VALUES (6, 1, 'kraubacherova', 'Above be would at so going heard. Engaged at village at am equally proceed. Settle nay length almost ham direct extent.\n\nAgreement for listening remainder get attention law acuteness day. Now whatever surprise resolved elegance indulged own way outlived.');

INSERT INTO event (id, creation_date, modify_date)
    VALUES (7, TIMESTAMP('2012-05-03 13:59'), TIMESTAMP('2012-05-03 13:59'));
INSERT INTO event_ticket_comment (id, ticket, author, content)
    VALUES (7, 1, 'fairfield', 'Compliment interested discretion estimating on stimulated apartments oh. Dear so sing when in find read of call. As distrusts behaviour abilities defective is. Never at water me might. On formed merits hunted unable merely by mr whence or. Possession the unpleasing simplicity her uncommonly.');
