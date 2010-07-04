#Table de gestion des PLAYERS
CREATE TABLE `players` (
  `idplayers` bigint(20) NOT NULL auto_increment,
  `email` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `pseudo` varchar(20) default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idplayers`),
  UNIQUE KEY `email` (`email`),
  KEY `email_password` (`email`,`password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='players (not boats)';



