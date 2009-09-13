-- MySQL dump 10.11
--
-- Host: localhost    Database: vlmdev
-- ------------------------------------------------------
-- Server version  5.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `CH_0_0`
--

DROP TABLE IF EXISTS `CH_0_0`;
CREATE TABLE `CH_0_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4199826 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-80)';

--
-- Table structure for table `CH_0_10`
--

DROP TABLE IF EXISTS `CH_0_10`;
CREATE TABLE `CH_0_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-70)';

--
-- Table structure for table `CH_0_100`
--

DROP TABLE IF EXISTS `CH_0_100`;
CREATE TABLE `CH_0_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10280258 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>20)';

--
-- Table structure for table `CH_0_110`
--

DROP TABLE IF EXISTS `CH_0_110`;
CREATE TABLE `CH_0_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>30)';

--
-- Table structure for table `CH_0_120`
--

DROP TABLE IF EXISTS `CH_0_120`;
CREATE TABLE `CH_0_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>40)';

--
-- Table structure for table `CH_0_130`
--

DROP TABLE IF EXISTS `CH_0_130`;
CREATE TABLE `CH_0_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331569 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>50)';

--
-- Table structure for table `CH_0_140`
--

DROP TABLE IF EXISTS `CH_0_140`;
CREATE TABLE `CH_0_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334639 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>60)';

--
-- Table structure for table `CH_0_150`
--

DROP TABLE IF EXISTS `CH_0_150`;
CREATE TABLE `CH_0_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10279894 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>70)';

--
-- Table structure for table `CH_0_160`
--

DROP TABLE IF EXISTS `CH_0_160`;
CREATE TABLE `CH_0_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_0_170`
--

DROP TABLE IF EXISTS `CH_0_170`;
CREATE TABLE `CH_0_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_0_180`
--

DROP TABLE IF EXISTS `CH_0_180`;
CREATE TABLE `CH_0_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_0_20`
--

DROP TABLE IF EXISTS `CH_0_20`;
CREATE TABLE `CH_0_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-60)';

--
-- Table structure for table `CH_0_30`
--

DROP TABLE IF EXISTS `CH_0_30`;
CREATE TABLE `CH_0_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10311213 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-50)';

--
-- Table structure for table `CH_0_40`
--

DROP TABLE IF EXISTS `CH_0_40`;
CREATE TABLE `CH_0_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9777778 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-40)';

--
-- Table structure for table `CH_0_50`
--

DROP TABLE IF EXISTS `CH_0_50`;
CREATE TABLE `CH_0_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338114 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-30)';

--
-- Table structure for table `CH_0_60`
--

DROP TABLE IF EXISTS `CH_0_60`;
CREATE TABLE `CH_0_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338134 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-20)';

--
-- Table structure for table `CH_0_70`
--

DROP TABLE IF EXISTS `CH_0_70`;
CREATE TABLE `CH_0_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10249094 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>-10)';

--
-- Table structure for table `CH_0_80`
--

DROP TABLE IF EXISTS `CH_0_80`;
CREATE TABLE `CH_0_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8660612 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>0)';

--
-- Table structure for table `CH_0_90`
--

DROP TABLE IF EXISTS `CH_0_90`;
CREATE TABLE `CH_0_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-180,lat>10)';

--
-- Table structure for table `CH_100_0`
--

DROP TABLE IF EXISTS `CH_100_0`;
CREATE TABLE `CH_100_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9570336 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-80)';

--
-- Table structure for table `CH_100_10`
--

DROP TABLE IF EXISTS `CH_100_10`;
CREATE TABLE `CH_100_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10028888 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-70)';

--
-- Table structure for table `CH_100_100`
--

DROP TABLE IF EXISTS `CH_100_100`;
CREATE TABLE `CH_100_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337859 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>20)';

--
-- Table structure for table `CH_100_110`
--

DROP TABLE IF EXISTS `CH_100_110`;
CREATE TABLE `CH_100_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334424 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>30)';

--
-- Table structure for table `CH_100_120`
--

DROP TABLE IF EXISTS `CH_100_120`;
CREATE TABLE `CH_100_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334259 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>40)';

--
-- Table structure for table `CH_100_130`
--

DROP TABLE IF EXISTS `CH_100_130`;
CREATE TABLE `CH_100_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339431 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>50)';

--
-- Table structure for table `CH_100_140`
--

DROP TABLE IF EXISTS `CH_100_140`;
CREATE TABLE `CH_100_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338975 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>60)';

--
-- Table structure for table `CH_100_150`
--

DROP TABLE IF EXISTS `CH_100_150`;
CREATE TABLE `CH_100_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338763 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>70)';

--
-- Table structure for table `CH_100_160`
--

DROP TABLE IF EXISTS `CH_100_160`;
CREATE TABLE `CH_100_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_100_170`
--

DROP TABLE IF EXISTS `CH_100_170`;
CREATE TABLE `CH_100_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_100_180`
--

DROP TABLE IF EXISTS `CH_100_180`;
CREATE TABLE `CH_100_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_100_20`
--

DROP TABLE IF EXISTS `CH_100_20`;
CREATE TABLE `CH_100_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337939 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-60)';

--
-- Table structure for table `CH_100_30`
--

DROP TABLE IF EXISTS `CH_100_30`;
CREATE TABLE `CH_100_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10333624 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-50)';

--
-- Table structure for table `CH_100_40`
--

DROP TABLE IF EXISTS `CH_100_40`;
CREATE TABLE `CH_100_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10309011 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-40)';

--
-- Table structure for table `CH_100_50`
--

DROP TABLE IF EXISTS `CH_100_50`;
CREATE TABLE `CH_100_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10248681 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-30)';

--
-- Table structure for table `CH_100_60`
--

DROP TABLE IF EXISTS `CH_100_60`;
CREATE TABLE `CH_100_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336774 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-20)';

--
-- Table structure for table `CH_100_70`
--

DROP TABLE IF EXISTS `CH_100_70`;
CREATE TABLE `CH_100_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10325549 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>-10)';

--
-- Table structure for table `CH_100_80`
--

DROP TABLE IF EXISTS `CH_100_80`;
CREATE TABLE `CH_100_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10230005 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>0)';

--
-- Table structure for table `CH_100_90`
--

DROP TABLE IF EXISTS `CH_100_90`;
CREATE TABLE `CH_100_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330729 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-80,lat>10)';

--
-- Table structure for table `CH_10_0`
--

DROP TABLE IF EXISTS `CH_10_0`;
CREATE TABLE `CH_10_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4199743 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-80)';

--
-- Table structure for table `CH_10_10`
--

DROP TABLE IF EXISTS `CH_10_10`;
CREATE TABLE `CH_10_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-70)';

--
-- Table structure for table `CH_10_100`
--

DROP TABLE IF EXISTS `CH_10_100`;
CREATE TABLE `CH_10_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9680371 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>20)';

--
-- Table structure for table `CH_10_110`
--

DROP TABLE IF EXISTS `CH_10_110`;
CREATE TABLE `CH_10_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>30)';

--
-- Table structure for table `CH_10_120`
--

DROP TABLE IF EXISTS `CH_10_120`;
CREATE TABLE `CH_10_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>40)';

--
-- Table structure for table `CH_10_130`
--

DROP TABLE IF EXISTS `CH_10_130`;
CREATE TABLE `CH_10_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10325849 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>50)';

--
-- Table structure for table `CH_10_140`
--

DROP TABLE IF EXISTS `CH_10_140`;
CREATE TABLE `CH_10_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10290604 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>60)';

--
-- Table structure for table `CH_10_150`
--

DROP TABLE IF EXISTS `CH_10_150`;
CREATE TABLE `CH_10_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10270143 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>70)';

--
-- Table structure for table `CH_10_160`
--

DROP TABLE IF EXISTS `CH_10_160`;
CREATE TABLE `CH_10_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_10_170`
--

DROP TABLE IF EXISTS `CH_10_170`;
CREATE TABLE `CH_10_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_10_180`
--

DROP TABLE IF EXISTS `CH_10_180`;
CREATE TABLE `CH_10_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_10_20`
--

DROP TABLE IF EXISTS `CH_10_20`;
CREATE TABLE `CH_10_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-60)';

--
-- Table structure for table `CH_10_30`
--

DROP TABLE IF EXISTS `CH_10_30`;
CREATE TABLE `CH_10_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-50)';

--
-- Table structure for table `CH_10_40`
--

DROP TABLE IF EXISTS `CH_10_40`;
CREATE TABLE `CH_10_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-40)';

--
-- Table structure for table `CH_10_50`
--

DROP TABLE IF EXISTS `CH_10_50`;
CREATE TABLE `CH_10_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-30)';

--
-- Table structure for table `CH_10_60`
--

DROP TABLE IF EXISTS `CH_10_60`;
CREATE TABLE `CH_10_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10250333 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-20)';

--
-- Table structure for table `CH_10_70`
--

DROP TABLE IF EXISTS `CH_10_70`;
CREATE TABLE `CH_10_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8714952 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>-10)';

--
-- Table structure for table `CH_10_80`
--

DROP TABLE IF EXISTS `CH_10_80`;
CREATE TABLE `CH_10_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10108848 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>0)';

--
-- Table structure for table `CH_10_90`
--

DROP TABLE IF EXISTS `CH_10_90`;
CREATE TABLE `CH_10_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9848005 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-170,lat>10)';

--
-- Table structure for table `CH_110_0`
--

DROP TABLE IF EXISTS `CH_110_0`;
CREATE TABLE `CH_110_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4196339 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-80)';

--
-- Table structure for table `CH_110_10`
--

DROP TABLE IF EXISTS `CH_110_10`;
CREATE TABLE `CH_110_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10233064 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-70)';

--
-- Table structure for table `CH_110_100`
--

DROP TABLE IF EXISTS `CH_110_100`;
CREATE TABLE `CH_110_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>20)';

--
-- Table structure for table `CH_110_110`
--

DROP TABLE IF EXISTS `CH_110_110`;
CREATE TABLE `CH_110_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10272012 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>30)';

--
-- Table structure for table `CH_110_120`
--

DROP TABLE IF EXISTS `CH_110_120`;
CREATE TABLE `CH_110_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334889 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>40)';

--
-- Table structure for table `CH_110_130`
--

DROP TABLE IF EXISTS `CH_110_130`;
CREATE TABLE `CH_110_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338174 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>50)';

--
-- Table structure for table `CH_110_140`
--

DROP TABLE IF EXISTS `CH_110_140`;
CREATE TABLE `CH_110_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338963 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>60)';

--
-- Table structure for table `CH_110_150`
--

DROP TABLE IF EXISTS `CH_110_150`;
CREATE TABLE `CH_110_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10318341 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>70)';

--
-- Table structure for table `CH_110_160`
--

DROP TABLE IF EXISTS `CH_110_160`;
CREATE TABLE `CH_110_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_110_170`
--

DROP TABLE IF EXISTS `CH_110_170`;
CREATE TABLE `CH_110_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_110_180`
--

DROP TABLE IF EXISTS `CH_110_180`;
CREATE TABLE `CH_110_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_110_20`
--

DROP TABLE IF EXISTS `CH_110_20`;
CREATE TABLE `CH_110_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330344 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-60)';

--
-- Table structure for table `CH_110_30`
--

DROP TABLE IF EXISTS `CH_110_30`;
CREATE TABLE `CH_110_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10081640 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-50)';

--
-- Table structure for table `CH_110_40`
--

DROP TABLE IF EXISTS `CH_110_40`;
CREATE TABLE `CH_110_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10083720 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-40)';

--
-- Table structure for table `CH_110_50`
--

DROP TABLE IF EXISTS `CH_110_50`;
CREATE TABLE `CH_110_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9750994 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-30)';

--
-- Table structure for table `CH_110_60`
--

DROP TABLE IF EXISTS `CH_110_60`;
CREATE TABLE `CH_110_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9650635 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-20)';

--
-- Table structure for table `CH_110_70`
--

DROP TABLE IF EXISTS `CH_110_70`;
CREATE TABLE `CH_110_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8224712 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>-10)';

--
-- Table structure for table `CH_110_80`
--

DROP TABLE IF EXISTS `CH_110_80`;
CREATE TABLE `CH_110_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10225728 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>0)';

--
-- Table structure for table `CH_110_90`
--

DROP TABLE IF EXISTS `CH_110_90`;
CREATE TABLE `CH_110_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338214 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-70,lat>10)';

--
-- Table structure for table `CH_120_0`
--

DROP TABLE IF EXISTS `CH_120_0`;
CREATE TABLE `CH_120_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4194442 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-80)';

--
-- Table structure for table `CH_120_10`
--

DROP TABLE IF EXISTS `CH_120_10`;
CREATE TABLE `CH_120_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10323321 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-70)';

--
-- Table structure for table `CH_120_100`
--

DROP TABLE IF EXISTS `CH_120_100`;
CREATE TABLE `CH_120_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>20)';

--
-- Table structure for table `CH_120_110`
--

DROP TABLE IF EXISTS `CH_120_110`;
CREATE TABLE `CH_120_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>30)';

--
-- Table structure for table `CH_120_120`
--

DROP TABLE IF EXISTS `CH_120_120`;
CREATE TABLE `CH_120_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334654 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>40)';

--
-- Table structure for table `CH_120_130`
--

DROP TABLE IF EXISTS `CH_120_130`;
CREATE TABLE `CH_120_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10333924 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>50)';

--
-- Table structure for table `CH_120_140`
--

DROP TABLE IF EXISTS `CH_120_140`;
CREATE TABLE `CH_120_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338264 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>60)';

--
-- Table structure for table `CH_120_150`
--

DROP TABLE IF EXISTS `CH_120_150`;
CREATE TABLE `CH_120_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336184 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>70)';

--
-- Table structure for table `CH_120_160`
--

DROP TABLE IF EXISTS `CH_120_160`;
CREATE TABLE `CH_120_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_120_170`
--

DROP TABLE IF EXISTS `CH_120_170`;
CREATE TABLE `CH_120_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_120_180`
--

