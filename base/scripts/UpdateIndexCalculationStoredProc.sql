drop table if exists RaceIndexCoef ;
create table RaceIndexCoef 
(RacersCount int not null,RaceLength int not null, Coef float not null);

insert into RaceIndexCoef values (49,499.9999,1);
insert into RaceIndexCoef values (49,1499.99999,1.5);
insert into RaceIndexCoef values (49,4999.99999,2);
insert into RaceIndexCoef values (49,100000,2.5);
insert into RaceIndexCoef values (149,499.9999,1.5);
insert into RaceIndexCoef values (149,1499.99999,2);
insert into RaceIndexCoef values (149,4999.99999,2.5);
insert into RaceIndexCoef values (149,100000,3);
insert into RaceIndexCoef values (299,499.9999,2);
insert into RaceIndexCoef values (299,1499.99999,2.5);
insert into RaceIndexCoef values (299,4999.99999,3);
insert into RaceIndexCoef values (299,100000,3);
insert into RaceIndexCoef values (300000,499.9999,2.5);
insert into RaceIndexCoef values (300000,1499.99999,3);
insert into RaceIndexCoef values (300000,4999.99999,3);
insert into RaceIndexCoef values (300000,100000,3.5);

drop view if exists VIEW_ENGAGED_PER_RACE;
  create view VIEW_ENGAGED_PER_RACE as
  select T.idraces, T.racetype, T.deptime,sum(T.engaged) engaged, min(T.Date1stArrival) Date1stArrival, R.racelength
  from (  
    select * from VIEW_ENGAGED_PER_RACE_COMPLETE 
    union 
    select *, 999999999999 Date1stArrival from VIEW_ENGAGED_PER_RACE_RACING) T
  join races R on T.idraces = R.idraces
  group by idraces, racetype, deptime, R.racelength;

drop function FnRaceCoef;
DELIMITER //
CREATE FUNCTION FnRaceCoef (racers integer, length integer)
RETURNS REAL DETERMINISTIC
LANGUAGE SQL
BEGIN
  DECLARE Ret REAL;
    
  select coef 
  into Ret
  from RaceIndexCoef
  where RacersCount>=racers and RaceLength >= length
  order by RacersCount, RaceLength
  limit 1;

  return Ret;
END; //
DELIMITER ;

drop view if exists VIEW_RACE_COEF;
create view VIEW_RACE_COEF as
  select 0 as DateType,idraces, 
    case when engaged < 50 then (1) 
    when engaged < 100 then (2) 
    when engaged < 250 then (3)
    when engaged < 500 then (4)
    else (5)
    end  coef from VIEW_ENGAGED_PER_RACE
  union 
  select 1,idraces,FnRaceCoef(engaged,racelength)
  from VIEW_ENGAGED_PER_RACE ;


drop procedure if exists SP_BUILD_VLM_INDEX;
DELIMITER //
CREATE PROCEDURE SP_BUILD_VLM_INDEX
(
  IN StartDate bigint,
  IN EndDate bigint,
  IN pRaceType int,
  IN WithDetail int,
  IN MinFactor int,
  IN MaxFactor int
) 
BEGIN

  declare v_finished int default 0;
  declare CurRace int default 0;
  
  DECLARE crsr_race CURSOR FOR 
    select idraces from VIEW_ENGAGED_PER_RACE
    where racetype=pRaceType and (Date1stArrival>=StartDate and Date1stArrival <= EndDate) and idraces not in (20200123, 20200109, 20200306);

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
    where (RC.DateType = 0 and Date1stArrival <1577836800) 
    or (RC.DateType = 1) 
    group by Pl.playername,P.idplayers,PRC.RaceCount
    order by 4 desc;

  if WithDetail then
    select Pl.playername,P.* , RC.*
    from tmpPlayersIndex P
    join players Pl on Pl.idplayers = P.idplayers
    join VIEW_ENGAGED_PER_RACE E on E.idraces = P.idraces
    join VIEW_RACE_COEF RC on RC.idraces = P.idraces
    where (RC.DateType = 0 and Date1stArrival <1577836800) 
    or (RC.DateType = 1) 
    order by P.idraces, Rank ;
  END IF;

  drop temporary table tmpPlayersIndex;
END //
DELIMITER ;

# Challenge 1year
call SP_BUILD_VLM_INDEX(UNIX_TIMESTAMP()-365*3600*24,UNIX_TIMESTAMP(),0,1,36,52);

# Challenge 2019
call SP_BUILD_VLM_INDEX(1546300800,1577836800,0,1,1,1);
# Challenge 2020
call SP_BUILD_VLM_INDEX(1577836800,UNIX_TIMESTAMP(),0,1,1,1);
