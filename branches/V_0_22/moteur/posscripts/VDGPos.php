<?php

include_once "config.php";
$IDRACES=20081109;




$filename="http://www.vendeeglobe.org/fr/classement.html";
//$filename="/tmp/vdg.txt";
//$filename="/home/fmicaux/Deckman.txt";

if ($fd = fopen ($filename, "r")) {
    while (!feof ($fd)) {
         $buffer = fgets($fd, 4096);
        //printf ("==> %s", $buffer);
  // Si la ligne contient  une référence à la page du skipper, 
           // on en extrait numéro du bateau , et nom du skipper
           if ( ereg('/fr/edition-2008/skippers/[0-9]+',$buffer) ) {
                $tmp=split('[/<>"]', $buffer);
                $boat_num=-1*(800 + $tmp[8]);
                $boat_url=$tmp[9];
                $skipper=$tmp[11];

                // nom du bateau
                 $buffer = fgets($fd, 4096);
                $tmp=split('[/<>"]', $buffer);
                $boatname= $tmp[2];
                
                // Puis on lit les suivantes : heure et position notamment
                // une ligne vide 
                 $buffer = fgets($fd, 4096);

                // Date position
                 $buffer = fgets($fd, 4096);
                                               // Tab + espace
                $tmp=split('[<>]', preg_replace('/[   ]*/','',$buffer));
                $date_pos=$tmp[0];
                
                // Heure position
                 $buffer = fgets($fd, 4096);
                                               // Tab + espace
                $tmp=split('[<>]', preg_replace('/[   ]*/','',$buffer));
                $heure_pos=$tmp[0];

                // Ne fonctionne pas bien à cause de la carto qui ne remonte pas les vielles positions.
                $time = strtotime ($date_pos . " " . $heure_pos) +7200;

    // Latitude
                 $buffer = fgets($fd, 4096);
                                               // Tab seulement
                $tmp=split('[<>]', preg_replace('/[  ]*/','',$buffer));
                $latitude=$tmp[2];
                $tmp=split("[ .]",$latitude);
                if ($tmp[3] == "S" ){
                    $latb=-1*1000*($tmp[0]  +  100/60*($tmp[1] . "." . substr($tmp[2],0,2))/100);
                } else {
                    $latb=1000*($tmp[0]  +  100/60*($tmp[1] . "." . substr($tmp[2],0,2))/100);
                }

    // Longitude
                 $buffer = fgets($fd, 4096);
                                               // Tab seulement
                $tmp=split('[<>]', preg_replace('/[  ]*/','',$buffer));
                $longitude=$tmp[2];
                $tmp=split("[ .]",$longitude);
//print_r($tmp);
                if ($tmp[3] == "W" ){
                    $lonb=-1*1000*($tmp[0]  +  100/60*($tmp[1] . "." . substr($tmp[2],0,2))/100);
                } else {
                    $lonb=1000*($tmp[0]  +  100/60*($tmp[1] . "." . substr($tmp[2],0,2))/100);
                }

                //printf ("==> %s %s Sk=%s, Bateau=%s\t%s\t%s\t%s:%s\t%s:%s\n", $boat_num , $boat_url, $skipper, $boatname, $date_pos, $heure_pos, $latitude, $latb, $longitude, $lonb);


                // Refresh des données dans la table "Users"
                $query = "REPLACE INTO `users` (idusers,boattype,username,password,boatname,engaged,userdeptime) VALUES (" . $boat_num . ",'boat_Imoca2008','$skipper','xxxx','$boatname',$IDRACES,1226232120);";
                mysql_query($query) or die("VLM70 : Query failed : " . mysql_error." ".$query);

                $time = time();
                if ( $latb != 0 && $lonb != 0 ) {

                 /*
                  $query ="update positions ";
                  $query .= "set time = $time , `long` = $lonb, `lat` = $latb 
                             where idusers =  $boat_num and race = $IDRACES";
                 */
                  $query = "delete from positions where race= $IDRACES and idusers=$boat_num ;";
                  mysql_query($query) or die("VDG : Query failed : " . mysql_error." ".$query . "\n");
//echo $query . "\n";

                  $query = "insert into positions values ";
                  $query .= "( $time , $lonb, $latb,  " . $boat_num. ", $IDRACES, '" . round($vent[0],1) . "," . round(($vent[1]+180)%360) . "') ;";
//echo $query . "\n";
                  mysql_query($query) or die("VDG : Query failed : " . mysql_error." ".$query . "\n");

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
