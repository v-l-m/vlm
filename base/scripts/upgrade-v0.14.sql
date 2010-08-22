#Nettoyages pour le dev
DROP TABLE IF EXISTS players_pending;
DROP TABLE IF EXISTS players;
DROP TABLE IF EXISTS playerstousers;

# FIX THIS BEFORE RELEASE
ALTER TABLE `user_action` DROP COLUMN `idplayers`;
ALTER TABLE `users` DROP INDEX `boatpseudo`;
ALTER TABLE `races_waypoints` DROP COLUMN `wpformat`;
ALTER TABLE `waypoint_crossing` DROP COLUMN `validity`;

#Tables de gestion des PLAYERS
CREATE TABLE `players_pending` (
  `idplayers_pending` bigint(20) NOT NULL auto_increment,
  `email` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  `playername` varchar(20) NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `seed` bigint default 0,
  PRIMARY KEY  (`idplayers_pending`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='players_pending validation';

CREATE TABLE `players` (
  `idplayers` bigint(20) NOT NULL auto_increment,
  `email` varchar(50) NOT NULL, 
  `password` varchar(64) NOT NULL,
  `playername` varchar(20) NOT NULL,
  `permissions` varchar(20) NOT NULL default 'player',
  `description` text default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`idplayers`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `playername` (`playername`),
  KEY `email_password` (`email`,`password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='players (not boats)';

#Tables players <-> boats
CREATE TABLE `playerstousers` (
  `idplayerstousers` bigint(20) NOT NULL auto_increment,
  `idplayers` bigint(20) NOT NULL,
  `idusers` bigint(20) NOT NULL,
  `linktype` int NOT NULL DEFAULT 1,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idplayerstousers`),
  UNIQUE KEY `playertouser` (`idplayers`, `idusers`, `linktype`),
  KEY `players` (`idplayers`),
  KEY `users` (`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='link beween players and users';

#Logging player action
ALTER TABLE user_action ADD COLUMN `idplayers` int(11) DEFAULT NULL AFTER `time` ;

#Ajout Index unique sur le username (=boatpseudo)
ALTER TABLE `users` ADD UNIQUE INDEX `boatpseudo` (`username`);

#Adding table for waypoint types
ALTER TABLE races_waypoints ADD COLUMN `wpformat` int NOT NULL default '0' AFTER `idwaypoint` ;
UPDATE races_waypoints RW, waypoints WP SET RW.wpformat = 1 WHERE RW.idwaypoint=WP.idwaypoint AND WP.latitude1=WP.latitude2 AND WP.longitude1=WP.longitude2 AND RW.laisser_au != 999;
UPDATE races_waypoints RW, waypoints WP SET RW.wpformat = RW.wpformat + 16 WHERE RW.idwaypoint=WP.idwaypoint AND RW.wptype LIKE "%Icegate%" AND WP.latitude1 > 0;
UPDATE races_waypoints RW, waypoints WP SET RW.wpformat = RW.wpformat + 32 WHERE RW.idwaypoint=WP.idwaypoint AND RW.wptype LIKE "%Icegate%" AND WP.latitude1 < 0;

#waypoints crossing, adding validity check
ALTER TABLE waypoint_crossing ADD COLUMN `validity` int NOT NULL default '1' AFTER `idusers` ;

