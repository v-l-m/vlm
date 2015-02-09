#Creation de la table racespreview
DROP TABLE IF EXISTS `racespreview`;
CREATE TABLE `racespreview` (
  `idracespreview` int(11) NOT NULL auto_increment,
  `idraces` int(11) NOT NULL,
  `racename` varchar(255) NOT NULL default '',
  `deptime` bigint(14) default NULL,
  `racetype` int(11) default NULL,
  `comments` varchar(255) default NULL,
  `admincomments` varchar(255) default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idracespreview`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='The races that could exist in the future';