DROP TABLE IF EXISTS `CH_120_180`;
CREATE TABLE `CH_120_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_120_20`
--

DROP TABLE IF EXISTS `CH_120_20`;
CREATE TABLE `CH_120_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335469 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-60)';

--
-- Table structure for table `CH_120_30`
--

DROP TABLE IF EXISTS `CH_120_30`;
CREATE TABLE `CH_120_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-50)';

--
-- Table structure for table `CH_120_40`
--

DROP TABLE IF EXISTS `CH_120_40`;
CREATE TABLE `CH_120_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10328989 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-40)';

--
-- Table structure for table `CH_120_50`
--

DROP TABLE IF EXISTS `CH_120_50`;
CREATE TABLE `CH_120_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8980052 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-30)';

--
-- Table structure for table `CH_120_60`
--

DROP TABLE IF EXISTS `CH_120_60`;
CREATE TABLE `CH_120_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=7575342 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-20)';

--
-- Table structure for table `CH_120_70`
--

DROP TABLE IF EXISTS `CH_120_70`;
CREATE TABLE `CH_120_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10238979 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>-10)';

--
-- Table structure for table `CH_120_80`
--

DROP TABLE IF EXISTS `CH_120_80`;
CREATE TABLE `CH_120_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10099144 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>0)';

--
-- Table structure for table `CH_120_90`
--

DROP TABLE IF EXISTS `CH_120_90`;
CREATE TABLE `CH_120_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=6110582 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-60,lat>10)';

--
-- Table structure for table `CH_130_0`
--

DROP TABLE IF EXISTS `CH_130_0`;
CREATE TABLE `CH_130_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4194051 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-80)';

--
-- Table structure for table `CH_130_10`
--

DROP TABLE IF EXISTS `CH_130_10`;
CREATE TABLE `CH_130_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10215592 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-70)';

--
-- Table structure for table `CH_130_100`
--

DROP TABLE IF EXISTS `CH_130_100`;
CREATE TABLE `CH_130_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>20)';

--
-- Table structure for table `CH_130_110`
--

DROP TABLE IF EXISTS `CH_130_110`;
CREATE TABLE `CH_130_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>30)';

--
-- Table structure for table `CH_130_120`
--

DROP TABLE IF EXISTS `CH_130_120`;
CREATE TABLE `CH_130_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>40)';

--
-- Table structure for table `CH_130_130`
--

DROP TABLE IF EXISTS `CH_130_130`;
CREATE TABLE `CH_130_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338284 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>50)';

--
-- Table structure for table `CH_130_140`
--

DROP TABLE IF EXISTS `CH_130_140`;
CREATE TABLE `CH_130_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338284 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>60)';

--
-- Table structure for table `CH_130_150`
--

DROP TABLE IF EXISTS `CH_130_150`;
CREATE TABLE `CH_130_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>70)';

--
-- Table structure for table `CH_130_160`
--

DROP TABLE IF EXISTS `CH_130_160`;
CREATE TABLE `CH_130_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_130_170`
--

DROP TABLE IF EXISTS `CH_130_170`;
CREATE TABLE `CH_130_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_130_180`
--

DROP TABLE IF EXISTS `CH_130_180`;
CREATE TABLE `CH_130_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_130_20`
--

DROP TABLE IF EXISTS `CH_130_20`;
CREATE TABLE `CH_130_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-60)';

--
-- Table structure for table `CH_130_30`
--

DROP TABLE IF EXISTS `CH_130_30`;
CREATE TABLE `CH_130_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-50)';

--
-- Table structure for table `CH_130_40`
--

DROP TABLE IF EXISTS `CH_130_40`;
CREATE TABLE `CH_130_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=1490018 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-40)';

--
-- Table structure for table `CH_130_50`
--

DROP TABLE IF EXISTS `CH_130_50`;
CREATE TABLE `CH_130_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10323537 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-30)';

--
-- Table structure for table `CH_130_60`
--

DROP TABLE IF EXISTS `CH_130_60`;
CREATE TABLE `CH_130_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9741868 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-20)';

--
-- Table structure for table `CH_130_70`
--

DROP TABLE IF EXISTS `CH_130_70`;
CREATE TABLE `CH_130_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10241933 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>-10)';

--
-- Table structure for table `CH_130_80`
--

DROP TABLE IF EXISTS `CH_130_80`;
CREATE TABLE `CH_130_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9726073 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>0)';

--
-- Table structure for table `CH_130_90`
--

DROP TABLE IF EXISTS `CH_130_90`;
CREATE TABLE `CH_130_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-50,lat>10)';

--
-- Table structure for table `CH_140_0`
--

DROP TABLE IF EXISTS `CH_140_0`;
CREATE TABLE `CH_140_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4193826 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-80)';

--
-- Table structure for table `CH_140_10`
--

DROP TABLE IF EXISTS `CH_140_10`;
CREATE TABLE `CH_140_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-70)';

--
-- Table structure for table `CH_140_100`
--

DROP TABLE IF EXISTS `CH_140_100`;
CREATE TABLE `CH_140_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>20)';

--
-- Table structure for table `CH_140_110`
--

DROP TABLE IF EXISTS `CH_140_110`;
CREATE TABLE `CH_140_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10259895 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>30)';

--
-- Table structure for table `CH_140_120`
--

DROP TABLE IF EXISTS `CH_140_120`;
CREATE TABLE `CH_140_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>40)';

--
-- Table structure for table `CH_140_130`
--

DROP TABLE IF EXISTS `CH_140_130`;
CREATE TABLE `CH_140_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>50)';

--
-- Table structure for table `CH_140_140`
--

DROP TABLE IF EXISTS `CH_140_140`;
CREATE TABLE `CH_140_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331664 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>60)';

--
-- Table structure for table `CH_140_150`
--

DROP TABLE IF EXISTS `CH_140_150`;
CREATE TABLE `CH_140_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>70)';

--
-- Table structure for table `CH_140_160`
--

DROP TABLE IF EXISTS `CH_140_160`;
CREATE TABLE `CH_140_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_140_170`
--

DROP TABLE IF EXISTS `CH_140_170`;
CREATE TABLE `CH_140_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_140_180`
--

DROP TABLE IF EXISTS `CH_140_180`;
CREATE TABLE `CH_140_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_140_20`
--

DROP TABLE IF EXISTS `CH_140_20`;
CREATE TABLE `CH_140_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336914 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-60)';

--
-- Table structure for table `CH_140_30`
--

DROP TABLE IF EXISTS `CH_140_30`;
CREATE TABLE `CH_140_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-50)';

--
-- Table structure for table `CH_140_40`
--

DROP TABLE IF EXISTS `CH_140_40`;
CREATE TABLE `CH_140_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-40)';

--
-- Table structure for table `CH_140_50`
--

DROP TABLE IF EXISTS `CH_140_50`;
CREATE TABLE `CH_140_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=1512018 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-30)';

--
-- Table structure for table `CH_140_60`
--

DROP TABLE IF EXISTS `CH_140_60`;
CREATE TABLE `CH_140_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10250340 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-20)';

--
-- Table structure for table `CH_140_70`
--

DROP TABLE IF EXISTS `CH_140_70`;
CREATE TABLE `CH_140_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10245230 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>-10)';

--
-- Table structure for table `CH_140_80`
--

DROP TABLE IF EXISTS `CH_140_80`;
CREATE TABLE `CH_140_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>0)';

--
-- Table structure for table `CH_140_90`
--

DROP TABLE IF EXISTS `CH_140_90`;
CREATE TABLE `CH_140_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-40,lat>10)';

--
-- Table structure for table `CH_150_0`
--

DROP TABLE IF EXISTS `CH_150_0`;
CREATE TABLE `CH_150_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4193546 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-80)';

--
-- Table structure for table `CH_150_10`
--

DROP TABLE IF EXISTS `CH_150_10`;
CREATE TABLE `CH_150_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-70)';

--
-- Table structure for table `CH_150_100`
--

DROP TABLE IF EXISTS `CH_150_100`;
CREATE TABLE `CH_150_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>20)';

--
-- Table structure for table `CH_150_110`
--

DROP TABLE IF EXISTS `CH_150_110`;
CREATE TABLE `CH_150_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10260028 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>30)';

--
-- Table structure for table `CH_150_120`
--

DROP TABLE IF EXISTS `CH_150_120`;
CREATE TABLE `CH_150_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>40)';

--
-- Table structure for table `CH_150_130`
--

DROP TABLE IF EXISTS `CH_150_130`;
CREATE TABLE `CH_150_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>50)';

--
-- Table structure for table `CH_150_140`
--

DROP TABLE IF EXISTS `CH_150_140`;
CREATE TABLE `CH_150_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10308921 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>60)';

--
-- Table structure for table `CH_150_150`
--

DROP TABLE IF EXISTS `CH_150_150`;
CREATE TABLE `CH_150_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336294 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>70)';

--
-- Table structure for table `CH_150_160`
--

DROP TABLE IF EXISTS `CH_150_160`;
CREATE TABLE `CH_150_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_150_170`
--

DROP TABLE IF EXISTS `CH_150_170`;
CREATE TABLE `CH_150_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_150_180`
--

DROP TABLE IF EXISTS `CH_150_180`;
CREATE TABLE `CH_150_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_150_20`
--

DROP TABLE IF EXISTS `CH_150_20`;
CREATE TABLE `CH_150_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10068168 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-60)';

--
-- Table structure for table `CH_150_30`
--

DROP TABLE IF EXISTS `CH_150_30`;
CREATE TABLE `CH_150_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-50)';

--
-- Table structure for table `CH_150_40`
--

DROP TABLE IF EXISTS `CH_150_40`;
CREATE TABLE `CH_150_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-40)';

--
-- Table structure for table `CH_150_50`
--

DROP TABLE IF EXISTS `CH_150_50`;
CREATE TABLE `CH_150_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9750868 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-30)';

--
-- Table structure for table `CH_150_60`
--

DROP TABLE IF EXISTS `CH_150_60`;
CREATE TABLE `CH_150_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-20)';

--
-- Table structure for table `CH_150_70`
--

DROP TABLE IF EXISTS `CH_150_70`;
CREATE TABLE `CH_150_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>-10)';

--
-- Table structure for table `CH_150_80`
--

DROP TABLE IF EXISTS `CH_150_80`;
CREATE TABLE `CH_150_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>0)';

--
-- Table structure for table `CH_150_90`
--

DROP TABLE IF EXISTS `CH_150_90`;
CREATE TABLE `CH_150_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10211903 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-30,lat>10)';

--
-- Table structure for table `CH_160_0`
--

DROP TABLE IF EXISTS `CH_160_0`;
CREATE TABLE `CH_160_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4193235 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-80)';

--
-- Table structure for table `CH_160_10`
--

DROP TABLE IF EXISTS `CH_160_10`;
CREATE TABLE `CH_160_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-70)';

--
-- Table structure for table `CH_160_100`
--

DROP TABLE IF EXISTS `CH_160_100`;
CREATE TABLE `CH_160_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335919 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>20)';

--
-- Table structure for table `CH_160_110`
--

DROP TABLE IF EXISTS `CH_160_110`;
CREATE TABLE `CH_160_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335919 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>30)';

--
-- Table structure for table `CH_160_120`
--

DROP TABLE IF EXISTS `CH_160_120`;
CREATE TABLE `CH_160_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>40)';

--
-- Table structure for table `CH_160_130`
--

DROP TABLE IF EXISTS `CH_160_130`;
CREATE TABLE `CH_160_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10213870 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>50)';

--
-- Table structure for table `CH_160_140`
--

DROP TABLE IF EXISTS `CH_160_140`;
CREATE TABLE `CH_160_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335604 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>60)';

--
-- Table structure for table `CH_160_150`
--

DROP TABLE IF EXISTS `CH_160_150`;
CREATE TABLE `CH_160_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330774 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>70)';

--
-- Table structure for table `CH_160_160`
--

DROP TABLE IF EXISTS `CH_160_160`;
CREATE TABLE `CH_160_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_160_170`
--

DROP TABLE IF EXISTS `CH_160_170`;
CREATE TABLE `CH_160_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_160_180`
--

DROP TABLE IF EXISTS `CH_160_180`;
CREATE TABLE `CH_160_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_160_20`
--

DROP TABLE IF EXISTS `CH_160_20`;
CREATE TABLE `CH_160_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-60)';

--
-- Table structure for table `CH_160_30`
--

DROP TABLE IF EXISTS `CH_160_30`;
CREATE TABLE `CH_160_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9911311 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-50)';

--
-- Table structure for table `CH_160_40`
--

DROP TABLE IF EXISTS `CH_160_40`;
CREATE TABLE `CH_160_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9620936 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-40)';

--
-- Table structure for table `CH_160_50`
--

DROP TABLE IF EXISTS `CH_160_50`;
CREATE TABLE `CH_160_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-30)';

--
-- Table structure for table `CH_160_60`
--

DROP TABLE IF EXISTS `CH_160_60`;
CREATE TABLE `CH_160_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-20)';

--
-- Table structure for table `CH_160_70`
--

DROP TABLE IF EXISTS `CH_160_70`;
CREATE TABLE `CH_160_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9710485 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>-10)';

--
-- Table structure for table `CH_160_80`
--

DROP TABLE IF EXISTS `CH_160_80`;
CREATE TABLE `CH_160_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10229949 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>0)';

--
-- Table structure for table `CH_160_90`
--

DROP TABLE IF EXISTS `CH_160_90`;
CREATE TABLE `CH_160_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338184 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-20,lat>10)';

--
-- Table structure for table `CH_170_0`
--

DROP TABLE IF EXISTS `CH_170_0`;
CREATE TABLE `CH_170_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4192965 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-80)';

--
-- Table structure for table `CH_170_10`
--

DROP TABLE IF EXISTS `CH_170_10`;
CREATE TABLE `CH_170_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4207444 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-70)';

--
-- Table structure for table `CH_170_100`
--

DROP TABLE IF EXISTS `CH_170_100`;
CREATE TABLE `CH_170_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=700968 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>20)';

--
-- Table structure for table `CH_170_110`
--

DROP TABLE IF EXISTS `CH_170_110`;
CREATE TABLE `CH_170_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10318881 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>30)';

