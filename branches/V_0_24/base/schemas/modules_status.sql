CREATE TABLE `modules_status` (
  `autoid` bigint(20) NOT NULL AUTO_INCREMENT,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `serverid` varchar(50) DEFAULT NULL,
  `moduleid` varchar(50) DEFAULT NULL,
  `revid` int(11) DEFAULT NULL,
  PRIMARY KEY (`autoid`)
) ENGINE=MyISAM AUTO_INCREMENT=680 DEFAULT CHARSET=utf8 COMMENT='status modules';
