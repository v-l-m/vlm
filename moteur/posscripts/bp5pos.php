<?php

include_once "config.php";
include_once('f_windAtPosition.php');


$boat_num= array(
    -5=>'BP5',
  );


//$filename="http://trimaran-idec.geovoile.com/tourdumonde2007/positions.asp";
//$filename="http://cammas-groupama.geovoile.com/julesverne/positions.asp?lg=fr";
$filename="http://banquepopulaire.geovoile.fr/julesverne/2010/positions.asp?lg=fr";

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

          $lat = preg_split("/[°, ']/",$ligne[2]);
//    print_r($lat);
          if ($lat[3] == "S"){
                 $latb=-1*($lat[0]+ $lat[1]/60 + $lat[2]/3600);
          }
          if ($lat[3] == "N"){
                 $latb=$lat[0]+ $lat[1]/60 + $lat[2]/3600;
          }

    $buffer= fgets($fd , 4096);
          $ligne = preg_split ("/[<>]/",$buffer);
          $lon = preg_split("/[°, ']/",$ligne[2]);
//    print_r($lon);

          if ($lon[3] == "W"){
                 $lonb=-1*($lon[0]+ $lon[1]/60 + $lon[2]/3600);
          }
          if ($lon[3] == "E"){
                 $lonb=$lon[0]+ $lon[1]/60 + $lon[2]/3600;
          }

 
    // Colecte cap
    $buffer= fgets($fd , 4096);
          $ligne = preg_split ("/[<>]/",$buffer);
          $fields = preg_split("/[°]/",$ligne[2]);
          $cap = $fields[0];
 
    // Collecte BS
    $buffer= fgets($fd , 4096); // DTF
    $buffer= fgets($fd , 4096); // AVANCE
    $buffer= fgets($fd , 4096); // VMG
    $buffer= fgets($fd , 4096); // BS
          $ligne = preg_split ("/[<>]/",$buffer);
          $fields = preg_split("/[ ]/",$ligne[2]);
          $bs = $fields[0];

    break;
        }

  }
}

  // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
  $time=time();

  if ( $latb != 0 
    && $lonb != 0 ) {

     $query  ="delete from positions where idusers=-5 and time < $time - 86400;" ;
     mysql_query($query) or die("Query failed : " . mysql_error." ".$query);

     $query ="insert into positions values ";
     $query .= "( $time , $lonb*1000, $latb*1000, -5, 81 ) ;";

     mysql_query($query) or die("BP5POS : Query failed : " . mysql_error." ".$query);
     //echo "$query\n";

     // Collecte pour future polaire BP5v2
     $fhandle=fopen (VLMTEMP . "/collecte-bp5.txt" , "a");
     $vent = windAtPosition($latb*1000, $lonb*1000, 0 ) ;
     $WS=round($vent['speed'],1);
     $WD=round(($vent['windangle']+180)%360);
 
     fputs( $fhandle, $time . ";" . $lonb*1000 . ";". $latb*1000 . ";" . $bs . ";" . $cap . ";" . $WS . ";" . $WD  . "\n");
 
     fclose ($fhandle);
 
     printf ("Time=%s, LAT=%s, LON=%s, BS=%s, HDG=%s, WS=%s, WD=%s\n", $time, $latb, $lonb, $bs, $cap, $WS, $WD);
 


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
