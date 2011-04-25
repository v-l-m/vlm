#Creation de la table des prefs des players
DROP TABLE IF EXISTS `players_prefs`;
CREATE TABLE `players_prefs` (
  `idplayers_prefs` bigint(20) NOT NULL auto_increment,
  `idplayers` int(11) NOT NULL,
  `pref_name` varchar(32) NOT NULL,
  `pref_value` mediumtext NOT NULL,
  `permissions` int(11) NOT NULL default 0,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idplayers_prefs`),
  UNIQUE KEY `idp_key` (`idplayers`, `pref_name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='preferences of players'

#On ajoute une clef indexee dans user_prefs pour pouvoir naviguer plus facilement dedans
ALTER TABLE `user_prefs` DROP PRIMARY KEY;
ALTER TABLE `user_prefs` ADD UNIQUE KEY(`idusers`, `pref_name`);
ALTER TABLE `user_prefs` ADD COLUMN `idusers_prefs` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`idusers_prefs`);
ALTER TABLE `user_prefs` ADD COLUMN `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