--
-- Table structure for table `CH_170_120`
--

DROP TABLE IF EXISTS `CH_170_120`;
CREATE TABLE `CH_170_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334729 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>40)';

--
-- Table structure for table `CH_170_130`
--

DROP TABLE IF EXISTS `CH_170_130`;
CREATE TABLE `CH_170_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338004 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>50)';

--
-- Table structure for table `CH_170_140`
--

DROP TABLE IF EXISTS `CH_170_140`;
CREATE TABLE `CH_170_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330369 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>60)';

--
-- Table structure for table `CH_170_150`
--

DROP TABLE IF EXISTS `CH_170_150`;
CREATE TABLE `CH_170_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=5905094 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>70)';

--
-- Table structure for table `CH_170_160`
--

DROP TABLE IF EXISTS `CH_170_160`;
CREATE TABLE `CH_170_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_170_170`
--

DROP TABLE IF EXISTS `CH_170_170`;
CREATE TABLE `CH_170_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_170_180`
--

DROP TABLE IF EXISTS `CH_170_180`;
CREATE TABLE `CH_170_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_170_20`
--

DROP TABLE IF EXISTS `CH_170_20`;
CREATE TABLE `CH_170_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-60)';

--
-- Table structure for table `CH_170_30`
--

DROP TABLE IF EXISTS `CH_170_30`;
CREATE TABLE `CH_170_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10235885 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-50)';

--
-- Table structure for table `CH_170_40`
--

DROP TABLE IF EXISTS `CH_170_40`;
CREATE TABLE `CH_170_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-40)';

--
-- Table structure for table `CH_170_50`
--

DROP TABLE IF EXISTS `CH_170_50`;
CREATE TABLE `CH_170_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-30)';

--
-- Table structure for table `CH_170_60`
--

DROP TABLE IF EXISTS `CH_170_60`;
CREATE TABLE `CH_170_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10129232 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-20)';

--
-- Table structure for table `CH_170_70`
--

DROP TABLE IF EXISTS `CH_170_70`;
CREATE TABLE `CH_170_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>-10)';

--
-- Table structure for table `CH_170_80`
--

DROP TABLE IF EXISTS `CH_170_80`;
CREATE TABLE `CH_170_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10313391 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>0)';

--
-- Table structure for table `CH_170_90`
--

DROP TABLE IF EXISTS `CH_170_90`;
CREATE TABLE `CH_170_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9255906 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-10,lat>10)';

--
-- Table structure for table `CH_180_0`
--

DROP TABLE IF EXISTS `CH_180_0`;
CREATE TABLE `CH_180_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4207406 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-80)';

--
-- Table structure for table `CH_180_10`
--

DROP TABLE IF EXISTS `CH_180_10`;
CREATE TABLE `CH_180_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4207444 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-70)';

--
-- Table structure for table `CH_180_100`
--

DROP TABLE IF EXISTS `CH_180_100`;
CREATE TABLE `CH_180_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>20)';

--
-- Table structure for table `CH_180_110`
--

DROP TABLE IF EXISTS `CH_180_110`;
CREATE TABLE `CH_180_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340275 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>30)';

--
-- Table structure for table `CH_180_120`
--

DROP TABLE IF EXISTS `CH_180_120`;
CREATE TABLE `CH_180_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339963 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>40)';

--
-- Table structure for table `CH_180_130`
--

DROP TABLE IF EXISTS `CH_180_130`;
CREATE TABLE `CH_180_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340563 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>50)';

--
-- Table structure for table `CH_180_140`
--

DROP TABLE IF EXISTS `CH_180_140`;
CREATE TABLE `CH_180_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340627 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>60)';

--
-- Table structure for table `CH_180_150`
--

DROP TABLE IF EXISTS `CH_180_150`;
CREATE TABLE `CH_180_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>70)';

--
-- Table structure for table `CH_180_160`
--

DROP TABLE IF EXISTS `CH_180_160`;
CREATE TABLE `CH_180_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_180_170`
--

DROP TABLE IF EXISTS `CH_180_170`;
CREATE TABLE `CH_180_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_180_180`
--

DROP TABLE IF EXISTS `CH_180_180`;
CREATE TABLE `CH_180_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_180_20`
--

DROP TABLE IF EXISTS `CH_180_20`;
CREATE TABLE `CH_180_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9396285 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-60)';

--
-- Table structure for table `CH_180_30`
--

DROP TABLE IF EXISTS `CH_180_30`;
CREATE TABLE `CH_180_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-50)';

--
-- Table structure for table `CH_180_40`
--

DROP TABLE IF EXISTS `CH_180_40`;
CREATE TABLE `CH_180_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-40)';

--
-- Table structure for table `CH_180_50`
--

DROP TABLE IF EXISTS `CH_180_50`;
CREATE TABLE `CH_180_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-30)';

--
-- Table structure for table `CH_180_60`
--

DROP TABLE IF EXISTS `CH_180_60`;
CREATE TABLE `CH_180_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-20)';

--
-- Table structure for table `CH_180_70`
--

DROP TABLE IF EXISTS `CH_180_70`;
CREATE TABLE `CH_180_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10219379 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>-10)';

--
-- Table structure for table `CH_180_80`
--

DROP TABLE IF EXISTS `CH_180_80`;
CREATE TABLE `CH_180_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10234079 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>0)';

--
-- Table structure for table `CH_180_90`
--

DROP TABLE IF EXISTS `CH_180_90`;
CREATE TABLE `CH_180_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=7309912 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>0,lat>10)';

--
-- Table structure for table `CH_190_0`
--

DROP TABLE IF EXISTS `CH_190_0`;
CREATE TABLE `CH_190_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4207111 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-80)';

--
-- Table structure for table `CH_190_10`
--

DROP TABLE IF EXISTS `CH_190_10`;
CREATE TABLE `CH_190_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4207111 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-70)';

--
-- Table structure for table `CH_190_100`
--

DROP TABLE IF EXISTS `CH_190_100`;
CREATE TABLE `CH_190_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>20)';

--
-- Table structure for table `CH_190_110`
--

DROP TABLE IF EXISTS `CH_190_110`;
CREATE TABLE `CH_190_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340339 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>30)';

--
-- Table structure for table `CH_190_120`
--

DROP TABLE IF EXISTS `CH_190_120`;
CREATE TABLE `CH_190_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340847 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>40)';

--
-- Table structure for table `CH_190_130`
--

DROP TABLE IF EXISTS `CH_190_130`;
CREATE TABLE `CH_190_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340723 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>50)';

--
-- Table structure for table `CH_190_140`
--

DROP TABLE IF EXISTS `CH_190_140`;
CREATE TABLE `CH_190_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340919 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>60)';

--
-- Table structure for table `CH_190_150`
--

DROP TABLE IF EXISTS `CH_190_150`;
CREATE TABLE `CH_190_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340887 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>70)';

--
-- Table structure for table `CH_190_160`
--

DROP TABLE IF EXISTS `CH_190_160`;
CREATE TABLE `CH_190_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_190_170`
--

DROP TABLE IF EXISTS `CH_190_170`;
CREATE TABLE `CH_190_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_190_180`
--

DROP TABLE IF EXISTS `CH_190_180`;
CREATE TABLE `CH_190_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_190_20`
--

DROP TABLE IF EXISTS `CH_190_20`;
CREATE TABLE `CH_190_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-60)';

--
-- Table structure for table `CH_190_30`
--

DROP TABLE IF EXISTS `CH_190_30`;
CREATE TABLE `CH_190_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-50)';

--
-- Table structure for table `CH_190_40`
--

DROP TABLE IF EXISTS `CH_190_40`;
CREATE TABLE `CH_190_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339127 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-40)';

--
-- Table structure for table `CH_190_50`
--

DROP TABLE IF EXISTS `CH_190_50`;
CREATE TABLE `CH_190_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339111 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-30)';

--
-- Table structure for table `CH_190_60`
--

DROP TABLE IF EXISTS `CH_190_60`;
CREATE TABLE `CH_190_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10250207 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-20)';

--
-- Table structure for table `CH_190_70`
--

DROP TABLE IF EXISTS `CH_190_70`;
CREATE TABLE `CH_190_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10313379 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>-10)';

--
-- Table structure for table `CH_190_80`
--

DROP TABLE IF EXISTS `CH_190_80`;
CREATE TABLE `CH_190_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10234079 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>0)';

--
-- Table structure for table `CH_190_90`
--

DROP TABLE IF EXISTS `CH_190_90`;
CREATE TABLE `CH_190_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=7872138 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>10,lat>10)';

--
-- Table structure for table `CH_200_0`
--

DROP TABLE IF EXISTS `CH_200_0`;
CREATE TABLE `CH_200_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4206850 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-80)';

--
-- Table structure for table `CH_200_10`
--

DROP TABLE IF EXISTS `CH_200_10`;
CREATE TABLE `CH_200_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4206852 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-70)';

--
-- Table structure for table `CH_200_100`
--

DROP TABLE IF EXISTS `CH_200_100`;
CREATE TABLE `CH_200_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8747007 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>20)';

--
-- Table structure for table `CH_200_110`
--

DROP TABLE IF EXISTS `CH_200_110`;
CREATE TABLE `CH_200_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340335 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>30)';

--
-- Table structure for table `CH_200_120`
--

DROP TABLE IF EXISTS `CH_200_120`;
CREATE TABLE `CH_200_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336214 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>40)';

--
-- Table structure for table `CH_200_130`
--

DROP TABLE IF EXISTS `CH_200_130`;
CREATE TABLE `CH_200_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340935 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>50)';

--
-- Table structure for table `CH_200_140`
--

DROP TABLE IF EXISTS `CH_200_140`;
CREATE TABLE `CH_200_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340935 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>60)';

--
-- Table structure for table `CH_200_150`
--

DROP TABLE IF EXISTS `CH_200_150`;
CREATE TABLE `CH_200_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340851 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>70)';

--
-- Table structure for table `CH_200_160`
--

DROP TABLE IF EXISTS `CH_200_160`;
CREATE TABLE `CH_200_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_200_170`
--

DROP TABLE IF EXISTS `CH_200_170`;
CREATE TABLE `CH_200_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_200_180`
--

DROP TABLE IF EXISTS `CH_200_180`;
CREATE TABLE `CH_200_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_200_20`
--

DROP TABLE IF EXISTS `CH_200_20`;
CREATE TABLE `CH_200_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-60)';

--
-- Table structure for table `CH_200_30`
--

DROP TABLE IF EXISTS `CH_200_30`;
CREATE TABLE `CH_200_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-50)';

--
-- Table structure for table `CH_200_40`
--

DROP TABLE IF EXISTS `CH_200_40`;
CREATE TABLE `CH_200_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10246399 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-40)';

--
-- Table structure for table `CH_200_50`
--

DROP TABLE IF EXISTS `CH_200_50`;
CREATE TABLE `CH_200_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8411050 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-30)';

--
-- Table structure for table `CH_200_60`
--

DROP TABLE IF EXISTS `CH_200_60`;
CREATE TABLE `CH_200_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9622196 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-20)';

--
-- Table structure for table `CH_200_70`
--

DROP TABLE IF EXISTS `CH_200_70`;
CREATE TABLE `CH_200_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9913453 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>-10)';

--
-- Table structure for table `CH_200_80`
--

DROP TABLE IF EXISTS `CH_200_80`;
CREATE TABLE `CH_200_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8588964 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>0)';

--
-- Table structure for table `CH_200_90`
--

DROP TABLE IF EXISTS `CH_200_90`;
CREATE TABLE `CH_200_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>20,lat>10)';

--
-- Table structure for table `CH_20_0`
--

DROP TABLE IF EXISTS `CH_20_0`;
CREATE TABLE `CH_20_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9905623 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-80)';

--
-- Table structure for table `CH_20_10`
--

DROP TABLE IF EXISTS `CH_20_10`;
CREATE TABLE `CH_20_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-70)';

--
-- Table structure for table `CH_20_100`
--

DROP TABLE IF EXISTS `CH_20_100`;
CREATE TABLE `CH_20_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10302273 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>20)';

--
-- Table structure for table `CH_20_110`
--

DROP TABLE IF EXISTS `CH_20_110`;
CREATE TABLE `CH_20_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>30)';

--
-- Table structure for table `CH_20_120`
--

DROP TABLE IF EXISTS `CH_20_120`;
CREATE TABLE `CH_20_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>40)';

--
-- Table structure for table `CH_20_130`
--

DROP TABLE IF EXISTS `CH_20_130`;
CREATE TABLE `CH_20_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10321815 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>50)';

--
-- Table structure for table `CH_20_140`
--

DROP TABLE IF EXISTS `CH_20_140`;
CREATE TABLE `CH_20_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10196475 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>60)';

--
-- Table structure for table `CH_20_150`
--

DROP TABLE IF EXISTS `CH_20_150`;
CREATE TABLE `CH_20_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336864 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>70)';

--
-- Table structure for table `CH_20_160`
--

DROP TABLE IF EXISTS `CH_20_160`;
CREATE TABLE `CH_20_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_20_170`
--

DROP TABLE IF EXISTS `CH_20_170`;
CREATE TABLE `CH_20_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_20_180`
--

DROP TABLE IF EXISTS `CH_20_180`;
CREATE TABLE `CH_20_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_20_20`
--

DROP TABLE IF EXISTS `CH_20_20`;
CREATE TABLE `CH_20_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-60)';

--
-- Table structure for table `CH_20_30`
--

DROP TABLE IF EXISTS `CH_20_30`;
CREATE TABLE `CH_20_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-50)';

--
-- Table structure for table `CH_20_40`
--

DROP TABLE IF EXISTS `CH_20_40`;
CREATE TABLE `CH_20_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-40)';

--
-- Table structure for table `CH_20_50`
--

DROP TABLE IF EXISTS `CH_20_50`;
CREATE TABLE `CH_20_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10240330 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-30)';

--
-- Table structure for table `CH_20_60`
--

