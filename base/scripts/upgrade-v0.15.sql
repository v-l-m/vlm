#Nettoyages
DROP TABLE IF EXISTS races_loch;

#Tables loch pour les courses
CREATE TABLE `races_loch` (
  `time` bigint(20) default NULL,
  `loch` double default NULL,	
  `idusers` bigint(20) NOT NULL,	
  `idraces` bigint(20) NOT NULL,	
KEY `idraces` (`idraces`),
KEY `idusers` (`idusers`),
KEY `entry` (`time`,`idraces`,`idusers`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  
