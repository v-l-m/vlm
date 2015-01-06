#Adding field for storing last run time
ALTER TABLE `races` ADD COLUMN `lastrun` timestamp default 0;


-- Using cursor on UserTrophyTable make it more easy for maintenance...
drop procedure `vlm`.`sptrophycontrolconnexion`;
-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

CREATE DEFINER=`vlm`@`%` PROCEDURE `sptrophycontrolconnexion`(par_dt date)
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE race, idu, idut INT;
  DECLARE condOAD double;
  DECLARE ID_TROPHY int;

DECLARE curTrophy CURSOR FOR SELECT UT.idraces, UT.idusers, UT.idUsersTrophies FROM users_Trophies UT 
	right join users US on UT.idusers = US.idUsers
	WHERE UT.RefTrophy=ID_TROPHY AND UT.idusers IS NOT NULL AND UT.quitdate IS NULL AND US.engaged=UT.idraces; -- par_race;

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

start transaction;
set ID_TROPHY=2;
if (par_dt IS NULL) then 
	set par_dt = CURRENT_DATE; 
end if;

-- Clear counter for race/date combinatioin 
DELETE FROM racetrophycontrol 
	WHERE racetrophycontrol.controldate=par_dt 
		AND racetrophycontrol.idusertrophy in (select iduserstrophies from users_Trophies where RefTrophy=ID_TROPHY);

-- Insert duration for all players that played on that day for the race and registered for the trophy and did not get kicked

OPEN curTrophy;

REPEAT
    FETCH curTrophy INTO race,idu,idut;
    IF NOT done THEN
		SET condOAD = (SELECT TIME_TO_SEC(TIMEDIFF(MAX(user_action.time),MIN(user_action.time))) 
		FROM user_action 
		WHERE user_action.idraces=race AND user_action.idusers=idu AND user_action.ipaddr <>'127.0.0.1' AND DATE(user_action.time)=par_dt LIMIT 1);
		insert into racetrophycontrol (controldate,idusertrophy,Score) SELECT par_dt, idut, condOAD;
		IF NOT (condOAD IS NULL OR condOAD < 3600) THEN
			-- insert into racetrophycontrol (controldate,idusertrophy,Score) SELECT par_dt, idut, condOAD;
			UPDATE users_Trophies SET quitdate = par_dt WHERE idUsersTrophies=idut; 
		END IF;
    END IF;
UNTIL done END REPEAT;
CLOSE curTrophy;
 
END
