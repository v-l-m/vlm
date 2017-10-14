delimiter $$

create function GetNextWeatherDate(ts timestamp)
returns timestamp
deterministic
begin
	declare t timestamp;
	declare h integer;
	set h = UNIX_TIMESTAMP(ts);
	set h = h - (h% (6*3600)) + 9.5*3600;
	
	return (FROM_UNIXTIME(h));
end

$$
delimiter ;

	