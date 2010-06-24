<?php

include_once "config.php";

$boat_num = array("-56");


//parse fichier b2b
$filename="http://213.41.63.15/ocean-express/getinfos.php";
if ($fd = fopen ($filename, "r")) {
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);
        if (ereg(';',$buffer)) {

     // 200711191800;50 31.72' N;1 15.28' W;16.00;200;

           $ligne = preg_split ("/\;/",$buffer);
     list($buf_tstamp,$buf_lat,$buf_lon,$boatspeed,$boatheading) = $ligne;
     
     // YYYYMMDDHHMM => YYYY/MM/DD HH:MM
     $time = strtotime(substr($buf_tstamp,0,4)."/"
                      .substr($buf_tstamp,4,2)."/"
          .substr($buf_tstamp,6,2)." "
          .substr($buf_tstamp,8,2).":"
          .substr($buf_tstamp,10,2));
     $time = time();

     // Conversion en degrés décimaux signés de la latitude
           if (ereg('N',$buf_lat)){
             $trimmed = trim($buf_lat, "N");
             $coord = preg_split ("/ /",$trimmed); 
       $lat_deg = $coord[0];
       $lat_minsec = preg_split ("/\./", $coord[1]);
             $latb=$lat_deg + $lat_minsec[0]/60 + $lat_minsec[1]/3600;
           }

           if (ereg('S',$buf_lat)){
             $trimmed = trim($buf_lat, "S");
             $coord = preg_split ("/ /",$trimmed);
       $lat_deg = $coord[0];
       $lat_minsec = preg_split ("/\./", $coord[1]);
             $latb=-1*($lat_deg + $lat_minsec[0]/60 + $lat_minsec[1]/3600);
           }

     // Conversion en degrés décimaux signés de la longitude
           if (ereg('E',$buf_lon)){
             $trimmed = trim($buf_lon, "E");
             $coord = preg_split ("/ /",$trimmed);
       $lon_deg = $coord[0];
       $lon_minsec = preg_split ("/\./", $coord[1]);
             $lonb=$lon_deg + $lon_minsec[0]/60 + $lon_minsec[1]/3600;
           }
           if (ereg('W',$buf_lon)){
             $trimmed = trim($buf_lon, "W");
             $coord = preg_split ("/ /",$trimmed);

       $lon_deg = $coord[0];
       $lon_minsec = preg_split ("/\./", $coord[1]);
             $lonb=-1*($lon_deg + $lon_minsec[0]/60 + $lon_minsec[1]/3600);
           }
        }
    }
}
// printf ("DATE=%s, LON=%s, LAT=%s\n", $time, $latb, $lonb);
    
if ( $latb != 0 && $lonb != 0 ) {
 $query  ="delete from positions where idusers=-56 ;" ;
 mysql_query($query) or die("Query failed : " . mysql_error." ".$query);

 $query ="insert into positions values ";
 $query .= "( $time , $lonb*1000, $latb*1000, -56, 20071201) ;";

 mysql_query($query) or die("OE-POS : Query failed : " . mysql_error." ".$query);
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
