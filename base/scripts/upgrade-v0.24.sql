

CREATE TABLE IF NOT EXISTS nszsegment(
  idsegment BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  segname VARCHAR(100),
  lon1 DOUBLE NOT NULL,
  lat1 DOUBLE NOT NULL,
  lon2 DOUBLE NOT NULL,
  lat2 DOUBLE NOT NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
  )CHARSET=utf8 COMMENT='NSZ Segments table';
  
CREATE TABLE IF NOT EXISTS nszracesegment(
  idracesegment  BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  idraces int(11) not null,
  idsegment bigint not null REFERENCES NSZsegment(idsegment),
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
  
)CHARSET=utf8 COMMENT='NSZ Segments to races association table';

create unique index idx_idraces_idsegment on nszracesegment (idraces,idsegment);




ALTER TABLE news ADD COLUMN url VARCHAR(250);

