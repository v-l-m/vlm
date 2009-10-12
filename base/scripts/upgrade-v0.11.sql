#cleaning...
DROP TABLE IF EXISTS `boat_A35`;
DROP TABLE IF EXISTS `boat_C5`;
DROP TABLE IF EXISTS `boat_C5v2`;
DROP TABLE IF EXISTS `boat_Class40`;
DROP TABLE IF EXISTS `boat_Imoca`;
DROP TABLE IF EXISTS `boat_Imoca2007`;
DROP TABLE IF EXISTS `boat_Imoca2008`;
DROP TABLE IF EXISTS `boat_Mono650`;
DROP TABLE IF EXISTS `boat_OEv2009`;
DROP TABLE IF EXISTS `boat_OceanExpress`;
DROP TABLE IF EXISTS `boat_VLM70`;
DROP TABLE IF EXISTS `boat_cigale14`;
DROP TABLE IF EXISTS `boat_debug`;
DROP TABLE IF EXISTS `boat_dnf`;
DROP TABLE IF EXISTS `boat_figaro`;
DROP TABLE IF EXISTS `boat_figaro2`;
DROP TABLE IF EXISTS `boat_hi5`;
DROP TABLE IF EXISTS `boat_imoca60`;
DROP TABLE IF EXISTS `boat_maxicata`;


#Status table for tracking module upgrades, anticipating v0.12
CREATE TABLE IF NOT EXISTS modules_status (
  `autoid`     bigint NOT NULL AUTO_INCREMENT,
  `updated`    timestamp,
  `serverid`   varchar(50)  default NULL,
  `moduleid`   varchar(50)  default NULL,
  `revid`      int(11)   default NULL,
  PRIMARY KEY  (`autoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='status modules';

#Log table for new admin interfaces
CREATE TABLE IF NOT EXISTS admin_changelog (
  `updated`    timestamp,
  `user`       varchar(255)  default NULL,
  `host`       varchar(255)  default NULL,
  `operation`  varchar(255)   default NULL,
  `tab`        varchar(255)  default NULL,
  `rowkey`     varchar(255)  default NULL,
  `col`        varchar(255)  default NULL,
  `oldval`     blob          default NULL,
  `newval`     blob          default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='new admin log table';

#Table pour stocker les flags
CREATE TABLE IF NOT EXISTS `flags` (
  `idflags` varchar(64) NOT NULL,
  `flag` longblob,
  `description` varchar(250),
  PRIMARY KEY  (`idflags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='content of flags';


#Table pour stocker les racesmap
CREATE TABLE IF NOT EXISTS `racesmap` (
  `idraces` int(11) NOT NULL,
  `racemap` longblob,
  PRIMARY KEY  (`idraces`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='racesmap';

#Clef auto pour pouvoir gèrer plus facilement les races instructions
ALTER TABLE races_instructions ADD autoid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "clef auto pour gestion de la table";


