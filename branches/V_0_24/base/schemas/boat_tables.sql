
--
-- Table structure for table `boat_A35`
--

DROP TABLE IF EXISTS `boat_A35`;
CREATE TABLE `boat_A35` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the A35';

--
-- Table structure for table `boat_C5`
--

DROP TABLE IF EXISTS `boat_C5`;
CREATE TABLE `boat_C5` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the C5';

--
-- Table structure for table `boat_C5v2`
--

DROP TABLE IF EXISTS `boat_C5v2`;
CREATE TABLE `boat_C5v2` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the C5v2';

--
-- Table structure for table `boat_Class40`
--

DROP TABLE IF EXISTS `boat_Class40`;
CREATE TABLE `boat_Class40` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the Class40';

--
-- Table structure for table `boat_G3`
--

DROP TABLE IF EXISTS `boat_G3`;
CREATE TABLE `boat_G3` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the G3';

--
-- Table structure for table `boat_Imoca`
--

DROP TABLE IF EXISTS `boat_Imoca`;
CREATE TABLE `boat_Imoca` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the Imoca';

--
-- Table structure for table `boat_Imoca2007`
--

DROP TABLE IF EXISTS `boat_Imoca2007`;
CREATE TABLE `boat_Imoca2007` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the Imoca2007';

--
-- Table structure for table `boat_Imoca2008`
--

DROP TABLE IF EXISTS `boat_Imoca2008`;
CREATE TABLE `boat_Imoca2008` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the Imoca2008';

--
-- Table structure for table `boat_OceanExpress`
--

DROP TABLE IF EXISTS `boat_OceanExpress`;
CREATE TABLE `boat_OceanExpress` (
  `wspeed` int(11) NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the OceanExpress';

--
-- Table structure for table `boat_VLM70`
--

DROP TABLE IF EXISTS `boat_VLM70`;
CREATE TABLE `boat_VLM70` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the VLM70';

--
-- Table structure for table `boat_cigale14`
--

DROP TABLE IF EXISTS `boat_cigale14`;
CREATE TABLE `boat_cigale14` (
  `wspeed` int(11) NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the cigale14';

--
-- Table structure for table `boat_debug`
--

DROP TABLE IF EXISTS `boat_debug`;
CREATE TABLE `boat_debug` (
  `wspeed` float default NULL,
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the debug';

--
-- Table structure for table `boat_dnf`
--

DROP TABLE IF EXISTS `boat_dnf`;
CREATE TABLE `boat_dnf` (
  `wspeed` int(11) NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Chart speed for sunk boat';

--
-- Table structure for table `boat_figaro`
--

DROP TABLE IF EXISTS `boat_figaro`;
CREATE TABLE `boat_figaro` (
  `wspeed` int(11) NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the figaro';

--
-- Table structure for table `boat_figaro2`
--

DROP TABLE IF EXISTS `boat_figaro2`;
CREATE TABLE `boat_figaro2` (
  `wspeed` float default NULL,
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the figaro2';

--
-- Table structure for table `boat_hi5`
--

DROP TABLE IF EXISTS `boat_hi5`;
CREATE TABLE `boat_hi5` (
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0',
  PRIMARY KEY  (`wspeed`,`wheading`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the hi5';

--
-- Table structure for table `boat_imoca60`
--

DROP TABLE IF EXISTS `boat_imoca60`;
CREATE TABLE `boat_imoca60` (
  `wspeed` int(11) NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the imoca 60';

--
-- Table structure for table `boat_maxicata`
--

DROP TABLE IF EXISTS `boat_maxicata`;
CREATE TABLE `boat_maxicata` (
  `wspeed` float default NULL,
  `wheading` int(11) NOT NULL default '0',
  `boatspeed` float NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='speed chart for the maxicata';
