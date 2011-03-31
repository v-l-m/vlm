#Ajoute un timestamp dans races pour connaitre la date de publication / dernier changement
ALTER TABLE `races` ADD COLUMN `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;
UPDATE `races` SET `updated` = IF(FROM_UNIXTIME(`deptime`) > NOW(), NOW(), FROM_UNIXTIME(`deptime`));

#Ajoute un champ pour logguer le serveur sur lequel l action a été faite.
ALTER TABLE `user_action` ADD COLUMN `actionserver` varchar(32) NOT NULL default 'UNDEFINED_VLM_SERVER';

