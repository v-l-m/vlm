/*
 * RACES, WAYPOINTS, et RACES_WAYPOINTS
 * Créer une course, c'est :
         - ajouter une ligne dans "races"
         - ajounter N lignes dans "waypoints" (une par waypoint)
         - faire la relation 1,N (intégrant l'ordonnancement des WP) dans races_waypoint
         - jusqu'à la version 0.8 incluse, la table races_instructions n'est pas utilisée
*/
--
-- Table structure for table `races`
--

DROP TABLE IF EXISTS `races`;
CREATE TABLE `races` (
  `idraces` int(11) NOT NULL auto_increment,
  `racename` varchar(255) NOT NULL default '',
  `started` int(11) NOT NULL default '0',
  `deptime` bigint(14) default NULL,
  `startlong` int(11) NOT NULL default '0',
  `startlat` int(11) NOT NULL default '0',
  `boattype` varchar(255) default NULL,
  `closetime` bigint(20) default NULL,
  `racetype` int(11) default NULL,
  `firstpcttime` bigint(20) default NULL,
  `depend_on` int(11) default NULL,
  `qualifying_races` text,
  `idchallenge` text,
  `coastpenalty` int(11) default '0',
  `bobegin` bigint(20) default '0',
  `boend` bigint(20) default '0',
  `maxboats` int(11) default '0',
  `theme` varchar(30) default NULL,
  PRIMARY KEY  (`idraces`)
) ENGINE=MyISAM AUTO_INCREMENT=2008443516 DEFAULT CHARSET=latin1 COMMENT='The races that exist';

--
-- Table structure for table `races_instructions`
--

DROP TABLE IF EXISTS `races_instructions`;
CREATE TABLE `races_instructions` (
  `idraces` int(11) default NULL,
  `instructions` text,
  `flag` int(11),
  KEY (`idraces`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `waypoints`
--
-- si latitude1 = latitude2 ET longitude1 = longitude2, alors le wp est de type laisser_au
-- Cf. table races_waypoints

DROP TABLE IF EXISTS `waypoints`;
CREATE TABLE `waypoints` (
  `idwaypoint` int(20) NOT NULL default '0',
  `wptype` int NOT NULL default '0',
  `latitude1` double default NULL,
  `longitude1` double default NULL,
  `latitude2` double default NULL,
  `longitude2` double default NULL,
  `libelle` varchar(255) default NULL,
  `maparea` int(11) default '10',
  PRIMARY KEY  (`idwaypoint`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='waypoints';

--
-- Table structure for table `races_waypoints`
--

DROP TABLE IF EXISTS `races_waypoints`;
CREATE TABLE `races_waypoints` (
  `idraces` int(11) NOT NULL default '0',
  `wporder` int(11) NOT NULL default '0',
  `idwaypoint` int(20) default NULL,
  `wpformat` int NOT NULL default '0',
  `laisser_au` int(11) default NULL,
  `wptype` varchar(32) default NULL,
  PRIMARY KEY  (`idraces`,`wporder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Race waypoints';

--
-- Table structure for table `histpos`
--

DROP TABLE IF EXISTS `histpos`;
CREATE TABLE `histpos` (
  `time` bigint(20) default NULL,
  `long` double default NULL,
  `lat` double default NULL,
  `idusers` int(11) NOT NULL default '0',
  `race` int(11) default NULL,
  `wind` text,
  KEY `race` (`race`),
  KEY `idusers` (`idusers`),
  KEY `time` (`time`,`race`,`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
CREATE TABLE `positions` (
  `time` bigint(20) default NULL,
  `long` double default NULL,
  `lat` double default NULL,
  `idusers` int(11) NOT NULL default '0',
  `race` int(11) default NULL,
  `wind` text,
  KEY `race` (`race`),
  KEY `idusers` (`idusers`),
  KEY `time` (`time`,`race`,`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `waypoint_crossing`
--

DROP TABLE IF EXISTS `waypoint_crossing`;
CREATE TABLE `waypoint_crossing` (
  `idraces` int(11) NOT NULL,
  `idwaypoint` int(11) NOT NULL,
  `idusers` int(11) NOT NULL,
  `time` int(11) default NULL,
  `userdeptime` int(20) default NULL,
  PRIMARY KEY  (`idraces`,`idwaypoint`,`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `races_ranking`
--

DROP TABLE IF EXISTS `races_ranking`;
CREATE TABLE `races_ranking` (
  `idraces` int(11) NOT NULL default '0',
  `idusers` int(11) NOT NULL default '0',
  `nwp` int(11) default NULL,
  `dnm` float default NULL,
  `latitude` float default NULL,
  `longitude` float default NULL,
  `last1h` float default NULL,
  `last3h` float default NULL,
  `last24h` float default NULL,
  `nmlat` double default NULL,
  `nmlong` double default NULL,
  `loch` float default NULL,
  PRIMARY KEY  (`idraces`,`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='The current races ranking';

--
-- Table structure for table `races_results`
--

DROP TABLE IF EXISTS `races_results`;
CREATE TABLE `races_results` (
  `idraces` int(11) NOT NULL default '0',
  `idusers` int(11) NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `duration` int(11) NOT NULL default '0',
  `longitude` float default NULL,
  `latitude` float default NULL,
  `deptime` int(11) default NULL,
  `loch` float default NULL,
  `penalty` int(11) default '0',
  PRIMARY KEY  (`idraces`,`idusers`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

