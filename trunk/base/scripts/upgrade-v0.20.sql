#Suppression des tracks cachées
UPDATE `users` SET color = SUBSTR(color, 2) WHERE LEFT(color, 1) = '-';

#Creation de la table racesgroups
DROP TABLE IF EXISTS `racesgroups`;
CREATE TABLE `racesgroups` (
  `idracesgroups` int(11) NOT NULL auto_increment,
  `grouptag` varchar(32) NOT NULL default '',
  `groupname` varchar(255) NOT NULL default '',
  `grouptitle` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  `admincomments` varchar(255) default NULL, 
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idracesgroups`),
  UNIQUE KEY `grouptag_idx` (`grouptag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='A racesgroup description';

#Creation de la table racestogroups
DROP TABLE IF EXISTS `racestogroups`;
CREATE TABLE `racestogroups` (
  `idracestogroups` int(11) NOT NULL auto_increment,
  `idraces` int(11) NOT NULL,
  `grouptag` varchar(32) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idracestogroups`),
  UNIQUE KEY `join_idx` (`idraces`, `grouptag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Join races with groups';

#Creation de la table news
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `idnews` int(11) NOT NULL auto_increment,
  `media` varchar(32) NOT NULL,
  `summary` varchar(140) NOT NULL default '',
  `timetarget` bigint default 0,
  `published` bigint default 0,
  PRIMARY KEY  (`idnews`),
  UNIQUE KEY `hashnews_idx` (`media`, `summary`, `timetarget`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='News table';

