#Useless since v0.10, just in case of...
DROP TABLE IF EXISTS boat_A35;
DROP TABLE IF EXISTS boat_C5;
DROP TABLE IF EXISTS boat_C5v2;
DROP TABLE IF EXISTS boat_Class40;
DROP TABLE IF EXISTS boat_Imoca;
DROP TABLE IF EXISTS boat_Imoca2007;
DROP TABLE IF EXISTS boat_Imoca2008;
DROP TABLE IF EXISTS boat_Mono650;
DROP TABLE IF EXISTS boat_OEv2009;
DROP TABLE IF EXISTS boat_OceanExpress;
DROP TABLE IF EXISTS boat_VLM70;
DROP TABLE IF EXISTS boat_cigale14;
DROP TABLE IF EXISTS boat_figaro;
DROP TABLE IF EXISTS boat_figaro2;
DROP TABLE IF EXISTS boat_debug;
DROP TABLE IF EXISTS boat_dnf;
DROP TABLE IF EXISTS boat_hi5;
DROP TABLE IF EXISTS boat_imoca60;
DROP TABLE IF EXISTS boat_maxicata;

#Change le charset par défaut latin1 => utf-8 (Cf. Ticket #221)
#No convert for coastlines & histpos & positions tables (useless vs. size of the table)
ALTER TABLE auto_pilot CONVERT TO CHARACTER SET utf8;
ALTER TABLE races CONVERT TO CHARACTER SET utf8;
ALTER TABLE races_instructions CONVERT TO CHARACTER SET utf8;
ALTER TABLE races_ranking CONVERT TO CHARACTER SET utf8;
ALTER TABLE races_results CONVERT TO CHARACTER SET utf8;
ALTER TABLE races_waypoints CONVERT TO CHARACTER SET utf8;
ALTER TABLE updates CONVERT TO CHARACTER SET utf8;
ALTER TABLE user_action CONVERT TO CHARACTER SET utf8;
ALTER TABLE user_prefs CONVERT TO CHARACTER SET utf8;
ALTER TABLE users CONVERT TO CHARACTER SET utf8;
ALTER TABLE waypoint_crossing CONVERT TO CHARACTER SET utf8;
ALTER TABLE waypoints CONVERT TO CHARACTER SET utf8;

#Passage en decimal du WP@
ALTER TABLE `users` MODIFY COLUMN `targetandhdg` decimal(4,1);

#Force le type timestamp qui s update tout seul au cas ou...
ALTER TABLE `admin_changelog` MODIFY COLUMN `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

#Migre l ancienne table admin_task vers admin_changelog
INSERT INTO `admin_changelog` (`updated`, `user`, `operation`) SELECT FROM_UNIXTIME(`time`), `admin`, `action` FROM `admin_tasks`;
DROP TABLE IF EXISTS admin_tasks;

#Migre le champ time de updates au format timestamp de mysql
ALTER TABLE `updates` ADD COLUMN `time2` timestamp;
UPDATE `updates` SET `time2` = FROM_UNIXTIME(`time`);
ALTER TABLE `updates` DROP COLUMN `time`;
ALTER TABLE `updates` CHANGE `time2` `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP FIRST;

#Ajoute le user-agent aux logs utilisateurs
ALTER TABLE `user_action` ADD COLUMN `useragent` varchar(255) default NULL;

#Migre le champ time de user_action au format timestamp de mysql
ALTER TABLE `user_action` ADD COLUMN `time2` timestamp;
UPDATE `user_action` SET `time2` = FROM_UNIXTIME(`time`);
ALTER TABLE `user_action` DROP COLUMN `time`;
ALTER TABLE `user_action` CHANGE `time2` `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP FIRST;

#Remets un INDEX dans histpos (mais pas unique)
ALTER TABLE `histpos` ADD INDEX `idu_race_time` (`idusers`, `race`, `time`);
ALTER TABLE `histpos` ADD INDEX `idu_race` (`idusers`, `race`);

#Remets un INDEX dans auto_pilot (mais pas unique)
ALTER TABLE `auto_pilot` ADD INDEX `idusers` (`idusers`);
ALTER TABLE `auto_pilot` ADD INDEX `idusers_time` (`idusers`, `time`);

#Remets un INDEX dans users sur 'engaged' (mais pas unique)
ALTER TABLE `users` ADD INDEX `engaged` (`engaged`);

