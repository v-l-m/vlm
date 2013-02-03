--
-- Table structure for table `admin_tasks`
--

DROP TABLE IF EXISTS `admin_tasks`;
CREATE TABLE `admin_tasks` (
  `time` int(11) default NULL,
  `admin` varchar(64) default NULL,
  `action` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `auto_pilot`
--

DROP TABLE IF EXISTS `auto_pilot`;
CREATE TABLE `auto_pilot` (
  `taskid` int(11) NOT NULL auto_increment,
  `time` int(11) default NULL,
  `idusers` int(11) default NULL,
  `pilotmode` int(11) default NULL,
  `pilotparameter` varchar(32) default NULL,
  `status` varchar(32) default NULL,
  PRIMARY KEY  (`taskid`)
) ENGINE=MyISAM AUTO_INCREMENT=98171 DEFAULT CHARSET=latin1;




--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
CREATE TABLE `updates` (
  `time` bigint(14) default NULL,
  `races` int(11) default NULL,
  `boats` int(11) default NULL,
  `duration` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='last updates date';

--
-- Table structure for table `user_action`
--

DROP TABLE IF EXISTS `user_action`;
CREATE TABLE `user_action` (
  `time` int(11) default NULL,
  `idusers` int(11) default NULL,
  `ipaddr` varchar(16) default '0',
  `idraces` int(11) default NULL,
  `action` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_prefs`
--

DROP TABLE IF EXISTS `user_prefs`;
CREATE TABLE `user_prefs` (
  `idusers` int(11) NOT NULL default '0',
  `pref_name` varchar(64) NOT NULL default '',
  `pref_value` text,
  PRIMARY KEY  (`idusers`,`pref_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `idusers` int(11) NOT NULL auto_increment,
  `boattype` varchar(255) NOT NULL default 'boat_cigale14',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `boatname` varchar(255) NOT NULL default '',
  `color` varchar(255) NOT NULL default '',
  `boatheading` float default NULL,
  `pilotmode` varchar(255) NOT NULL default '',
  `pilotparameter` float default NULL,
  `engaged` int(11) NOT NULL default '0',
  `lastchange` int(11) default NULL,
  `email` varchar(200) default NULL,
  `nextwaypoint` int(11) default NULL,
  `userdeptime` bigint(20) default NULL,
  `lastupdate` int(11) default NULL,
  `loch` float default NULL,
  `country` varchar(64) default NULL,
  `class` varchar(32) default NULL,
  `targetlat` double default '0',
  `targetlong` double default '0',
  `targetandhdg` int(11) default '-1',
  `mooringtime` int(11) default '0',
  `releasetime` bigint(20) default '0',
  `hidepos` tinyint(4) default '0',
  `blocnote` varchar(250) default NULL,
  `ipaddr` varchar(16) default '0',
  `theme` varchar(30) default NULL,
  PRIMARY KEY  (`idusers`)
) ENGINE=MyISAM AUTO_INCREMENT=9338 DEFAULT CHARSET=latin1 COMMENT='Utilisateurs de VLM';


/*
 * TABLE WIND : utilisée par la fonction OLDwindAtPosition
 *
*/
--
-- Table structure for table `wind`
--
DROP TABLE IF EXISTS `wind`;
CREATE TABLE `wind` (
  `latitude` int(11) NOT NULL default '0',
  `longitude` int(11) NOT NULL default '0',
  `wspeed` float NOT NULL default '0',
  `wheading` int(11) NOT NULL default '0',
  `time` bigint(20) NOT NULL default '0',
  `uwind` float default NULL,
  `vwind` float default NULL,
  `uwind3` float default NULL,
  `vwind3` float default NULL,
  `uwind6` float default NULL,
  `vwind6` float default NULL,
  `uwind9` float default NULL,
  `vwind9` float default NULL,
  PRIMARY KEY  (`latitude`,`longitude`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='geographical grid contains wind info in knts';

/*
 * TABLE WINDS : utilisée par la fonction NEWwindAtPosition
                 CETTE FONCTION NE SERA JAMAIS MISE EN PRODUCTION (trop lente)
                 Cette table disparait (comme WINDS) en version 0.8, à la mise en route
                 de la fonction SPFwindAtPosition (utilisant un segment de SHM)
 *
*/
--
-- Table structure for table `winds`
--

DROP TABLE IF EXISTS `winds`;
CREATE TABLE `winds` (
  `latitude` int(11) NOT NULL default '0',
  `longitude` int(11) NOT NULL default '0',
  `time` bigint(20) NOT NULL default '0',
  `uwind` float default NULL,
  `vwind` float default NULL,
  PRIMARY KEY  (`latitude`,`longitude`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='geographical grid with wind data';

