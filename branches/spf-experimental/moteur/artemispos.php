<?php

    include_once "config.php";

    // Main : open stdin and wait for lines
    if ($fd = fopen('php://stdin', 'r') ) {
        while (!feof ($fd)) {
            $buffer = fgets($fd, 4096);

            // Parse the line and find words 
            if (ereg(';',$buffer)) {
               $ligne = explode (";",rtrim($buffer,"\n"));
//               print_r($ligne);

               if (count($ligne) == 9 ) {
            //1;8;49.5879;-4.4989;1210517100;2940.2;12.5;Gitana Eighty;2008051160
                  $idusers=-100-$ligne[1];
               
                  $coord = preg_split ("/\./",$ligne[2]); 
                  $latb=$coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600;

                  $coord = preg_split ("/\./",$ligne[3]); 
                  $lonb=$coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600;


		  //$loch=ortho($lonb*1000, $latb*1000, -4151.5 , 50320);
                  $loch=0;

  		  $time=$ligne[4];
                  $boatname=$ligne[7];
                  $race=$ligne[8];
                  $dnm=$ligne[5];
                  $last1h=$ligne[6];

  		  // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
                  printf ("Boat=%s, Num=%d, Time=%s, LAT=%s, LON=%s\n", $boatname, $idusers,$time, $latb, $lonb);
                  $query="replace into users (idusers,password,username,engaged,nextwaypoint,userdeptime,loch)
                                values ($idusers, 'xxxxxxxx', '".$boatname."', $race,1,1210510800,$loch);";
     		  mysql_db_query(DBNAME,$query) or die("Artemis : Query failed : " . mysql_error." ".$query);
     		  //echo "$query\n";

                  $query="replace into races_ranking (idraces,idusers,latitude,longitude,loch,nwp,dnm, last1h)
                                values ($race,$idusers, $latb*1000,$lonb*1000, $loch,1,$dnm,$last1h);";
     		  mysql_db_query(DBNAME,$query) or die("Artemis : Query failed : " . mysql_error." ".$query);
     		  //echo "$query\n";


     		  $query ="insert into positions values ";
     		  $query .= "( $time , $lonb*1000, $latb*1000, $idusers, $race, '' ) ;";

     		  mysql_db_query(DBNAME,$query) or die("Artemis : Query failed : " . mysql_error." ".$query);
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