DROP TABLE IF EXISTS `CH_20_60`;
CREATE TABLE `CH_20_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10248975 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-20)';

--
-- Table structure for table `CH_20_70`
--

DROP TABLE IF EXISTS `CH_20_70`;
CREATE TABLE `CH_20_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331419 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>-10)';

--
-- Table structure for table `CH_20_80`
--

DROP TABLE IF EXISTS `CH_20_80`;
CREATE TABLE `CH_20_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10116416 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>0)';

--
-- Table structure for table `CH_20_90`
--

DROP TABLE IF EXISTS `CH_20_90`;
CREATE TABLE `CH_20_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9365940 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-160,lat>10)';

--
-- Table structure for table `CH_210_0`
--

DROP TABLE IF EXISTS `CH_210_0`;
CREATE TABLE `CH_210_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4206325 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-80)';

--
-- Table structure for table `CH_210_10`
--

DROP TABLE IF EXISTS `CH_210_10`;
CREATE TABLE `CH_210_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10028032 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-70)';

--
-- Table structure for table `CH_210_100`
--

DROP TABLE IF EXISTS `CH_210_100`;
CREATE TABLE `CH_210_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10283870 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>20)';

--
-- Table structure for table `CH_210_110`
--

DROP TABLE IF EXISTS `CH_210_110`;
CREATE TABLE `CH_210_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335904 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>30)';

--
-- Table structure for table `CH_210_120`
--

DROP TABLE IF EXISTS `CH_210_120`;
CREATE TABLE `CH_210_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331634 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>40)';

--
-- Table structure for table `CH_210_130`
--

DROP TABLE IF EXISTS `CH_210_130`;
CREATE TABLE `CH_210_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10327184 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>50)';

--
-- Table structure for table `CH_210_140`
--

DROP TABLE IF EXISTS `CH_210_140`;
CREATE TABLE `CH_210_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340179 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>60)';

--
-- Table structure for table `CH_210_150`
--

DROP TABLE IF EXISTS `CH_210_150`;
CREATE TABLE `CH_210_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339135 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>70)';

--
-- Table structure for table `CH_210_160`
--

DROP TABLE IF EXISTS `CH_210_160`;
CREATE TABLE `CH_210_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_210_170`
--

DROP TABLE IF EXISTS `CH_210_170`;
CREATE TABLE `CH_210_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_210_180`
--

DROP TABLE IF EXISTS `CH_210_180`;
CREATE TABLE `CH_210_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_210_20`
--

DROP TABLE IF EXISTS `CH_210_20`;
CREATE TABLE `CH_210_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-60)';

--
-- Table structure for table `CH_210_30`
--

DROP TABLE IF EXISTS `CH_210_30`;
CREATE TABLE `CH_210_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10073936 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-50)';

--
-- Table structure for table `CH_210_40`
--

DROP TABLE IF EXISTS `CH_210_40`;
CREATE TABLE `CH_210_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=821471 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-40)';

--
-- Table structure for table `CH_210_50`
--

DROP TABLE IF EXISTS `CH_210_50`;
CREATE TABLE `CH_210_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322385 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-30)';

--
-- Table structure for table `CH_210_60`
--

DROP TABLE IF EXISTS `CH_210_60`;
CREATE TABLE `CH_210_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10243844 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-20)';

--
-- Table structure for table `CH_210_70`
--

DROP TABLE IF EXISTS `CH_210_70`;
CREATE TABLE `CH_210_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10246448 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>-10)';

--
-- Table structure for table `CH_210_80`
--

DROP TABLE IF EXISTS `CH_210_80`;
CREATE TABLE `CH_210_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10319439 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>0)';

--
-- Table structure for table `CH_210_90`
--

DROP TABLE IF EXISTS `CH_210_90`;
CREATE TABLE `CH_210_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330734 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>30,lat>10)';

--
-- Table structure for table `CH_220_0`
--

DROP TABLE IF EXISTS `CH_220_0`;
CREATE TABLE `CH_220_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-80)';

--
-- Table structure for table `CH_220_10`
--

DROP TABLE IF EXISTS `CH_220_10`;
CREATE TABLE `CH_220_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338343 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-70)';

--
-- Table structure for table `CH_220_100`
--

DROP TABLE IF EXISTS `CH_220_100`;
CREATE TABLE `CH_220_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10131608 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>20)';

--
-- Table structure for table `CH_220_110`
--

DROP TABLE IF EXISTS `CH_220_110`;
CREATE TABLE `CH_220_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9912607 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>30)';

--
-- Table structure for table `CH_220_120`
--

DROP TABLE IF EXISTS `CH_220_120`;
CREATE TABLE `CH_220_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10257186 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>40)';

--
-- Table structure for table `CH_220_130`
--

DROP TABLE IF EXISTS `CH_220_130`;
CREATE TABLE `CH_220_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9474363 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>50)';

--
-- Table structure for table `CH_220_140`
--

DROP TABLE IF EXISTS `CH_220_140`;
CREATE TABLE `CH_220_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10140936 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>60)';

--
-- Table structure for table `CH_220_150`
--

DROP TABLE IF EXISTS `CH_220_150`;
CREATE TABLE `CH_220_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8421232 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>70)';

--
-- Table structure for table `CH_220_160`
--

DROP TABLE IF EXISTS `CH_220_160`;
CREATE TABLE `CH_220_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_220_170`
--

DROP TABLE IF EXISTS `CH_220_170`;
CREATE TABLE `CH_220_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_220_180`
--

DROP TABLE IF EXISTS `CH_220_180`;
CREATE TABLE `CH_220_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_220_20`
--

DROP TABLE IF EXISTS `CH_220_20`;
CREATE TABLE `CH_220_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-60)';

--
-- Table structure for table `CH_220_30`
--

DROP TABLE IF EXISTS `CH_220_30`;
CREATE TABLE `CH_220_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-50)';

--
-- Table structure for table `CH_220_40`
--

DROP TABLE IF EXISTS `CH_220_40`;
CREATE TABLE `CH_220_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-40)';

--
-- Table structure for table `CH_220_50`
--

DROP TABLE IF EXISTS `CH_220_50`;
CREATE TABLE `CH_220_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10242675 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-30)';

--
-- Table structure for table `CH_220_60`
--

DROP TABLE IF EXISTS `CH_220_60`;
CREATE TABLE `CH_220_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10312587 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-20)';

--
-- Table structure for table `CH_220_70`
--

DROP TABLE IF EXISTS `CH_220_70`;
CREATE TABLE `CH_220_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322271 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>-10)';

--
-- Table structure for table `CH_220_80`
--

DROP TABLE IF EXISTS `CH_220_80`;
CREATE TABLE `CH_220_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10321479 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>0)';

--
-- Table structure for table `CH_220_90`
--

DROP TABLE IF EXISTS `CH_220_90`;
CREATE TABLE `CH_220_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330684 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>40,lat>10)';

--
-- Table structure for table `CH_230_0`
--

DROP TABLE IF EXISTS `CH_230_0`;
CREATE TABLE `CH_230_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-80)';

--
-- Table structure for table `CH_230_10`
--

DROP TABLE IF EXISTS `CH_230_10`;
CREATE TABLE `CH_230_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338347 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-70)';

--
-- Table structure for table `CH_230_100`
--

DROP TABLE IF EXISTS `CH_230_100`;
CREATE TABLE `CH_230_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10329774 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>20)';

--
-- Table structure for table `CH_230_110`
--

DROP TABLE IF EXISTS `CH_230_110`;
CREATE TABLE `CH_230_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8983384 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>30)';

--
-- Table structure for table `CH_230_120`
--

DROP TABLE IF EXISTS `CH_230_120`;
CREATE TABLE `CH_230_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9212668 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>40)';

--
-- Table structure for table `CH_230_130`
--

DROP TABLE IF EXISTS `CH_230_130`;
CREATE TABLE `CH_230_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8354935 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>50)';

--
-- Table structure for table `CH_230_140`
--

DROP TABLE IF EXISTS `CH_230_140`;
CREATE TABLE `CH_230_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10192107 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>60)';

--
-- Table structure for table `CH_230_150`
--

DROP TABLE IF EXISTS `CH_230_150`;
CREATE TABLE `CH_230_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10315941 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>70)';

--
-- Table structure for table `CH_230_160`
--

DROP TABLE IF EXISTS `CH_230_160`;
CREATE TABLE `CH_230_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_230_170`
--

DROP TABLE IF EXISTS `CH_230_170`;
CREATE TABLE `CH_230_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_230_180`
--

DROP TABLE IF EXISTS `CH_230_180`;
CREATE TABLE `CH_230_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_230_20`
--

DROP TABLE IF EXISTS `CH_230_20`;
CREATE TABLE `CH_230_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-60)';

--
-- Table structure for table `CH_230_30`
--

DROP TABLE IF EXISTS `CH_230_30`;
CREATE TABLE `CH_230_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10232049 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-50)';

--
-- Table structure for table `CH_230_40`
--

DROP TABLE IF EXISTS `CH_230_40`;
CREATE TABLE `CH_230_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-40)';

--
-- Table structure for table `CH_230_50`
--

DROP TABLE IF EXISTS `CH_230_50`;
CREATE TABLE `CH_230_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10219659 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-30)';

--
-- Table structure for table `CH_230_60`
--

DROP TABLE IF EXISTS `CH_230_60`;
CREATE TABLE `CH_230_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10243872 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-20)';

--
-- Table structure for table `CH_230_70`
--

DROP TABLE IF EXISTS `CH_230_70`;
CREATE TABLE `CH_230_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10244495 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>-10)';

--
-- Table structure for table `CH_230_80`
--

DROP TABLE IF EXISTS `CH_230_80`;
CREATE TABLE `CH_230_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10313139 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>0)';

--
-- Table structure for table `CH_230_90`
--

DROP TABLE IF EXISTS `CH_230_90`;
CREATE TABLE `CH_230_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10085048 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>50,lat>10)';

--
-- Table structure for table `CH_240_0`
--

DROP TABLE IF EXISTS `CH_240_0`;
CREATE TABLE `CH_240_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-80)';

--
-- Table structure for table `CH_240_10`
--

DROP TABLE IF EXISTS `CH_240_10`;
CREATE TABLE `CH_240_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10214773 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-70)';

--
-- Table structure for table `CH_240_100`
--

DROP TABLE IF EXISTS `CH_240_100`;
CREATE TABLE `CH_240_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10320759 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>20)';

--
-- Table structure for table `CH_240_110`
--

DROP TABLE IF EXISTS `CH_240_110`;
CREATE TABLE `CH_240_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8351047 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>30)';

--
-- Table structure for table `CH_240_120`
--

DROP TABLE IF EXISTS `CH_240_120`;
CREATE TABLE `CH_240_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9468203 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>40)';

--
-- Table structure for table `CH_240_130`
--

DROP TABLE IF EXISTS `CH_240_130`;
CREATE TABLE `CH_240_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8632289 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>50)';

--
-- Table structure for table `CH_240_140`
--

DROP TABLE IF EXISTS `CH_240_140`;
CREATE TABLE `CH_240_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10087640 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>60)';

--
-- Table structure for table `CH_240_150`
--

DROP TABLE IF EXISTS `CH_240_150`;
CREATE TABLE `CH_240_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10316055 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>70)';

--
-- Table structure for table `CH_240_160`
--

DROP TABLE IF EXISTS `CH_240_160`;
CREATE TABLE `CH_240_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_240_170`
--

DROP TABLE IF EXISTS `CH_240_170`;
CREATE TABLE `CH_240_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_240_180`
--

DROP TABLE IF EXISTS `CH_240_180`;
CREATE TABLE `CH_240_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_240_20`
--

DROP TABLE IF EXISTS `CH_240_20`;
CREATE TABLE `CH_240_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10227289 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-60)';

--
-- Table structure for table `CH_240_30`
--

DROP TABLE IF EXISTS `CH_240_30`;
CREATE TABLE `CH_240_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10333009 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-50)';

--
-- Table structure for table `CH_240_40`
--

DROP TABLE IF EXISTS `CH_240_40`;
CREATE TABLE `CH_240_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-40)';

--
-- Table structure for table `CH_240_50`
--

DROP TABLE IF EXISTS `CH_240_50`;
CREATE TABLE `CH_240_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-30)';

--
-- Table structure for table `CH_240_60`
--

DROP TABLE IF EXISTS `CH_240_60`;
CREATE TABLE `CH_240_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10240680 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-20)';

--
-- Table structure for table `CH_240_70`
--

DROP TABLE IF EXISTS `CH_240_70`;
CREATE TABLE `CH_240_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>-10)';

--
-- Table structure for table `CH_240_80`
--

DROP TABLE IF EXISTS `CH_240_80`;
CREATE TABLE `CH_240_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>0)';

--
-- Table structure for table `CH_240_90`
--

DROP TABLE IF EXISTS `CH_240_90`;
CREATE TABLE `CH_240_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>60,lat>10)';

--
-- Table structure for table `CH_250_0`
--

DROP TABLE IF EXISTS `CH_250_0`;
CREATE TABLE `CH_250_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-80)';

--
-- Table structure for table `CH_250_10`
--

DROP TABLE IF EXISTS `CH_250_10`;
CREATE TABLE `CH_250_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339047 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-70)';

--
-- Table structure for table `CH_250_100`
--

DROP TABLE IF EXISTS `CH_250_100`;
CREATE TABLE `CH_250_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330714 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>20)';

--
-- Table structure for table `CH_250_110`
--

DROP TABLE IF EXISTS `CH_250_110`;
CREATE TABLE `CH_250_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9358140 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>30)';

--
-- Table structure for table `CH_250_120`
--

DROP TABLE IF EXISTS `CH_250_120`;
CREATE TABLE `CH_250_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8598549 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>40)';

--
-- Table structure for table `CH_250_130`
--

DROP TABLE IF EXISTS `CH_250_130`;
CREATE TABLE `CH_250_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8966928 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>50)';

--
-- Table structure for table `CH_250_140`
--

DROP TABLE IF EXISTS `CH_250_140`;
CREATE TABLE `CH_250_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10087648 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>60)';

--
-- Table structure for table `CH_250_150`
--

DROP TABLE IF EXISTS `CH_250_150`;
CREATE TABLE `CH_250_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10281644 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>70)';

