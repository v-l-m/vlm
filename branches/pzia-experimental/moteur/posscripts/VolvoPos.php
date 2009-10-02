<?php

include_once "config.php";
$IDRACES=80106;
$boats= array(
    -701=>'ERT4',
    -702=>'ERT3',
    -703=>'ILMO',
    -704=>'KOSA',
    -705=>'TELA',
    -706=>'TELN',
    -707=>'GDRA',
    -708=>'DLYD',
  );

// Insertion des bateaux dans la base
while ( $boat = current($boats) ) {
    $query = "REPLACE INTO `users` VALUES (" . key($boats) . ",'boat_VLM70','$boat','xxxx','$boat','000000',161,'4',135.4,$IDRACES,1224489337,'',1,1223726400,1224489337,2655.89,'','admin',5,-26,-1,0,1216352105,0,'-','127.0.0.1','');";
    mysql_query($query) or die("VLM70 : Query failed : " . mysql_error." ".$query);
    next($boats);
}

$filename="http://www.volvooceanrace.org/downloads/Deckman.txt";
//$filename="/home/fmicaux/Deckman.txt";

if ($fd = fopen ($filename, "r")) {
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);
  // Si la ligne commence par un chiffre (position)
        if ( ereg('^[0-9];[A-Z]',$buffer) ) {

    // Le timestamp
          list($num,$nom,$lat,$lon,$time,$date,$heading,$speed,$dnm,$ecart) = preg_split ("/[;]/",$buffer);
          if (substr($lat,8,1) == "S"){
                 $latb=-1*(substr($lat,0,2)+ substr($lat,3,2)/60 + substr($lat,6,2)/3660);
          }
          if (substr($lat,8,1) == "N"){
                 $latb=substr($lat,0,2)+ substr($lat,3,2)/60 + substr($lat,6,2)/3660;
          }

          if (substr($lon,9,1) == "W"){
                 $lonb=-1*(substr($lon,0,3)+ substr($lon,4,2)/60 + substr($lon,7,2)/3660);
          }
          if (substr($lon,9,1) == "E"){
                 $lonb=substr($lon,0,3)+ substr($lon,4,2)/60 + substr($lon,7,2)/3660;
          }
          //printf ("Time=%s, LAT=%s, LON=%s\n", $time, $latb, $lonb);

          // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
          $time=time();

          if ( $latb != 0 && $lonb != 0 ) {

            $vent = windAtPosition($latb*1000, $lonb*1000, 0, 'OLD' ) ;
            $query ="insert into positions values ";
            $query .= "( $time , $lonb*1000, $latb*1000,  " . array_search($nom, $boats). ", $IDRACES, '" . round($vent['speed'],1) . "," . round(($vent['windangle']+180)%360) . "') ;";

            mysql_query($query) or die("VLM70 : Query failed : " . mysql_error." ".$query);

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
