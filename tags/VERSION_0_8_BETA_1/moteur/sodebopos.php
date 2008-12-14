<?php

include_once "config.php";

$boat_num= array(
		-11=>'SODEBO',
	);



$filename="http://trimaran-idec.geovoile.com/tourdumonde2007/positions.asp";
$filename="http://sodebo-voile.geovoile.com/tourdumonde/positions.asp";

if ($fd = fopen ($filename, "r")) {
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);
	// Si le bloc contient "trLigne".. on regarde ce qui suit : les 3 lignes suivantes sont intéressantes
        if ( ereg('trLigne',$buffer) ) {

	  // Le timestamp
	  $buffer= fgets($fd , 4096);
          $ligne = preg_split ("/[<>]/",$buffer);
	  $time=$ligne[2];

	  $buffer= fgets($fd , 4096);
          $ligne = preg_split ("/[<>]/",$buffer);

          $lat = preg_split("/[°, ]/",$ligne[2]);
	  //print_r($lat);
          if ($lat[3] == "S"){
                 $latb=-1*($lat[0]+ $lat[1]/60 + $lat[2]/3600);
          }
          if ($lat[3] == "N"){
                 $latb=$lat[0]+ $lat[1]/60 + $lat[2]/3600;
          }

	  $buffer= fgets($fd , 4096);
          $ligne = preg_split ("/[<>]/",$buffer);
          $lon = preg_split("/[°, ]/",$ligne[2]);
	  //print_r($lon);

          if ($lon[3] == "W"){
                 $lonb=-1*($lon[0]+ $lon[1]/60 + $lon[2]/3600);
          }
          if ($lon[3] == "E"){
                 $lonb=$lon[0]+ $lon[1]/60 + $lon[2]/3600;
          }
          //printf ("Time=%s, LAT=%s, LON=%s\n", $time, $latb, $lonb);
	  break;
        }

  }
}

  // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
  $time=time();

  if ( $latb != 0 
    && $lonb != 0 ) {

     //echo "Bateau $boat_num[$i] - classement $class[$i] / pos(lat,lon) : $latb[$i] , $lonb[$i]<br>\n";
     $query  ="delete from positions where idusers=-11;" ;
     mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

     $query ="insert into positions values ";
     $query .= "( $time , $lonb*1000, $latb*1000, -11, 80, '') ;";

     mysql_db_query(DBNAME,$query) or die("BTOB : Query failed : " . mysql_error." ".$query);
     //echo "$query\n";
   }


/*
mysql> desc positions;
+---------+------------+------+-----+---------+-------+
| Field   | Type       | Null | Key | Default | Extra |
+---------+------------+------+-----+---------+-------+
| time    | bigint(20) | YES  | MUL | NULL    |       |
| long    | double     | YES  |     | NULL    |       |
| lat     | double     | YES  |     | NULL    |       |
| idusers | int(11)    | NO   | MUL | 0       |       |
| race    | int(11)    | YES  | MUL | NULL    |       |
+---------+------------+------+-----+---------+-------+
*/

?>
