--
-- Table structure for table `coastline_c`
--

DROP TABLE IF EXISTS `coastline_c`;
CREATE TABLE `coastline_c` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  PRIMARY KEY  (`idpoint`),
  KEY `idcoast` (`idcoast`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=12968 DEFAULT CHARSET=latin1 COMMENT='GSHHS c';

--
-- Table structure for table `coastline_f`
--

DROP TABLE IF EXISTS `coastline_f`;
CREATE TABLE `coastline_f` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  PRIMARY KEY  (`idpoint`),
  KEY `idcoast` (`idcoast`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=10340935 DEFAULT CHARSET=latin1 COMMENT='GSHHS f';

--
-- Table structure for table `coastline_h`
--

DROP TABLE IF EXISTS `coastline_h`;
CREATE TABLE `coastline_h` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  PRIMARY KEY  (`idpoint`),
  KEY `idcoast` (`idcoast`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=1906489 DEFAULT CHARSET=latin1 COMMENT='GSHHS h';

--
-- Table structure for table `coastline_i`
--

DROP TABLE IF EXISTS `coastline_i`;
CREATE TABLE `coastline_i` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  PRIMARY KEY  (`idpoint`),
  KEY `idcoast` (`idcoast`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=450664 DEFAULT CHARSET=latin1 COMMENT='GSHHS i';

--
-- Table structure for table `coastline_l`
--

DROP TABLE IF EXISTS `coastline_l`;
CREATE TABLE `coastline_l` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double default NULL,
  `latitude` double default NULL,
  PRIMARY KEY  (`idpoint`),
  KEY `idcoast` (`idcoast`),
  KEY `longitude` (`longitude`),
  KEY `latitude` (`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=90240 DEFAULT CHARSET=latin1 COMMENT='GSHHS l';
