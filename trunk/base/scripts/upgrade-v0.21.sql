DROP TABLE IF EXISTS `racetrophycontrol`;
CREATE TABLE `racetrophycontrol` (
  `controldate` datetime NOT NULL,
  `idusertrophy` int(11) NOT NULL DEFAULT '0',
  `Score` double DEFAULT NULL,
  PRIMARY KEY (`controldate`,`idusertrophy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users_Trophies`;
CREATE TABLE `users_Trophies` (
  `idUsersTrophies` int(11) NOT NULL AUTO_INCREMENT,
  `idusers` int(11) NOT NULL,
  `idraces` int(11) NOT NULL,
  `joindate` datetime NOT NULL,
  `RefTrophy` int(11) NOT NULL,
  `quitdate` datetime DEFAULT NULL,
  PRIMARY KEY (`idUsersTrophies`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8$$

DROP TABLE IF EXISTS `trophies`;
CREATE TABLE `trophies` (
  `idTrophies` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ControlPeriod` int(11) NOT NULL DEFAULT '60',
  `LastRun` datetime DEFAULT NULL,
  PRIMARY KEY (`idTrophies`),
  UNIQUE KEY `idTrophies_UNIQUE` (`idTrophies`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8$$


-- recherche des users éligibles dans users_trophies, étude dans user actions et inscription dans racetrophycontrol
-- procédure stockée sptrophycontrolperdays
-- procédure stockée sptrophycontrolconnexion