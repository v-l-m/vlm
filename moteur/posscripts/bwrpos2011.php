<?php

//include_once "config.php";

$boat_num = array("", "-85", "-9", "-99", "-35", "-1876", "-29", "-33", "-4", "-06");


//parse fichier b2b
//define("VLMTEMP", "/home/vlm/tmp");
define("VLMTEMP", "/tmp");
$filename=VLMTEMP."/bwr-ranking.txt";
if ($fd = fopen ($filename, "r")){
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);

        if (ereg('^[0-9]+\.',$buffer)){
           $ligne = preg_split ("/\;/",$buffer);

           print_r($ligne);
        }
/*
           $class[$boat]=$ligne[0];
     $time = strtotime("20".substr($ligne[4],6,2)."/".substr($ligne[4],0,2)."/".substr($ligne[4],3,2)." ".substr($ligne[4],9));


           $buf_lat=$ligne[2];
           if (ereg('N',$buf_lat)){
             $trimmed = trim($buf_lat, "N");
             $coord = preg_split ("/\./",$trimmed); // ça nest pas très élégant mais ça permet de gérer les coord à 1, 2 ou 3 chiffre(s)
             $latb[$boat]=$coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600;
           }
           if (ereg('S',$buf_lat)){
             $trimmed = trim($buf_lat, "S");
             $coord = preg_split ("/\./",$trimmed);
             $latb[$boat]=-1*($coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600);
           }

           $buf_lon=$ligne[3];
           if (ereg('E',$buf_lon)){
             $trimmed = trim($buf_lon, "E");
               $coord = preg_split ("/\./",$trimmed);
             $lonb[$boat]=$coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600;
           }
           if (ereg('W',$buf_lon)){
             $trimmed = trim($buf_lon, "W");
               $coord = preg_split ("/\./",$trimmed);
             $lonb[$boat]=-1*($coord[0]+ substr($coord[1], 0, 2)/60 + substr($coord[1], 2, 2)/3600);
           }
        }
*/
        }
}

exit;


//affichage
//echo "Classement de $time<Br>";
for ($i = 1; $i<10; $i++){
    
  if ( $latb[$i] != 0 && $lonb[$i] != 0 ) {

    echo "Bateau $boat_num[$i] - classement $class[$i] / pos(lat,lon) : $latb[$i] , $lonb[$i]<br>\n";
     //$query  ="delete from positions where idusers=" . $boat_num[$i] . ";" ;
     //mysql_query($query) or die("BWR: Query failed : " . mysql_error." ".$query);

//     $vent = windAtPosition($latb[$i]*1000, $lonb[$i]*1000, 0, 'NEW' ) ;

//     $query ="insert into positions values ";
//     $query .= "( $time , $lonb[$i]*1000, $latb[$i]*1000, $boat_num[$i], 2008051160, '" . round($vent['speed'],1) . "," . round(($vent['windangle']+180)%360)."') ;";

     //mysql_query($query) or die("BWR : Query failed : " . mysql_error." ".$query);
     echo "$query\n";
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
