<?php

    include_once("config.php");
    include_once("f_windAtPosition.php");

    $filename="http://banquepopulaire.geovoile.fr/julesverne/2011/positions.asp?lg=fr";

    if ($fd = fopen ($filename, "r")) {
        while (!feof ($fd)) {
            $buffer = fgets($fd, 4096);
            // Si le bloc contient "trLigne".. on regarde ce qui suit : les 3 lignes suivantes sont int�ressantes
            if ( ereg('trLigne',$buffer) ) {
                // Le timestamp
                $buffer= fgets($fd , 4096);
                $ligne = preg_split ("/[<>]/",$buffer);
                $time = str_replace('/', '-', $ligne[2]);
                //print "*$time*\n";
                $time= strtotime($time);

                $buffer= fgets($fd , 4096);
                $ligne = preg_split ("/[<>]/",$buffer);

                $lat = preg_split("/[�, ']/",$ligne[2]);

                if ($lat[3] == "S"){
                   $latb=-1*($lat[0]+ $lat[1]/60 + $lat[2]/3600);
                }
                if ($lat[3] == "N"){
                   $latb=$lat[0]+ $lat[1]/60 + $lat[2]/3600;
                }

                $buffer= fgets($fd , 4096);
                $ligne = preg_split ("/[<>]/",$buffer);
                $lon = preg_split("/[�, ']/",$ligne[2]);

                if ($lon[3] == "W"){
                    $lonb=-1*($lon[0]+ $lon[1]/60 + $lon[2]/3600);
                }
                if ($lon[3] == "E"){
                    $lonb=$lon[0]+ $lon[1]/60 + $lon[2]/3600;
                }
     
                // Colecte cap
                $buffer= fgets($fd , 4096);
                $ligne = preg_split ("/[<>]/",$buffer);
                $fields = preg_split("/[�]/",$ligne[2]);
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
    //print strftime("%H:%M ", $time); print "LAT: "; print_r($lat); print "LON: "; print_r( $lon); print "\n";
    // On n'utilise pas le timestamp disponible dans l'URL, mais l'heure de prise en compte
    if ($time < 1) $time=time();

    if ( $latb != 0 && $lonb != 0 ) {

        //     $query  ="delete from positions where idusers=-5 and time < ($time - 86400) ;" ;
        //     mysql_query($query) or die("Query failed : " . mysql_error." ".$query);

        $query ="REPLACE into positions values ";
        $query .= "( $time , $lonb*1000, $latb*1000, -5, 1181 ) ;";

        mysql_query($query) or die("BP5POS : Query failed : " . mysql_error." ".$query);
        //echo "$query\n";

        // Collecte pour future polaire BP5v3
        $fhandle=fopen (VLMTEMP . "/collecte-bp5-2011.txt" , "a");
        $vent = windAtPosition($latb*1000, $lonb*1000, 0 ) ;
        $WS=round($vent['speed'],1);
        $WD=round(($vent['windangle']+180)%360);
     
        fputs( $fhandle, $time . ";" . $lonb*1000 . ";". $latb*1000 . ";" . $bs . ";" . $cap . ";" . $WS . ";" . $WD  . "\n");
     
        fclose ($fhandle);
        mysql_close($link);
     
        //printf ("Time=%s, LAT=%s, LON=%s, BS=%s, HDG=%s, WS=%s, WD=%s\n", $time, $latb, $lonb, $bs, $cap, $WS, $WD);
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
