<?php

    include_once "config.php";

    // Main : open stdin and wait for lines
    if ($fd = fopen('php://stdin', 'r') ) {
        while (!feof ($fd)) {
            $buffer = fgets($fd, 4096);

            // Parse the line and find words (username + password)
            if (ereg(';',$buffer)) {
               $ligne = explode (";",rtrim($buffer,"\n"));
//               print_r($ligne);

               if (count($ligne) == 8 ) {
                  $idusers=-$ligne[0];
                  $bn=preg_split("/ - /",$ligne[1]);
                  $boatname=$bn[0] . "<br>" . $bn[1]. " ";
               
                  $lat = preg_split("/[ .']/",$ligne[2]);
//            print_r($lat);
                  if ($lat[4] == "S"){ $latb=-1*($lat[0]+ $lat[1]/60 + $lat[2]/3600); }
                  if ($lat[4] == "N"){ $latb=$lat[0]+ $lat[1]/60 + $lat[2]/3600; }

                  $lon = preg_split("/[ .']/",$ligne[3]);
//            print_r($lon);

                  if ($lon[4] == "W"){ $lonb=-1*($lon[0]+ $lon[1]/60 + $lon[2]/3600); }
                  if ($lon[4] == "E"){ $lonb=$lon[0]+ $lon[1]/60 + $lon[2]/3600; }

      $loch=ortho($latb*1000, $lonb*1000, 47855, -3947);

        // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
        $time=time();
                  printf ("Boat=%s, Num=%d, Time=%s, LAT=%s, LON=%s\n", $boatname, $idusers,$time, $latb, $lonb);
                  $query="replace into users (idusers,password,username,engaged,nextwaypoint,userdeptime,loch)
                                values ($idusers, 'xxxxxxxx', '".$boatname."', 20080420,2,1208692800,$loch);";
           mysql_query($query) or die("AG2R : Query failed : " . mysql_error." ".$query);

                  $query="replace into races_ranking (idraces,idusers,latitude,longitude,loch,nwp,dnm)
                                values (20080420,$idusers, $latb*1000,$lonb*1000, $loch,2,$ligne[6]);";
           mysql_query($query) or die("AG2R : Query failed : " . mysql_error." ".$query);
           //echo "$query\n";


           $query ="insert into positions values ";
           $query .= "( $time , $lonb*1000, $latb*1000, $idusers, 20080420, '' ) ;";

           mysql_query($query) or die("AG2R : Query failed : " . mysql_error." ".$query);
           //echo "$query\n";

           $fullUsersObj = new fullUsers($idusers);
           $fullUsersObj->writeCurrentRanking();
                  printf("\n");
            }

  }
   }

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
