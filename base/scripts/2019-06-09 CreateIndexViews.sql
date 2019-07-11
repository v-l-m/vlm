drop view if exists VIEW_ENGAGED_PER_RACE;
create view VIEW_ENGAGED_PER_RACE as
 select RR.idraces idraces,R.racetype racetype, R.deptime deptime, count(idusers) engaged, min(RR.deptime+RR.duration) Date1stArrival from races_results RR join races R on RR.idraces=R.idraces and idusers>0 group by idraces, racetype, R.deptime;

drop view if exists VIEW_RACE_COEF;
create view VIEW_RACE_COEF as
  select idraces, 
    case when engaged < 50 then (1) 
    when engaged < 100 then (2) 
    when engaged < 250 then (3)
    when engaged < 500 then (4)
    else (5)
    end  coef from VIEW_ENGAGED_PER_RACE; 

drop procedure if exists SP_BUILD_VLM_INDEX;
DELIMITER //
CREATE PROCEDURE SP_BUILD_VLM_INDEX
(
  IN StartDate bigint,
  IN EndDate bigint,
  IN pRaceType int,
  IN WithDetail int
) 
BEGIN

  declare v_finished int default 0;
  declare CurRace int default 0;
  declare MinFactor numeric(6,3) default 36;
  declare MaxFactor numeric(6,3) default 52;

  DECLARE crsr_race CURSOR FOR 
    select idraces from VIEW_ENGAGED_PER_RACE
    where racetype=pRaceType and (Date1stArrival>=StartDate and Date1stArrival <= EndDate);

  DECLARE CONTINUE HANDLER 
        FOR NOT FOUND SET v_finished = 1;
  
  drop temporary table if exists tmpPlayersIndex;
  create temporary table tmpPlayersIndex (idplayers bigint(20) not null,idraces int not null ,rank int not null, Bonus decimal(6,3), primary key (idplayers,idraces));
  drop temporary table if exists tmpPlayersRaceCount;
  create temporary table tmpPlayersRaceCount (idplayers bigint(20) not null, RaceCount int not null, primary key (idplayers));
  
  #Loop each race included for index 
  open crsr_race;
  
  make_race_ranking: LOOP

  fetch crsr_race into CurRace;
  if v_finished=1 then
    LEAVE make_race_ranking;
  end if;

  #insert players ranking for this race
  insert into tmpPlayersIndex (idplayers,rank, idraces, Bonus)
    select P.idplayers,
        @rank:=CASE
            WHEN @race = RR.idraces THEN @rank + 1
            ELSE 1
        END AS rank,
        @race:=RR.idraces as idraces, 
        case
            when @rank <=10 then 1.2 - 0.02*(@rank-1)
            else 1
        END as Bonus
    from players P 
    join playerstousers PU on P.idplayers = PU.idplayers and linktype=1
    join races_results RR on RR.idusers = PU.idusers and RR.idraces = CurRace
    where RR.position > 0 
    order by duration;
  
  #select * from tmpPlayersIndex where idplayers in (151,87,2521);
  
  end loop make_race_ranking;

  close crsr_race;



  #select * from tmpPlayersIndex where idplayers=2521;
  #select * from tmpPlayersIndex where idraces=20190527 order by rank;

  insert into tmpPlayersRaceCount (idplayers,RaceCount)
    select players.idplayers, count(idraces) NbRaces
    from players  join tmpPlayersIndex PI on players.idplayers=PI.idplayers
    group by players.idplayers;

  #select * from tmpPlayersRaceCount where idplayers = 2521 order by idplayers;

  
  select Pl.playername,P.idplayers,PRC.RaceCount,
    sum( coef * (E.engaged - P.rank+1)* P.Bonus )   /
    (case 
      when PRC.RaceCount < MinFactor then MinFactor
      when PRC.RaceCount > MaxFactor then MaxFactor
      else PRC.RaceCount
    end)  as vlmindex, sum( coef * (E.engaged - P.rank+1)* P.Bonus )
    from tmpPlayersRaceCount PRC 
    join players Pl on Pl.idplayers = PRC.idplayers
    join tmpPlayersIndex P on PRC.idplayers = P.idplayers
    join VIEW_ENGAGED_PER_RACE E on E.idraces = P.idraces
    join VIEW_RACE_COEF RC on RC.idraces = P.idraces
    group by Pl.playername,P.idplayers,PRC.RaceCount
    order by 4 desc;

  if WithDetail then
    select Pl.playername,P.* 
    from tmpPlayersIndex P
    join players Pl on Pl.idplayers = P.idplayers
    order by idraces, Rank ;
  END IF;

  drop temporary table tmpPlayersIndex;
END //
DELIMITER ;

#call SP_BUILD_VLM_INDEX(1528614625,0);
#call SP_BUILD_VLM_INDEX(1546300800,0);
call SP_BUILD_VLM_INDEX(UNIX_TIMESTAMP()-365*3600*24,UNIX_TIMESTAMP(),0,1);

