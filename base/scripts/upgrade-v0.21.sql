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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `trophies`;
CREATE TABLE `trophies` (
  `idTrophies` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ControlPeriod` int(11) NOT NULL DEFAULT '60',
  `LastRun` datetime DEFAULT NULL,
  PRIMARY KEY (`idTrophies`),
  UNIQUE KEY `idTrophies_UNIQUE` (`idTrophies`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- recherche des users éligibles dans users_trophies, étude dans user actions et inscription dans racetrophycontrol
-- procédure stockée sptrophycontrolperdays
-- procédure stockée sptrophycontrolconnexion
-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$
CREATE DEFINER=`vlm`@`%` PROCEDURE `sptrophycontrolconnexion`(IN par_dt date)
BEGIN
declare var_dt date;
declare ID_TROPHY int;

start transaction;
set ID_TROPHY=2;
if (par_dt IS NULL) then 
	set par_dt = CURRENT_DATE; 
end if;
-- set par_dt = ISNULL(par_dt, CURRENT_DATE); 

-- Clear counter for race/date combinatioin 
DELETE FROM racetrophycontrol 
	WHERE racetrophycontrol.controldate=par_dt 
		AND racetrophycontrol.idusertrophy in (select iduserstrophies from users_Trophies where RefTrophy=ID_TROPHY);

-- Insert duration for all players that played on that day for the race and registered for the trophy and did not get kicked
insert into racetrophycontrol (controldate,idusertrophy,Score)
	select par_dt, UT.idUsersTrophies, TIME_TO_SEC(TIMEDIFF(MAX(U.time),MIN(U.time)))
	from user_action U
		inner join users_Trophies UT on U.idusers = UT.idusers and UT.quitdate is null
			inner join trophies T on T.idTrophies=UT.RefTrophy and T.idTrophies = ID_TROPHY  
		where date(U.time)=par_dt and U.idraces = UT.idraces and ipaddr<>'127.0.0.1'
		group by U.idusers, U.idraces, date(U.time)
UNION
	SELECT par_dt, UT.idUsersTrophies, 0
    FROM users_Trophies UT 
			inner join trophies T on T.idTrophies=UT.RefTrophy and T.idTrophies = ID_TROPHY
		WHERE UT.quitdate is null AND NOT EXISTS (
			SELECT * FROM  user_action A WHERE A.ipaddr <>'127.0.0.1' AND DATE(A.time)=par_dt AND A.idusers=UT.idusers)
	GROUP BY UT.idusers;

-- Delete all users that are above the threashold
UPDATE users_Trophies SET quitdate=par_dt 
	WHERE idUsersTrophies 
	IN (SELECT idusertrophy 
		FROM racetrophycontrol R 
		WHERE getTrophy(idusertrophy)=ID_TROPHY AND R.Score>3600 AND R.controldate=par_dt);
commit ;
END