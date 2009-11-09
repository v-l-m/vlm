<?php
    include_once "config.php";

    // Main : open stdin and wait for lines
    if ($fd = fopen('php://stdin', 'r') ) {
        while (!feof ($fd)) {
            $buffer = fgets($fd, 4096);
            $ligne = explode ("|",rtrim($buffer,"\n"));
            if (count($ligne) != 10 ) continue;
            //20091108|1|1257681600|-729|BT|S�bastien Josse - Jean Fran�ois Cuzon|50.016000|-1.891500|85.252725|4651.600000
            $idusers=$ligne[3];
            $latb=$ligne[6];
            $lonb=$ligne[7];
            $loch=$ligne[8];
            $nwp=$ligne[1];
            $time=$ligne[2];
            $boatname=$ligne[4]."( ".$ligne[5]." )";
            $race=$ligne[0];
            $dnm=$ligne[9];

            // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
            $time = time();
            printf ("Boat=%s, Num=%d, Time=%s, LAT=%s, LON=%s\n", $boatname, $idusers,$time, $latb, $lonb);
            //$query="replace into users (idusers,password,username,engaged,nextwaypoint,userdeptime,loch)
            //            values ($idusers, 'xxxxxxxx', '".$boatname."', $race,1,1210510800,$loch);";
            //mysql_query($query) or die("KAWA! : Query failed : " . mysql_error." ".$query);
            //echo "$query\n";

            //$query="replace into races_ranking (idraces,idusers,latitude,longitude,loch,nwp,dnm, last1h)
            //            values ($race,$idusers, $latb*1000,$lonb*1000, $loch,1,$dnm,$last1h);";
            //mysql_query($query) or die("KAWA! : Query failed : " . mysql_error." ".$query);
            //echo "$query\n";


            $query ="insert into positions values ";
            $query .= "( $time , $lonb*1000, $latb*1000, $idusers, $race, '' ) ;";

            mysql_query($query) or die("KAWA : Query failed : " . mysql_error." ".$query);
            #echo "$query\n";
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
