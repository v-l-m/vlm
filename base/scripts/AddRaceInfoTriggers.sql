drop trigger if exists TRG_RACE_WAYPOINT_UPDATE;
drop trigger if exists TRG_RACE_WAYPOINT_INSERT;
drop trigger if exists TRG_RACE_WAYPOINT_DELETE;

create trigger TRG_RACE_WAYPOINT_INSERT after INSERT on races_waypoints 
  for each row update races set updated = now() where idraces=NEW.idraces;

create trigger TRG_RACE_WAYPOINT_UPDATE after UPDATE on races_waypoints 
  for each row update races set updated = now() where idraces=NEW.idraces;

create trigger TRG_RACE_WAYPOINT_DELETE before DELETE on races_waypoints 
  for each row update races set updated = now() where idraces=OLD.idraces;

drop trigger if exists TRG_WAYPOINT_UPDATE;
drop trigger if exists TRG_WAYPOINT_INSERT;
drop trigger if exists TRG_WAYPOINT_DELETE;

create trigger TRG_WAYPOINT_INSERT after INSERT on waypoints 
  for each row update races set updated = now() where idraces in (select distinct idraces from races_waypoints  where NEW.idwaypoint = races_waypoints.idwaypoint);

create trigger TRG_WAYPOINT_UPDATE after UPDATE on waypoints 
  for each row update races set updated = now() where idraces in (select distinct idraces from races_waypoints  where NEW.idwaypoint = races_waypoints.idwaypoint);

create trigger TRG_WAYPOINT_DELETE before DELETE on waypoints 
  for each row update races set updated = now() where idraces in (select distinct idraces from races_waypoints  where OLD.idwaypoint = races_waypoints.idwaypoint);

drop trigger if exists TRG_SEGMENT_UPDATE;
drop trigger if exists TRG_SEGMENT_INSERT;
drop trigger if exists TRG_SEGMENT_DELETE;

create trigger TRG_SEGMENT_INSERT after INSERT on nszsegment 
  for each row update races set updated = now() where idraces in (select distinct idraces from nszracesegment  where NEW.idsegment = nszracesegment.idsegment);

create trigger TRG_SEGMENT_UPDATE after UPDATE on nszsegment 
  for each row update races set updated = now() where idraces in (select distinct idraces from nszracesegment  where NEW.idsegment = nszracesegment.idsegment);

create trigger TRG_SEGMENT_DELETE before DELETE on nszsegment 
  for each row update races set updated = now() where idraces in (select distinct idraces from nszracesegment  where OLD.idsegment = nszracesegment.idsegment);

drop trigger if exists TRG_RACESEGMENT_UPDATE;
drop trigger if exists TRG_RACESEGMENT_INSERT;
drop trigger if exists TRG_RACESEGMENT_DELETE;

create trigger TRG_RACESEGMENT_INSERT after INSERT on nszracesegment 
  for each row update races set updated = now() where idraces=NEW.idraces;

create trigger TRG_RACESEGMENT_UPDATE after UPDATE on nszracesegment 
  for each row update races set updated = now() where idraces=NEW.idraces;

create trigger TRG_RACESEGMENT_DELETE after DELETE on nszracesegment 
  for each row update races set updated = now() where idraces=OLD.idraces;