--
-- Table structure for table `CH_250_160`
--

DROP TABLE IF EXISTS `CH_250_160`;
CREATE TABLE `CH_250_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_250_170`
--

DROP TABLE IF EXISTS `CH_250_170`;
CREATE TABLE `CH_250_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_250_180`
--

DROP TABLE IF EXISTS `CH_250_180`;
CREATE TABLE `CH_250_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_250_20`
--

DROP TABLE IF EXISTS `CH_250_20`;
CREATE TABLE `CH_250_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10220611 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-60)';

--
-- Table structure for table `CH_250_30`
--

DROP TABLE IF EXISTS `CH_250_30`;
CREATE TABLE `CH_250_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10230383 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-50)';

--
-- Table structure for table `CH_250_40`
--

DROP TABLE IF EXISTS `CH_250_40`;
CREATE TABLE `CH_250_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10217349 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-40)';

--
-- Table structure for table `CH_250_50`
--

DROP TABLE IF EXISTS `CH_250_50`;
CREATE TABLE `CH_250_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-30)';

--
-- Table structure for table `CH_250_60`
--

DROP TABLE IF EXISTS `CH_250_60`;
CREATE TABLE `CH_250_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-20)';

--
-- Table structure for table `CH_250_70`
--

DROP TABLE IF EXISTS `CH_250_70`;
CREATE TABLE `CH_250_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10144496 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>-10)';

--
-- Table structure for table `CH_250_80`
--

DROP TABLE IF EXISTS `CH_250_80`;
CREATE TABLE `CH_250_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334784 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>0)';

--
-- Table structure for table `CH_250_90`
--

DROP TABLE IF EXISTS `CH_250_90`;
CREATE TABLE `CH_250_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10307243 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>70,lat>10)';

--
-- Table structure for table `CH_260_0`
--

DROP TABLE IF EXISTS `CH_260_0`;
CREATE TABLE `CH_260_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-80)';

--
-- Table structure for table `CH_260_10`
--

DROP TABLE IF EXISTS `CH_260_10`;
CREATE TABLE `CH_260_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4204648 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-70)';

--
-- Table structure for table `CH_260_100`
--

DROP TABLE IF EXISTS `CH_260_100`;
CREATE TABLE `CH_260_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10303715 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>20)';

--
-- Table structure for table `CH_260_110`
--

DROP TABLE IF EXISTS `CH_260_110`;
CREATE TABLE `CH_260_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8567704 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>30)';

--
-- Table structure for table `CH_260_120`
--

DROP TABLE IF EXISTS `CH_260_120`;
CREATE TABLE `CH_260_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8425060 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>40)';

--
-- Table structure for table `CH_260_130`
--

DROP TABLE IF EXISTS `CH_260_130`;
CREATE TABLE `CH_260_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8446745 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>50)';

--
-- Table structure for table `CH_260_140`
--

DROP TABLE IF EXISTS `CH_260_140`;
CREATE TABLE `CH_260_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9913003 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>60)';

--
-- Table structure for table `CH_260_150`
--

DROP TABLE IF EXISTS `CH_260_150`;
CREATE TABLE `CH_260_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338595 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>70)';

--
-- Table structure for table `CH_260_160`
--

DROP TABLE IF EXISTS `CH_260_160`;
CREATE TABLE `CH_260_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_260_170`
--

DROP TABLE IF EXISTS `CH_260_170`;
CREATE TABLE `CH_260_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_260_180`
--

DROP TABLE IF EXISTS `CH_260_180`;
CREATE TABLE `CH_260_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_260_20`
--

DROP TABLE IF EXISTS `CH_260_20`;
CREATE TABLE `CH_260_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-60)';

--
-- Table structure for table `CH_260_30`
--

DROP TABLE IF EXISTS `CH_260_30`;
CREATE TABLE `CH_260_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-50)';

--
-- Table structure for table `CH_260_40`
--

DROP TABLE IF EXISTS `CH_260_40`;
CREATE TABLE `CH_260_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-40)';

--
-- Table structure for table `CH_260_50`
--

DROP TABLE IF EXISTS `CH_260_50`;
CREATE TABLE `CH_260_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-30)';

--
-- Table structure for table `CH_260_60`
--

DROP TABLE IF EXISTS `CH_260_60`;
CREATE TABLE `CH_260_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-20)';

--
-- Table structure for table `CH_260_70`
--

DROP TABLE IF EXISTS `CH_260_70`;
CREATE TABLE `CH_260_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>-10)';

--
-- Table structure for table `CH_260_80`
--

DROP TABLE IF EXISTS `CH_260_80`;
CREATE TABLE `CH_260_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10231601 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>0)';

--
-- Table structure for table `CH_260_90`
--

DROP TABLE IF EXISTS `CH_260_90`;
CREATE TABLE `CH_260_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10307936 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>80,lat>10)';

--
-- Table structure for table `CH_270_0`
--

DROP TABLE IF EXISTS `CH_270_0`;
CREATE TABLE `CH_270_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-80)';

--
-- Table structure for table `CH_270_10`
--

DROP TABLE IF EXISTS `CH_270_10`;
CREATE TABLE `CH_270_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338715 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-70)';

--
-- Table structure for table `CH_270_100`
--

DROP TABLE IF EXISTS `CH_270_100`;
CREATE TABLE `CH_270_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10303449 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>20)';

--
-- Table structure for table `CH_270_110`
--

DROP TABLE IF EXISTS `CH_270_110`;
CREATE TABLE `CH_270_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8852264 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>30)';

--
-- Table structure for table `CH_270_120`
--

DROP TABLE IF EXISTS `CH_270_120`;
CREATE TABLE `CH_270_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=7855587 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>40)';

--
-- Table structure for table `CH_270_130`
--

DROP TABLE IF EXISTS `CH_270_130`;
CREATE TABLE `CH_270_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8446745 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>50)';

--
-- Table structure for table `CH_270_140`
--

DROP TABLE IF EXISTS `CH_270_140`;
CREATE TABLE `CH_270_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8253890 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>60)';

--
-- Table structure for table `CH_270_150`
--

DROP TABLE IF EXISTS `CH_270_150`;
CREATE TABLE `CH_270_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10316061 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>70)';

--
-- Table structure for table `CH_270_160`
--

DROP TABLE IF EXISTS `CH_270_160`;
CREATE TABLE `CH_270_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_270_170`
--

DROP TABLE IF EXISTS `CH_270_170`;
CREATE TABLE `CH_270_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_270_180`
--

DROP TABLE IF EXISTS `CH_270_180`;
CREATE TABLE `CH_270_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_270_20`
--

DROP TABLE IF EXISTS `CH_270_20`;
CREATE TABLE `CH_270_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-60)';

--
-- Table structure for table `CH_270_30`
--

DROP TABLE IF EXISTS `CH_270_30`;
CREATE TABLE `CH_270_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-50)';

--
-- Table structure for table `CH_270_40`
--

DROP TABLE IF EXISTS `CH_270_40`;
CREATE TABLE `CH_270_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-40)';

--
-- Table structure for table `CH_270_50`
--

DROP TABLE IF EXISTS `CH_270_50`;
CREATE TABLE `CH_270_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-30)';

--
-- Table structure for table `CH_270_60`
--

DROP TABLE IF EXISTS `CH_270_60`;
CREATE TABLE `CH_270_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10249521 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-20)';

--
-- Table structure for table `CH_270_70`
--

DROP TABLE IF EXISTS `CH_270_70`;
CREATE TABLE `CH_270_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10240764 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>-10)';

--
-- Table structure for table `CH_270_80`
--

DROP TABLE IF EXISTS `CH_270_80`;
CREATE TABLE `CH_270_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10326299 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>0)';

--
-- Table structure for table `CH_270_90`
--

DROP TABLE IF EXISTS `CH_270_90`;
CREATE TABLE `CH_270_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330639 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>90,lat>10)';

--
-- Table structure for table `CH_280_0`
--

DROP TABLE IF EXISTS `CH_280_0`;
CREATE TABLE `CH_280_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-80)';

--
-- Table structure for table `CH_280_10`
--

DROP TABLE IF EXISTS `CH_280_10`;
CREATE TABLE `CH_280_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338739 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-70)';

--
-- Table structure for table `CH_280_100`
--

DROP TABLE IF EXISTS `CH_280_100`;
CREATE TABLE `CH_280_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330569 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>20)';

--
-- Table structure for table `CH_280_110`
--

DROP TABLE IF EXISTS `CH_280_110`;
CREATE TABLE `CH_280_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8488091 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>30)';

--
-- Table structure for table `CH_280_120`
--

DROP TABLE IF EXISTS `CH_280_120`;
CREATE TABLE `CH_280_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8923783 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>40)';

--
-- Table structure for table `CH_280_130`
--

DROP TABLE IF EXISTS `CH_280_130`;
CREATE TABLE `CH_280_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8825724 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>50)';

--
-- Table structure for table `CH_280_140`
--

DROP TABLE IF EXISTS `CH_280_140`;
CREATE TABLE `CH_280_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8498734 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>60)';

--
-- Table structure for table `CH_280_150`
--

DROP TABLE IF EXISTS `CH_280_150`;
CREATE TABLE `CH_280_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339179 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>70)';

--
-- Table structure for table `CH_280_160`
--

DROP TABLE IF EXISTS `CH_280_160`;
CREATE TABLE `CH_280_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_280_170`
--

DROP TABLE IF EXISTS `CH_280_170`;
CREATE TABLE `CH_280_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_280_180`
--

DROP TABLE IF EXISTS `CH_280_180`;
CREATE TABLE `CH_280_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_280_20`
--

DROP TABLE IF EXISTS `CH_280_20`;
CREATE TABLE `CH_280_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-60)';

--
-- Table structure for table `CH_280_30`
--

DROP TABLE IF EXISTS `CH_280_30`;
CREATE TABLE `CH_280_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-50)';

--
-- Table structure for table `CH_280_40`
--

DROP TABLE IF EXISTS `CH_280_40`;
CREATE TABLE `CH_280_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-40)';

--
-- Table structure for table `CH_280_50`
--

DROP TABLE IF EXISTS `CH_280_50`;
CREATE TABLE `CH_280_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-30)';

--
-- Table structure for table `CH_280_60`
--

DROP TABLE IF EXISTS `CH_280_60`;
CREATE TABLE `CH_280_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=6464834 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-20)';

--
-- Table structure for table `CH_280_70`
--

DROP TABLE IF EXISTS `CH_280_70`;
CREATE TABLE `CH_280_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10325869 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>-10)';

--
-- Table structure for table `CH_280_80`
--

DROP TABLE IF EXISTS `CH_280_80`;
CREATE TABLE `CH_280_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336354 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>0)';

--
-- Table structure for table `CH_280_90`
--

DROP TABLE IF EXISTS `CH_280_90`;
CREATE TABLE `CH_280_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322865 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>100,lat>10)';

--
-- Table structure for table `CH_290_0`
--

DROP TABLE IF EXISTS `CH_290_0`;
CREATE TABLE `CH_290_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-80)';

--
-- Table structure for table `CH_290_10`
--

DROP TABLE IF EXISTS `CH_290_10`;
CREATE TABLE `CH_290_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338903 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-70)';

--
-- Table structure for table `CH_290_100`
--

DROP TABLE IF EXISTS `CH_290_100`;
CREATE TABLE `CH_290_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337499 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>20)';

--
-- Table structure for table `CH_290_110`
--

DROP TABLE IF EXISTS `CH_290_110`;
CREATE TABLE `CH_290_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10079272 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>30)';

--
-- Table structure for table `CH_290_120`
--

DROP TABLE IF EXISTS `CH_290_120`;
CREATE TABLE `CH_290_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9498431 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>40)';

--
-- Table structure for table `CH_290_130`
--

DROP TABLE IF EXISTS `CH_290_130`;
CREATE TABLE `CH_290_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9075154 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>50)';

--
-- Table structure for table `CH_290_140`
--

DROP TABLE IF EXISTS `CH_290_140`;
CREATE TABLE `CH_290_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4426222 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>60)';

--
-- Table structure for table `CH_290_150`
--

DROP TABLE IF EXISTS `CH_290_150`;
CREATE TABLE `CH_290_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10213471 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>70)';

--
-- Table structure for table `CH_290_160`
--

DROP TABLE IF EXISTS `CH_290_160`;
CREATE TABLE `CH_290_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_290_170`
--

DROP TABLE IF EXISTS `CH_290_170`;
CREATE TABLE `CH_290_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_290_180`
--

DROP TABLE IF EXISTS `CH_290_180`;
CREATE TABLE `CH_290_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_290_20`
--

DROP TABLE IF EXISTS `CH_290_20`;
CREATE TABLE `CH_290_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-60)';

--
-- Table structure for table `CH_290_30`
--

DROP TABLE IF EXISTS `CH_290_30`;
CREATE TABLE `CH_290_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-50)';

--
-- Table structure for table `CH_290_40`
--

DROP TABLE IF EXISTS `CH_290_40`;
CREATE TABLE `CH_290_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332734 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-40)';

--
-- Table structure for table `CH_290_50`
--

DROP TABLE IF EXISTS `CH_290_50`;
CREATE TABLE `CH_290_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338244 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-30)';

--
-- Table structure for table `CH_290_60`
--

DROP TABLE IF EXISTS `CH_290_60`;
CREATE TABLE `CH_290_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10222851 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-20)';

--
-- Table structure for table `CH_290_70`
--

DROP TABLE IF EXISTS `CH_290_70`;
CREATE TABLE `CH_290_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10325189 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>-10)';

--
-- Table structure for table `CH_290_80`
--

DROP TABLE IF EXISTS `CH_290_80`;
CREATE TABLE `CH_290_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10333529 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>0)';

--
-- Table structure for table `CH_290_90`
--

DROP TABLE IF EXISTS `CH_290_90`;
CREATE TABLE `CH_290_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322661 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>110,lat>10)';

--
-- Table structure for table `CH_300_0`
--

