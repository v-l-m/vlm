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
  PRIMARY KEY  (`idraces`)
) ENGINE=MyISAM AUTO_INCREMENT=2008443516 DEFAULT CHARSET=latin1 COMMENT='The races that exist';

--
-- Table structure for table `races_instructions`
--

DROP TABLE IF EXISTS `races_instructions`;
CREATE TABLE `races_instructions` (
  `idraces` int(11) default NULL,
  `instructions` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


--
-- Table structure for table `waypoints`
--

DROP TABLE IF EXISTS `waypoints`;
CREATE TABLE `waypoints` (
  `idwaypoint` int(20) NOT NULL default '0',
  `latitude1` double default NULL,
  `longitude1` double default NULL,
  `latitude2` double default NULL,
  `longitude2` double default NULL,
  `libelle` varchar(255) default NULL,
  `maparea` int(11) default '10',
  PRIMARY KEY  (`idwaypoint`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='waypoints';

