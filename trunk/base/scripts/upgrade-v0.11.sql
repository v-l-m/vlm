#Table pour stocker les flags
CREATE TABLE IF NOT EXISTS `flags` (
  `idflags` varchar(25) NOT NULL,
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