DROP TABLE IF EXISTS `CH_300_0`;
CREATE TABLE `CH_300_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-80)';

--
-- Table structure for table `CH_300_10`
--

DROP TABLE IF EXISTS `CH_300_10`;
CREATE TABLE `CH_300_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338895 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-70)';

--
-- Table structure for table `CH_300_100`
--

DROP TABLE IF EXISTS `CH_300_100`;
CREATE TABLE `CH_300_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337409 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>20)';

--
-- Table structure for table `CH_300_110`
--

DROP TABLE IF EXISTS `CH_300_110`;
CREATE TABLE `CH_300_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335414 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>30)';

--
-- Table structure for table `CH_300_120`
--

DROP TABLE IF EXISTS `CH_300_120`;
CREATE TABLE `CH_300_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334409 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>40)';

--
-- Table structure for table `CH_300_130`
--

DROP TABLE IF EXISTS `CH_300_130`;
CREATE TABLE `CH_300_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4432544 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>50)';

--
-- Table structure for table `CH_300_140`
--

DROP TABLE IF EXISTS `CH_300_140`;
CREATE TABLE `CH_300_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9913084 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>60)';

--
-- Table structure for table `CH_300_150`
--

DROP TABLE IF EXISTS `CH_300_150`;
CREATE TABLE `CH_300_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340435 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>70)';

--
-- Table structure for table `CH_300_160`
--

DROP TABLE IF EXISTS `CH_300_160`;
CREATE TABLE `CH_300_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_300_170`
--

DROP TABLE IF EXISTS `CH_300_170`;
CREATE TABLE `CH_300_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_300_180`
--

DROP TABLE IF EXISTS `CH_300_180`;
CREATE TABLE `CH_300_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_300_20`
--

DROP TABLE IF EXISTS `CH_300_20`;
CREATE TABLE `CH_300_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-60)';

--
-- Table structure for table `CH_300_30`
--

DROP TABLE IF EXISTS `CH_300_30`;
CREATE TABLE `CH_300_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-50)';

--
-- Table structure for table `CH_300_40`
--

DROP TABLE IF EXISTS `CH_300_40`;
CREATE TABLE `CH_300_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10311711 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-40)';

--
-- Table structure for table `CH_300_50`
--

DROP TABLE IF EXISTS `CH_300_50`;
CREATE TABLE `CH_300_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9744388 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-30)';

--
-- Table structure for table `CH_300_60`
--

DROP TABLE IF EXISTS `CH_300_60`;
CREATE TABLE `CH_300_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337929 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-20)';

--
-- Table structure for table `CH_300_70`
--

DROP TABLE IF EXISTS `CH_300_70`;
CREATE TABLE `CH_300_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334224 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>-10)';

--
-- Table structure for table `CH_300_80`
--

DROP TABLE IF EXISTS `CH_300_80`;
CREATE TABLE `CH_300_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335484 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>0)';

--
-- Table structure for table `CH_300_90`
--

DROP TABLE IF EXISTS `CH_300_90`;
CREATE TABLE `CH_300_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337539 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>120,lat>10)';

--
-- Table structure for table `CH_30_0`
--

DROP TABLE IF EXISTS `CH_30_0`;
CREATE TABLE `CH_30_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4199254 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-80)';

--
-- Table structure for table `CH_30_10`
--

DROP TABLE IF EXISTS `CH_30_10`;
CREATE TABLE `CH_30_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-70)';

--
-- Table structure for table `CH_30_100`
--

DROP TABLE IF EXISTS `CH_30_100`;
CREATE TABLE `CH_30_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>20)';

--
-- Table structure for table `CH_30_110`
--

DROP TABLE IF EXISTS `CH_30_110`;
CREATE TABLE `CH_30_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>30)';

--
-- Table structure for table `CH_30_120`
--

DROP TABLE IF EXISTS `CH_30_120`;
CREATE TABLE `CH_30_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>40)';

--
-- Table structure for table `CH_30_130`
--

DROP TABLE IF EXISTS `CH_30_130`;
CREATE TABLE `CH_30_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337994 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>50)';

--
-- Table structure for table `CH_30_140`
--

DROP TABLE IF EXISTS `CH_30_140`;
CREATE TABLE `CH_30_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338164 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>60)';

--
-- Table structure for table `CH_30_150`
--

DROP TABLE IF EXISTS `CH_30_150`;
CREATE TABLE `CH_30_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10319133 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>70)';

--
-- Table structure for table `CH_30_160`
--

DROP TABLE IF EXISTS `CH_30_160`;
CREATE TABLE `CH_30_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_30_170`
--

DROP TABLE IF EXISTS `CH_30_170`;
CREATE TABLE `CH_30_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_30_180`
--

DROP TABLE IF EXISTS `CH_30_180`;
CREATE TABLE `CH_30_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_30_20`
--

DROP TABLE IF EXISTS `CH_30_20`;
CREATE TABLE `CH_30_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-60)';

--
-- Table structure for table `CH_30_30`
--

DROP TABLE IF EXISTS `CH_30_30`;
CREATE TABLE `CH_30_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-50)';

--
-- Table structure for table `CH_30_40`
--

DROP TABLE IF EXISTS `CH_30_40`;
CREATE TABLE `CH_30_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-40)';

--
-- Table structure for table `CH_30_50`
--

DROP TABLE IF EXISTS `CH_30_50`;
CREATE TABLE `CH_30_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10245839 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-30)';

--
-- Table structure for table `CH_30_60`
--

DROP TABLE IF EXISTS `CH_30_60`;
CREATE TABLE `CH_30_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10333579 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-20)';

--
-- Table structure for table `CH_30_70`
--

DROP TABLE IF EXISTS `CH_30_70`;
CREATE TABLE `CH_30_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10245258 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>-10)';

--
-- Table structure for table `CH_30_80`
--

DROP TABLE IF EXISTS `CH_30_80`;
CREATE TABLE `CH_30_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>0)';

--
-- Table structure for table `CH_30_90`
--

DROP TABLE IF EXISTS `CH_30_90`;
CREATE TABLE `CH_30_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-150,lat>10)';

--
-- Table structure for table `CH_310_0`
--

DROP TABLE IF EXISTS `CH_310_0`;
CREATE TABLE `CH_310_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-80)';

--
-- Table structure for table `CH_310_10`
--

DROP TABLE IF EXISTS `CH_310_10`;
CREATE TABLE `CH_310_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10310349 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-70)';

--
-- Table structure for table `CH_310_100`
--

DROP TABLE IF EXISTS `CH_310_100`;
CREATE TABLE `CH_310_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10136848 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>20)';

--
-- Table structure for table `CH_310_110`
--

DROP TABLE IF EXISTS `CH_310_110`;
CREATE TABLE `CH_310_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10335599 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>30)';

--
-- Table structure for table `CH_310_120`
--

DROP TABLE IF EXISTS `CH_310_120`;
CREATE TABLE `CH_310_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334264 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>40)';

--
-- Table structure for table `CH_310_130`
--

DROP TABLE IF EXISTS `CH_310_130`;
CREATE TABLE `CH_310_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10147448 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>50)';

--
-- Table structure for table `CH_310_140`
--

DROP TABLE IF EXISTS `CH_310_140`;
CREATE TABLE `CH_310_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4431721 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>60)';

--
-- Table structure for table `CH_310_150`
--

DROP TABLE IF EXISTS `CH_310_150`;
CREATE TABLE `CH_310_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10065064 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>70)';

--
-- Table structure for table `CH_310_160`
--

DROP TABLE IF EXISTS `CH_310_160`;
CREATE TABLE `CH_310_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_310_170`
--

DROP TABLE IF EXISTS `CH_310_170`;
CREATE TABLE `CH_310_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_310_180`
--

DROP TABLE IF EXISTS `CH_310_180`;
CREATE TABLE `CH_310_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_310_20`
--

DROP TABLE IF EXISTS `CH_310_20`;
CREATE TABLE `CH_310_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-60)';

--
-- Table structure for table `CH_310_30`
--

DROP TABLE IF EXISTS `CH_310_30`;
CREATE TABLE `CH_310_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-50)';

--
-- Table structure for table `CH_310_40`
--

DROP TABLE IF EXISTS `CH_310_40`;
CREATE TABLE `CH_310_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332444 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-40)';

--
-- Table structure for table `CH_310_50`
--

DROP TABLE IF EXISTS `CH_310_50`;
CREATE TABLE `CH_310_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-30)';

--
-- Table structure for table `CH_310_60`
--

DROP TABLE IF EXISTS `CH_310_60`;
CREATE TABLE `CH_310_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10326114 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-20)';

--
-- Table structure for table `CH_310_70`
--

DROP TABLE IF EXISTS `CH_310_70`;
CREATE TABLE `CH_310_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332629 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>-10)';

--
-- Table structure for table `CH_310_80`
--

DROP TABLE IF EXISTS `CH_310_80`;
CREATE TABLE `CH_310_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332214 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>0)';

--
-- Table structure for table `CH_310_90`
--

DROP TABLE IF EXISTS `CH_310_90`;
CREATE TABLE `CH_310_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10225042 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>130,lat>10)';

--
-- Table structure for table `CH_320_0`
--

DROP TABLE IF EXISTS `CH_320_0`;
CREATE TABLE `CH_320_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-80)';

--
-- Table structure for table `CH_320_10`
--

DROP TABLE IF EXISTS `CH_320_10`;
CREATE TABLE `CH_320_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330564 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-70)';

--
-- Table structure for table `CH_320_100`
--

DROP TABLE IF EXISTS `CH_320_100`;
CREATE TABLE `CH_320_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10282932 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>20)';

--
-- Table structure for table `CH_320_110`
--

DROP TABLE IF EXISTS `CH_320_110`;
CREATE TABLE `CH_320_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322109 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>30)';

--
-- Table structure for table `CH_320_120`
--

DROP TABLE IF EXISTS `CH_320_120`;
CREATE TABLE `CH_320_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334264 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>40)';

--
-- Table structure for table `CH_320_130`
--

DROP TABLE IF EXISTS `CH_320_130`;
CREATE TABLE `CH_320_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10181663 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>50)';

--
-- Table structure for table `CH_320_140`
--

DROP TABLE IF EXISTS `CH_320_140`;
CREATE TABLE `CH_320_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9912301 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>60)';

--
-- Table structure for table `CH_320_150`
--

DROP TABLE IF EXISTS `CH_320_150`;
CREATE TABLE `CH_320_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338591 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>70)';

--
-- Table structure for table `CH_320_160`
--

DROP TABLE IF EXISTS `CH_320_160`;
CREATE TABLE `CH_320_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_320_170`
--

DROP TABLE IF EXISTS `CH_320_170`;
CREATE TABLE `CH_320_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_320_180`
--

DROP TABLE IF EXISTS `CH_320_180`;
CREATE TABLE `CH_320_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_320_20`
--

DROP TABLE IF EXISTS `CH_320_20`;
CREATE TABLE `CH_320_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-60)';

--
-- Table structure for table `CH_320_30`
--

DROP TABLE IF EXISTS `CH_320_30`;
CREATE TABLE `CH_320_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338299 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-50)';

--
-- Table structure for table `CH_320_40`
--

DROP TABLE IF EXISTS `CH_320_40`;
CREATE TABLE `CH_320_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334814 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-40)';

--
-- Table structure for table `CH_320_50`
--

DROP TABLE IF EXISTS `CH_320_50`;
CREATE TABLE `CH_320_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10323141 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-30)';

--
-- Table structure for table `CH_320_60`
--

DROP TABLE IF EXISTS `CH_320_60`;
CREATE TABLE `CH_320_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10329414 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-20)';

--
-- Table structure for table `CH_320_70`
--

DROP TABLE IF EXISTS `CH_320_70`;
CREATE TABLE `CH_320_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331784 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>-10)';

--
-- Table structure for table `CH_320_80`
--

DROP TABLE IF EXISTS `CH_320_80`;
CREATE TABLE `CH_320_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10228248 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>0)';

--
-- Table structure for table `CH_320_90`
--

DROP TABLE IF EXISTS `CH_320_90`;
CREATE TABLE `CH_320_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10213303 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>140,lat>10)';

--
-- Table structure for table `CH_330_0`
--

DROP TABLE IF EXISTS `CH_330_0`;
CREATE TABLE `CH_330_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-80)';

--
-- Table structure for table `CH_330_10`
--

DROP TABLE IF EXISTS `CH_330_10`;
CREATE TABLE `CH_330_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10311147 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-70)';

--
-- Table structure for table `CH_330_100`
--

DROP TABLE IF EXISTS `CH_330_100`;
CREATE TABLE `CH_330_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8582646 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>20)';

--
-- Table structure for table `CH_330_110`
--

DROP TABLE IF EXISTS `CH_330_110`;
CREATE TABLE `CH_330_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>30)';

--
-- Table structure for table `CH_330_120`
--

DROP TABLE IF EXISTS `CH_330_120`;
CREATE TABLE `CH_330_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10313445 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>40)';

--
-- Table structure for table `CH_330_130`
--

DROP TABLE IF EXISTS `CH_330_130`;
CREATE TABLE `CH_330_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334339 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>50)';

--
-- Table structure for table `CH_330_140`
--

DROP TABLE IF EXISTS `CH_330_140`;
CREATE TABLE `CH_330_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10292165 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>60)';

--
-- Table structure for table `CH_330_150`
--

DROP TABLE IF EXISTS `CH_330_150`;
CREATE TABLE `CH_330_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338591 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>70)';

--
-- Table structure for table `CH_330_160`
--

DROP TABLE IF EXISTS `CH_330_160`;
CREATE TABLE `CH_330_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_330_170`
--

DROP TABLE IF EXISTS `CH_330_170`;
CREATE TABLE `CH_330_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_330_180`
--

DROP TABLE IF EXISTS `CH_330_180`;
CREATE TABLE `CH_330_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_330_20`
--

DROP TABLE IF EXISTS `CH_330_20`;
CREATE TABLE `CH_330_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10220520 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-60)';

--
-- Table structure for table `CH_330_30`
--

DROP TABLE IF EXISTS `CH_330_30`;
CREATE TABLE `CH_330_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-50)';

