#Passage en decimal du WP@
ALTER TABLE `users` MODIFY COLUMN `targetandhdg` decimal(4,1);

#Force le type timestamp qui s update tout seul au cas ou...
ALTER TABLE `admin_changelog` MODIFY COLUMN `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

#Migre l ancienne table admin_task vers admin_changelog
INSERT INTO `admin_changelog` (`updated`, `user`, `operation`) SELECT FROM_UNIXTIME(`time`), `admin`, `action` FROM `admin_tasks`;
#On effacera dans la prochaine release (juste au cas ou...)

#Migre le champ time de updates au format timestamp de mysql
ALTER TABLE `updates` ADD COLUMN `time2` timestamp;
UPDATE `updates` SET `time2` = FROM_UNIXTIME(time);
ALTER TABLE `updates` DROP COLUMN `time`;
ALTER TABLE `updates` CHANGE `time2` `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP FIRST;
