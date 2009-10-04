#Clef auto pour pouvoir gèrer plus facilement les races instructions
ALTER TABLE races_instructions ADD autoid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT "clef auto pour gestion de la table";

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