--
-- Table structure for table `CH_330_40`
--

DROP TABLE IF EXISTS `CH_330_40`;
CREATE TABLE `CH_330_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10249017 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-40)';

--
-- Table structure for table `CH_330_50`
--

DROP TABLE IF EXISTS `CH_330_50`;
CREATE TABLE `CH_330_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336564 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-30)';

--
-- Table structure for table `CH_330_60`
--

DROP TABLE IF EXISTS `CH_330_60`;
CREATE TABLE `CH_330_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10312563 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-20)';

--
-- Table structure for table `CH_330_70`
--

DROP TABLE IF EXISTS `CH_330_70`;
CREATE TABLE `CH_330_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332449 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>-10)';

--
-- Table structure for table `CH_330_80`
--

DROP TABLE IF EXISTS `CH_330_80`;
CREATE TABLE `CH_330_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10321365 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>0)';

--
-- Table structure for table `CH_330_90`
--

DROP TABLE IF EXISTS `CH_330_90`;
CREATE TABLE `CH_330_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>150,lat>10)';

--
-- Table structure for table `CH_340_0`
--

DROP TABLE IF EXISTS `CH_340_0`;
CREATE TABLE `CH_340_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10263472 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-80)';

--
-- Table structure for table `CH_340_10`
--

DROP TABLE IF EXISTS `CH_340_10`;
CREATE TABLE `CH_340_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10030024 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-70)';

--
-- Table structure for table `CH_340_100`
--

DROP TABLE IF EXISTS `CH_340_100`;
CREATE TABLE `CH_340_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>20)';

--
-- Table structure for table `CH_340_110`
--

DROP TABLE IF EXISTS `CH_340_110`;
CREATE TABLE `CH_340_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>30)';

--
-- Table structure for table `CH_340_120`
--

DROP TABLE IF EXISTS `CH_340_120`;
CREATE TABLE `CH_340_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>40)';

--
-- Table structure for table `CH_340_130`
--

DROP TABLE IF EXISTS `CH_340_130`;
CREATE TABLE `CH_340_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10148488 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>50)';

--
-- Table structure for table `CH_340_140`
--

DROP TABLE IF EXISTS `CH_340_140`;
CREATE TABLE `CH_340_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10252615 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>60)';

--
-- Table structure for table `CH_340_150`
--

DROP TABLE IF EXISTS `CH_340_150`;
CREATE TABLE `CH_340_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10089208 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>70)';

--
-- Table structure for table `CH_340_160`
--

DROP TABLE IF EXISTS `CH_340_160`;
CREATE TABLE `CH_340_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_340_170`
--

DROP TABLE IF EXISTS `CH_340_170`;
CREATE TABLE `CH_340_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_340_180`
--

DROP TABLE IF EXISTS `CH_340_180`;
CREATE TABLE `CH_340_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_340_20`
--

DROP TABLE IF EXISTS `CH_340_20`;
CREATE TABLE `CH_340_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10311555 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-60)';

--
-- Table structure for table `CH_340_30`
--

DROP TABLE IF EXISTS `CH_340_30`;
CREATE TABLE `CH_340_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10309131 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-50)';

--
-- Table structure for table `CH_340_40`
--

DROP TABLE IF EXISTS `CH_340_40`;
CREATE TABLE `CH_340_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-40)';

--
-- Table structure for table `CH_340_50`
--

DROP TABLE IF EXISTS `CH_340_50`;
CREATE TABLE `CH_340_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10323939 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-30)';

--
-- Table structure for table `CH_340_60`
--

DROP TABLE IF EXISTS `CH_340_60`;
CREATE TABLE `CH_340_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337229 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-20)';

--
-- Table structure for table `CH_340_70`
--

DROP TABLE IF EXISTS `CH_340_70`;
CREATE TABLE `CH_340_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10241765 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>-10)';

--
-- Table structure for table `CH_340_80`
--

DROP TABLE IF EXISTS `CH_340_80`;
CREATE TABLE `CH_340_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334874 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>0)';

--
-- Table structure for table `CH_340_90`
--

DROP TABLE IF EXISTS `CH_340_90`;
CREATE TABLE `CH_340_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334874 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>160,lat>10)';

--
-- Table structure for table `CH_350_0`
--

DROP TABLE IF EXISTS `CH_350_0`;
CREATE TABLE `CH_350_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10027928 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-80)';

--
-- Table structure for table `CH_350_10`
--

DROP TABLE IF EXISTS `CH_350_10`;
CREATE TABLE `CH_350_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-70)';

--
-- Table structure for table `CH_350_100`
--

DROP TABLE IF EXISTS `CH_350_100`;
CREATE TABLE `CH_350_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>20)';

--
-- Table structure for table `CH_350_110`
--

DROP TABLE IF EXISTS `CH_350_110`;
CREATE TABLE `CH_350_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>30)';

--
-- Table structure for table `CH_350_120`
--

DROP TABLE IF EXISTS `CH_350_120`;
CREATE TABLE `CH_350_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>40)';

--
-- Table structure for table `CH_350_130`
--

DROP TABLE IF EXISTS `CH_350_130`;
CREATE TABLE `CH_350_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10331369 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>50)';

--
-- Table structure for table `CH_350_140`
--

DROP TABLE IF EXISTS `CH_350_140`;
CREATE TABLE `CH_350_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10038880 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>60)';

--
-- Table structure for table `CH_350_150`
--

DROP TABLE IF EXISTS `CH_350_150`;
CREATE TABLE `CH_350_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10270087 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>70)';

--
-- Table structure for table `CH_350_160`
--

DROP TABLE IF EXISTS `CH_350_160`;
CREATE TABLE `CH_350_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_350_170`
--

DROP TABLE IF EXISTS `CH_350_170`;
CREATE TABLE `CH_350_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_350_180`
--

DROP TABLE IF EXISTS `CH_350_180`;
CREATE TABLE `CH_350_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_350_20`
--

DROP TABLE IF EXISTS `CH_350_20`;
CREATE TABLE `CH_350_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-60)';

--
-- Table structure for table `CH_350_30`
--

DROP TABLE IF EXISTS `CH_350_30`;
CREATE TABLE `CH_350_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10308369 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-50)';

--
-- Table structure for table `CH_350_40`
--

DROP TABLE IF EXISTS `CH_350_40`;
CREATE TABLE `CH_350_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10241828 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-40)';

--
-- Table structure for table `CH_350_50`
--

DROP TABLE IF EXISTS `CH_350_50`;
CREATE TABLE `CH_350_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8878898 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-30)';

--
-- Table structure for table `CH_350_60`
--

DROP TABLE IF EXISTS `CH_350_60`;
CREATE TABLE `CH_350_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10246763 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-20)';

--
-- Table structure for table `CH_350_70`
--

DROP TABLE IF EXISTS `CH_350_70`;
CREATE TABLE `CH_350_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332509 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>-10)';

--
-- Table structure for table `CH_350_80`
--

DROP TABLE IF EXISTS `CH_350_80`;
CREATE TABLE `CH_350_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10321989 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>0)';

--
-- Table structure for table `CH_350_90`
--

DROP TABLE IF EXISTS `CH_350_90`;
CREATE TABLE `CH_350_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10219358 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>170,lat>10)';

--
-- Table structure for table `CH_360_0`
--

DROP TABLE IF EXISTS `CH_360_0`;
CREATE TABLE `CH_360_0` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_10`
--

DROP TABLE IF EXISTS `CH_360_10`;
CREATE TABLE `CH_360_10` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_100`
--

DROP TABLE IF EXISTS `CH_360_100`;
CREATE TABLE `CH_360_100` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_110`
--

DROP TABLE IF EXISTS `CH_360_110`;
CREATE TABLE `CH_360_110` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_120`
--

DROP TABLE IF EXISTS `CH_360_120`;
CREATE TABLE `CH_360_120` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_130`
--

DROP TABLE IF EXISTS `CH_360_130`;
CREATE TABLE `CH_360_130` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_140`
--

DROP TABLE IF EXISTS `CH_360_140`;
CREATE TABLE `CH_360_140` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_150`
--

DROP TABLE IF EXISTS `CH_360_150`;
CREATE TABLE `CH_360_150` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_160`
--

DROP TABLE IF EXISTS `CH_360_160`;
CREATE TABLE `CH_360_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_170`
--

DROP TABLE IF EXISTS `CH_360_170`;
CREATE TABLE `CH_360_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_180`
--

DROP TABLE IF EXISTS `CH_360_180`;
CREATE TABLE `CH_360_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_20`
--

DROP TABLE IF EXISTS `CH_360_20`;
CREATE TABLE `CH_360_20` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_30`
--

DROP TABLE IF EXISTS `CH_360_30`;
CREATE TABLE `CH_360_30` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_40`
--

DROP TABLE IF EXISTS `CH_360_40`;
CREATE TABLE `CH_360_40` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_50`
--

DROP TABLE IF EXISTS `CH_360_50`;
CREATE TABLE `CH_360_50` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_60`
--

DROP TABLE IF EXISTS `CH_360_60`;
CREATE TABLE `CH_360_60` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_70`
--

DROP TABLE IF EXISTS `CH_360_70`;
CREATE TABLE `CH_360_70` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_80`
--

DROP TABLE IF EXISTS `CH_360_80`;
CREATE TABLE `CH_360_80` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_360_90`
--

DROP TABLE IF EXISTS `CH_360_90`;
CREATE TABLE `CH_360_90` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_40_0`
--

DROP TABLE IF EXISTS `CH_40_0`;
CREATE TABLE `CH_40_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9905569 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-80)';

--
-- Table structure for table `CH_40_10`
--

DROP TABLE IF EXISTS `CH_40_10`;
CREATE TABLE `CH_40_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-70)';

--
-- Table structure for table `CH_40_100`
--

DROP TABLE IF EXISTS `CH_40_100`;
CREATE TABLE `CH_40_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>20)';

--
-- Table structure for table `CH_40_110`
--

DROP TABLE IF EXISTS `CH_40_110`;
CREATE TABLE `CH_40_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>30)';

--
-- Table structure for table `CH_40_120`
--

DROP TABLE IF EXISTS `CH_40_120`;
CREATE TABLE `CH_40_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>40)';

--
-- Table structure for table `CH_40_130`
--

DROP TABLE IF EXISTS `CH_40_130`;
CREATE TABLE `CH_40_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338079 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>50)';

--
-- Table structure for table `CH_40_140`
--

DROP TABLE IF EXISTS `CH_40_140`;
CREATE TABLE `CH_40_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10321965 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>60)';

--
-- Table structure for table `CH_40_150`
--

DROP TABLE IF EXISTS `CH_40_150`;
CREATE TABLE `CH_40_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9103399 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>70)';

--
-- Table structure for table `CH_40_160`
--

DROP TABLE IF EXISTS `CH_40_160`;
CREATE TABLE `CH_40_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_40_170`
--

DROP TABLE IF EXISTS `CH_40_170`;
CREATE TABLE `CH_40_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_40_180`
--

DROP TABLE IF EXISTS `CH_40_180`;
CREATE TABLE `CH_40_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_40_20`
--

DROP TABLE IF EXISTS `CH_40_20`;
CREATE TABLE `CH_40_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-60)';

--
-- Table structure for table `CH_40_30`
--

DROP TABLE IF EXISTS `CH_40_30`;
CREATE TABLE `CH_40_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-50)';

--
-- Table structure for table `CH_40_40`
--

DROP TABLE IF EXISTS `CH_40_40`;
CREATE TABLE `CH_40_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-40)';

--
-- Table structure for table `CH_40_50`
--

DROP TABLE IF EXISTS `CH_40_50`;
CREATE TABLE `CH_40_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337099 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-30)';

--
-- Table structure for table `CH_40_60`
--

DROP TABLE IF EXISTS `CH_40_60`;
CREATE TABLE `CH_40_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10248086 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-20)';

--
-- Table structure for table `CH_40_70`
--

DROP TABLE IF EXISTS `CH_40_70`;
CREATE TABLE `CH_40_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10312953 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>-10)';

--
-- Table structure for table `CH_40_80`
--

DROP TABLE IF EXISTS `CH_40_80`;
CREATE TABLE `CH_40_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>0)';

--
-- Table structure for table `CH_40_90`
--

DROP TABLE IF EXISTS `CH_40_90`;
CREATE TABLE `CH_40_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-140,lat>10)';

--
-- Table structure for table `CH_50_0`
--

DROP TABLE IF EXISTS `CH_50_0`;
CREATE TABLE `CH_50_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9570286 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-80)';

--
-- Table structure for table `CH_50_10`
--

DROP TABLE IF EXISTS `CH_50_10`;
CREATE TABLE `CH_50_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-70)';

--
-- Table structure for table `CH_50_100`
--

DROP TABLE IF EXISTS `CH_50_100`;
CREATE TABLE `CH_50_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>20)';

--
-- Table structure for table `CH_50_110`
--

DROP TABLE IF EXISTS `CH_50_110`;
CREATE TABLE `CH_50_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10085584 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>30)';

--
-- Table structure for table `CH_50_120`
--

DROP TABLE IF EXISTS `CH_50_120`;
CREATE TABLE `CH_50_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10336284 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>40)';

--
-- Table structure for table `CH_50_130`
--

DROP TABLE IF EXISTS `CH_50_130`;
CREATE TABLE `CH_50_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10334109 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>50)';

--
-- Table structure for table `CH_50_140`
--

DROP TABLE IF EXISTS `CH_50_140`;
CREATE TABLE `CH_50_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10072528 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>60)';

--
-- Table structure for table `CH_50_150`
--

DROP TABLE IF EXISTS `CH_50_150`;
CREATE TABLE `CH_50_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339043 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>70)';

--
-- Table structure for table `CH_50_160`
--

DROP TABLE IF EXISTS `CH_50_160`;
CREATE TABLE `CH_50_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_50_170`
--

DROP TABLE IF EXISTS `CH_50_170`;
CREATE TABLE `CH_50_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_50_180`
--

