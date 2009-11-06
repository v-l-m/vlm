<?php

include_once "config.php";

//$boat_num = array("", "-85", "-9", "-99", "-35", "-1876", "-29", "-33", "-4", "-06");
$boat_num= array(
    -25=>'Safran',
    -7=>'Cheminées Poujoulat',
    -14=>'Akena Veranda',
    -101=>'Foncia',
    -80=>'Gitana Eighty',
    -62=>'Brit\'Air',
    -3=>'Ecover',
    -22=>'Generali',
    -84=>'Spirit of Canada',
    -50=>'Great American III',
    -64=>'Roxy',
    -1=>'Cervin EnR',
    -111=>'Aviva',
    -8=>'Maisonneuve',
    -360=>'Groupe Bel'
  );



$filename="http://netandsea.dnsalias.com/transatbtob/classement.php";
$class = 0;
$tab_i=0;
$tab = "non";

if ($fd = fopen ($filename, "r")){
  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);
        if (ereg('</tr>',$buffer) && (($tab == "ok")|| ($tab == "nl"))){
          $tab = "non";
          $tab_i=0;
        }
        if ($tab == "ok"){
          $ligne = preg_split ("/[<>]/",$buffer);
          $tab_i++;
          if ($tab_i ==1){
              $name[$class]=$ligne[2];
          }
          if ($tab_i ==2){
            $lat = preg_split("/[&;.\']/",$ligne[2]);
              if ($lat[6] == "S"){
                 $latb[$class]=-1*($lat[0]+ $lat[2]/60 + $lat[3]/3600);
              }
              if ($lat[6] == "N"){
                 $latb[$class]=$lat[0]+ $lat[2]/60 + $lat[3]/3600;
              }
          }
          if ($tab_i ==3){
            $lon = preg_split("/[&;.\']/",$ligne[2]);
              if ($lon[6] == "W"){
                 $lonb[$class]=-1*($lon[0]+ $lon[2]/60 + $lon[3]/3600);
              }
              if ($lon[6] == "E"){
                 $lonb[$class]=$lon[0]+ $lon[2]/60 + $lon[3]/3600;
              }
          }
        }

        if ($tab == "nl"){
          $ligne = preg_split ("/[<>]/",$buffer);
          $tab_i++;
          if ($tab_i ==1){
              $name[$class]=$ligne[2];
              $latb[$class]="NL";
              $lonb[$class]="NL";
          }
        }

        if (ereg('<tr><td class="classementval">',$buffer)){
          $class ++;
          $tab = "ok";
        }
        if (ereg('<tr><td class="classementval">NL',$buffer)){
          $tab = "nl";
        }
  }
}

$time=time();
for ($i = 1; $i<16; $i++){
  //echo "$name[$i]:".array_search($name[$i], $boat_num).":  pos(lat,lon) : $latb[$i] , $lonb[$i]<br>\n";
    
  if ( $latb[$i] != 0 && $latb[$i] != "NL" 
    && $lonb[$i] != 0 && $lonb[$i] != "NL" 
    && array_search($name[$i], $boat_num) )  {

     //echo "Bateau $boat_num[$i] - classement $class[$i] / pos(lat,lon) : $latb[$i] , $lonb[$i]<br>\n";
     $query  ="delete from positions where idusers=" . array_search($name[$i], $boat_num) . ";" ;
     mysql_query($query) or die("BTOB:Query failed : " . mysql_error." ".$query);

     $query ="insert into positions values ";
     $query .= "( $time , $lonb[$i]*1000, $latb[$i]*1000, " . array_search($name[$i], $boat_num) . ", 20071111) ;";

     mysql_query($query) or die("BTOB : Query failed : " . mysql_error." ".$query);
     //echo "$query\n";
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
