update races set lastrun = '1970-01-01 00:00:01' where lastrun = 0;
alter table races alter column lastrun set default '1970-01-01 00:00:01';
alter table races add racelength int not null default 0;