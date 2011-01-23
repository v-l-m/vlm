<?php

include_once "config.php";

$boat_num = array(-157, -163 , -152 , -150 , -161 , -164 , -158 , -159 , -162 , -160 , -151 , -166 , -153);
$time = time();

//parse fichier b2b
// $filename="http://www.barcelonaworldrace.com/ftp/leaderboard/posmaxseaterre.txt";
$filename="http://www.barcelonaworldrace.org/fr/ranking/";
if ($fd = fopen ($filename, "r")) {
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);
        if (ereg('equipiers',$buffer)) {
           $ligne = preg_split ("/[\/\"]/",$buffer);
    
           // recup numero : avant dernier champ numérique
           $ligne = preg_split("/-/" , $ligne[8]);
           $boat = -1 * $ligne[count($ligne)-2];
        
           // On se positionne juste au dessus de la ligne "longitude"
           while (! ereg('precedent',$buffer) ){
               $buffer = fgets($fd, 4096);
           }
      
           // longitude
           $buffer = fgets($fd, 4096);
           $coord = preg_split("/[<> '\.]/" , $buffer);
           $lonb=$coord[2]+ $coord[3]/60 + $coord[4]/3600;

           // latitude
           $buffer = fgets($fd, 4096);
           $coord = preg_split("/[<> '\.]/" , $buffer);
           $latb=$coord[2]+ $coord[3]/60 + $coord[4]/3600;

           // On recherche les lignes donnant les "polarités"
           while (! ereg('precedent_details.*[WENS]',$buffer) ){
               $buffer = fgets($fd, 4096);
           }

           // signe longitude
           $pol_tab = preg_split("/[<>]/" , $buffer);
           if ( $pol_tab[2] == 'W' ) {
                $lonb = -1 * $lonb;
           }
           
           // signe longitude
           $buffer = fgets($fd, 4096);
           $pol_tab = preg_split("/[<>]/" , $buffer);
           if ( $pol_tab[2] == 'S' ) {
                $latb = -1 * $latb;
           }

//           printf ("Boat=%s, lat=%s, lon=%s\n",  $boat, $latb, $lonb);
           if ( $lonb != 0 && $latb != 0 ) {
                $query ="insert into positions values ";
                $query .= "( $time , $lonb * 1000, $latb * 1000, $boat, 20101231) ;";

                //mysql_query($query) or die("BWR : Query failed : " . mysql_error." ".$query);
                echo "$query\n";
           }


        }
      }
}


?>