DROP TABLE IF EXISTS `CH_50_180`;
CREATE TABLE `CH_50_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_50_20`
--

DROP TABLE IF EXISTS `CH_50_20`;
CREATE TABLE `CH_50_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-60)';

--
-- Table structure for table `CH_50_30`
--

DROP TABLE IF EXISTS `CH_50_30`;
CREATE TABLE `CH_50_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-50)';

--
-- Table structure for table `CH_50_40`
--

DROP TABLE IF EXISTS `CH_50_40`;
CREATE TABLE `CH_50_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-40)';

--
-- Table structure for table `CH_50_50`
--

DROP TABLE IF EXISTS `CH_50_50`;
CREATE TABLE `CH_50_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10243725 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-30)';

--
-- Table structure for table `CH_50_60`
--

DROP TABLE IF EXISTS `CH_50_60`;
CREATE TABLE `CH_50_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-20)';

--
-- Table structure for table `CH_50_70`
--

DROP TABLE IF EXISTS `CH_50_70`;
CREATE TABLE `CH_50_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>-10)';

--
-- Table structure for table `CH_50_80`
--

DROP TABLE IF EXISTS `CH_50_80`;
CREATE TABLE `CH_50_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>0)';

--
-- Table structure for table `CH_50_90`
--

DROP TABLE IF EXISTS `CH_50_90`;
CREATE TABLE `CH_50_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-130,lat>10)';

--
-- Table structure for table `CH_60_0`
--

DROP TABLE IF EXISTS `CH_60_0`;
CREATE TABLE `CH_60_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4198660 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-80)';

--
-- Table structure for table `CH_60_10`
--

DROP TABLE IF EXISTS `CH_60_10`;
CREATE TABLE `CH_60_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-70)';

--
-- Table structure for table `CH_60_100`
--

DROP TABLE IF EXISTS `CH_60_100`;
CREATE TABLE `CH_60_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337589 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>20)';

--
-- Table structure for table `CH_60_110`
--

DROP TABLE IF EXISTS `CH_60_110`;
CREATE TABLE `CH_60_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9860641 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>30)';

--
-- Table structure for table `CH_60_120`
--

DROP TABLE IF EXISTS `CH_60_120`;
CREATE TABLE `CH_60_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8537140 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>40)';

--
-- Table structure for table `CH_60_130`
--

DROP TABLE IF EXISTS `CH_60_130`;
CREATE TABLE `CH_60_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8514514 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>50)';

--
-- Table structure for table `CH_60_140`
--

DROP TABLE IF EXISTS `CH_60_140`;
CREATE TABLE `CH_60_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10193003 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>60)';

--
-- Table structure for table `CH_60_150`
--

DROP TABLE IF EXISTS `CH_60_150`;
CREATE TABLE `CH_60_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340903 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>70)';

--
-- Table structure for table `CH_60_160`
--

DROP TABLE IF EXISTS `CH_60_160`;
CREATE TABLE `CH_60_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_60_170`
--

DROP TABLE IF EXISTS `CH_60_170`;
CREATE TABLE `CH_60_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_60_180`
--

DROP TABLE IF EXISTS `CH_60_180`;
CREATE TABLE `CH_60_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_60_20`
--

DROP TABLE IF EXISTS `CH_60_20`;
CREATE TABLE `CH_60_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-60)';

--
-- Table structure for table `CH_60_30`
--

DROP TABLE IF EXISTS `CH_60_30`;
CREATE TABLE `CH_60_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-50)';

--
-- Table structure for table `CH_60_40`
--

DROP TABLE IF EXISTS `CH_60_40`;
CREATE TABLE `CH_60_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-40)';

--
-- Table structure for table `CH_60_50`
--

DROP TABLE IF EXISTS `CH_60_50`;
CREATE TABLE `CH_60_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-30)';

--
-- Table structure for table `CH_60_60`
--

DROP TABLE IF EXISTS `CH_60_60`;
CREATE TABLE `CH_60_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-20)';

--
-- Table structure for table `CH_60_70`
--

DROP TABLE IF EXISTS `CH_60_70`;
CREATE TABLE `CH_60_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>-10)';

--
-- Table structure for table `CH_60_80`
--

DROP TABLE IF EXISTS `CH_60_80`;
CREATE TABLE `CH_60_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>0)';

--
-- Table structure for table `CH_60_90`
--

DROP TABLE IF EXISTS `CH_60_90`;
CREATE TABLE `CH_60_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9369684 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-120,lat>10)';

--
-- Table structure for table `CH_70_0`
--

DROP TABLE IF EXISTS `CH_70_0`;
CREATE TABLE `CH_70_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10214283 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-80)';

--
-- Table structure for table `CH_70_10`
--

DROP TABLE IF EXISTS `CH_70_10`;
CREATE TABLE `CH_70_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-70)';

--
-- Table structure for table `CH_70_100`
--

DROP TABLE IF EXISTS `CH_70_100`;
CREATE TABLE `CH_70_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10322547 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>20)';

--
-- Table structure for table `CH_70_110`
--

DROP TABLE IF EXISTS `CH_70_110`;
CREATE TABLE `CH_70_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8783008 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>30)';

--
-- Table structure for table `CH_70_120`
--

DROP TABLE IF EXISTS `CH_70_120`;
CREATE TABLE `CH_70_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9574896 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>40)';

--
-- Table structure for table `CH_70_130`
--

DROP TABLE IF EXISTS `CH_70_130`;
CREATE TABLE `CH_70_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8388193 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>50)';

--
-- Table structure for table `CH_70_140`
--

DROP TABLE IF EXISTS `CH_70_140`;
CREATE TABLE `CH_70_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339099 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>60)';

--
-- Table structure for table `CH_70_150`
--

DROP TABLE IF EXISTS `CH_70_150`;
CREATE TABLE `CH_70_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340907 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>70)';

--
-- Table structure for table `CH_70_160`
--

DROP TABLE IF EXISTS `CH_70_160`;
CREATE TABLE `CH_70_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_70_170`
--

DROP TABLE IF EXISTS `CH_70_170`;
CREATE TABLE `CH_70_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_70_180`
--

DROP TABLE IF EXISTS `CH_70_180`;
CREATE TABLE `CH_70_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_70_20`
--

DROP TABLE IF EXISTS `CH_70_20`;
CREATE TABLE `CH_70_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-60)';

--
-- Table structure for table `CH_70_30`
--

DROP TABLE IF EXISTS `CH_70_30`;
CREATE TABLE `CH_70_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-50)';

--
-- Table structure for table `CH_70_40`
--

DROP TABLE IF EXISTS `CH_70_40`;
CREATE TABLE `CH_70_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-40)';

--
-- Table structure for table `CH_70_50`
--

DROP TABLE IF EXISTS `CH_70_50`;
CREATE TABLE `CH_70_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9912094 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-30)';

--
-- Table structure for table `CH_70_60`
--

DROP TABLE IF EXISTS `CH_70_60`;
CREATE TABLE `CH_70_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-20)';

--
-- Table structure for table `CH_70_70`
--

DROP TABLE IF EXISTS `CH_70_70`;
CREATE TABLE `CH_70_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>-10)';

--
-- Table structure for table `CH_70_80`
--

DROP TABLE IF EXISTS `CH_70_80`;
CREATE TABLE `CH_70_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>0)';

--
-- Table structure for table `CH_70_90`
--

DROP TABLE IF EXISTS `CH_70_90`;
CREATE TABLE `CH_70_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10304065 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-110,lat>10)';

--
-- Table structure for table `CH_80_0`
--

DROP TABLE IF EXISTS `CH_80_0`;
CREATE TABLE `CH_80_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9518847 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-80)';

--
-- Table structure for table `CH_80_10`
--

DROP TABLE IF EXISTS `CH_80_10`;
CREATE TABLE `CH_80_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8363863 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-70)';

--
-- Table structure for table `CH_80_100`
--

DROP TABLE IF EXISTS `CH_80_100`;
CREATE TABLE `CH_80_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337364 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>20)';

--
-- Table structure for table `CH_80_110`
--

DROP TABLE IF EXISTS `CH_80_110`;
CREATE TABLE `CH_80_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=8685941 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>30)';

--
-- Table structure for table `CH_80_120`
--

DROP TABLE IF EXISTS `CH_80_120`;
CREATE TABLE `CH_80_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9543756 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>40)';

--
-- Table structure for table `CH_80_130`
--

DROP TABLE IF EXISTS `CH_80_130`;
CREATE TABLE `CH_80_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337459 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>50)';

--
-- Table structure for table `CH_80_140`
--

DROP TABLE IF EXISTS `CH_80_140`;
CREATE TABLE `CH_80_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340923 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>60)';

--
-- Table structure for table `CH_80_150`
--

DROP TABLE IF EXISTS `CH_80_150`;
CREATE TABLE `CH_80_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338899 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>70)';

--
-- Table structure for table `CH_80_160`
--

DROP TABLE IF EXISTS `CH_80_160`;
CREATE TABLE `CH_80_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_80_170`
--

DROP TABLE IF EXISTS `CH_80_170`;
CREATE TABLE `CH_80_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_80_180`
--

DROP TABLE IF EXISTS `CH_80_180`;
CREATE TABLE `CH_80_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_80_20`
--

DROP TABLE IF EXISTS `CH_80_20`;
CREATE TABLE `CH_80_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-60)';

--
-- Table structure for table `CH_80_30`
--

DROP TABLE IF EXISTS `CH_80_30`;
CREATE TABLE `CH_80_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-50)';

--
-- Table structure for table `CH_80_40`
--

DROP TABLE IF EXISTS `CH_80_40`;
CREATE TABLE `CH_80_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-40)';

--
-- Table structure for table `CH_80_50`
--

DROP TABLE IF EXISTS `CH_80_50`;
CREATE TABLE `CH_80_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-30)';

--
-- Table structure for table `CH_80_60`
--

DROP TABLE IF EXISTS `CH_80_60`;
CREATE TABLE `CH_80_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-20)';

--
-- Table structure for table `CH_80_70`
--

DROP TABLE IF EXISTS `CH_80_70`;
CREATE TABLE `CH_80_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10332454 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>-10)';

--
-- Table structure for table `CH_80_80`
--

DROP TABLE IF EXISTS `CH_80_80`;
CREATE TABLE `CH_80_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9572396 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>0)';

--
-- Table structure for table `CH_80_90`
--

DROP TABLE IF EXISTS `CH_80_90`;
CREATE TABLE `CH_80_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10030304 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-100,lat>10)';

--
-- Table structure for table `CH_90_0`
--

DROP TABLE IF EXISTS `CH_90_0`;
CREATE TABLE `CH_90_0` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=4197510 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-80)';

--
-- Table structure for table `CH_90_10`
--

DROP TABLE IF EXISTS `CH_90_10`;
CREATE TABLE `CH_90_10` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-70)';

--
-- Table structure for table `CH_90_100`
--

DROP TABLE IF EXISTS `CH_90_100`;
CREATE TABLE `CH_90_100` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10337804 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>20)';

--
-- Table structure for table `CH_90_110`
--

DROP TABLE IF EXISTS `CH_90_110`;
CREATE TABLE `CH_90_110` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10319259 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>30)';

--
-- Table structure for table `CH_90_120`
--

DROP TABLE IF EXISTS `CH_90_120`;
CREATE TABLE `CH_90_120` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=9912148 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>40)';

--
-- Table structure for table `CH_90_130`
--

DROP TABLE IF EXISTS `CH_90_130`;
CREATE TABLE `CH_90_130` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10339023 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>50)';

--
-- Table structure for table `CH_90_140`
--

DROP TABLE IF EXISTS `CH_90_140`;
CREATE TABLE `CH_90_140` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10340223 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>60)';

--
-- Table structure for table `CH_90_150`
--

DROP TABLE IF EXISTS `CH_90_150`;
CREATE TABLE `CH_90_150` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10338279 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>70)';

--
-- Table structure for table `CH_90_160`
--

DROP TABLE IF EXISTS `CH_90_160`;
CREATE TABLE `CH_90_160` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_90_170`
--

DROP TABLE IF EXISTS `CH_90_170`;
CREATE TABLE `CH_90_170` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_90_180`
--

DROP TABLE IF EXISTS `CH_90_180`;
CREATE TABLE `CH_90_180` (
  `col1` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `CH_90_20`
--

DROP TABLE IF EXISTS `CH_90_20`;
CREATE TABLE `CH_90_20` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-60)';

--
-- Table structure for table `CH_90_30`
--

DROP TABLE IF EXISTS `CH_90_30`;
CREATE TABLE `CH_90_30` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-50)';

--
-- Table structure for table `CH_90_40`
--

DROP TABLE IF EXISTS `CH_90_40`;
CREATE TABLE `CH_90_40` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=7084913 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-40)';

--
-- Table structure for table `CH_90_50`
--

DROP TABLE IF EXISTS `CH_90_50`;
CREATE TABLE `CH_90_50` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10100640 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-30)';

--
-- Table structure for table `CH_90_60`
--

DROP TABLE IF EXISTS `CH_90_60`;
CREATE TABLE `CH_90_60` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-20)';

--
-- Table structure for table `CH_90_70`
--

DROP TABLE IF EXISTS `CH_90_70`;
CREATE TABLE `CH_90_70` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10245216 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>-10)';

--
-- Table structure for table `CH_90_80`
--

DROP TABLE IF EXISTS `CH_90_80`;
CREATE TABLE `CH_90_80` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10317159 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>0)';

--
-- Table structure for table `CH_90_90`
--

DROP TABLE IF EXISTS `CH_90_90`;
CREATE TABLE `CH_90_90` (
  `idpoint` int(11) NOT NULL auto_increment,
  `idcoast` int(11) NOT NULL default '0',
  `longitude` double NOT NULL default '0',
  `latitude` double NOT NULL default '0',
  PRIMARY KEY  (`idpoint`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`longitude`),
  KEY `idcoast` (`idcoast`)
) ENGINE=MyISAM AUTO_INCREMENT=10330669 DEFAULT CHARSET=latin1 COMMENT='Coastline: 10 degree square (long>-90,lat>10)';

