<?php

//include_once "config.php";

$boat_id = array (
          101 => "Estrella Damm Sailing"      ,
          102 => "Groupe Bel"                 ,
          103 => "Mapfre"                     ,
          104 => "Mirabaud"                   ,
          105 => "Virbac-Paprec 3"            ,
          106 => "Foncia"                     ,
          107 => "Neutrogena Formula Noru"    ,
          108 => "Gaes Centros Auditivos"     ,
          109 => "Renault"                    ,
          110 => "Hugo_Boss"                  ,
          111 => "Central Lechera Asturia"    ,
          112 => "We are water"               ,
          113 => "Forum Maritim Catala"       
);

//parse fichier b2b
//define("VLMTEMP", "/home/vlm/tmp");
define("VLMTEMP", "/tmp");
$filename=VLMTEMP."/bwr-ranking.txt";

$time = time();

if ($fd = fopen ($filename, "r")){

  while (!feof ($fd)) {
        $buffer = fgets($fd, 4096);

        // La mise en forme est faite par html2text : toute ligne doit donc être considérée
        if (ereg('^[0-9]+\.',$buffer)){
            // Nom du bateau col 9 à 36

            $boat=str_replace(' ','_',substr($buffer,8,6));
            $idusers=array_search($boat , $boat_id);

            printf ("%s/%s\n", $boat , $idusers);
        
            // Tout ce qui suit nous intéresse
            $ligne = preg_split ("/  */",substr($buffer,36));

            $coord = preg_split ("/\./",$ligne[7]);
            $lonb=$ligne[6]+$coord[0]/60 + $coord[1]/3600;

            $coord = preg_split ("/\./",$ligne[9]);
            $latb=$ligne[8]+$coord[0]/60 + $coord[1]/3600;


            // recherche polarité N/S et W/E : seconde ligne
            $buffer = fgets($fd, 4096);
            if (ereg('^R] +',$buffer)) {
                preg_match_all("/ [NSWE] /",$buffer, $quarts);
           //                print_r($quarts);  //$quarts[0] => W/E   $quarts[1] => N/S

               if ( strcmp($quarts[0][0], 'W') ) {
                   $lonb=-1*$lonb;
               }

               if ( strcmp($quarts[0][1], 'S') ){
                   $latb=-1*$latb;
               }
            }


           // Enregistrement dans la base
//           echo "Bateau $idusers / pos(lat,lon) : $latb , $lonb<br>\n";
           $query  ="delete from positions where idusers=" . $idusers . ";" ;
//           mysql_query($query) or die("BWR: Query failed : " . mysql_error." ".$query);


           $query ="insert into positions values ";
           $query .= "( $time , $lonb*1000, $latb*1000, $idusers, 20101231) ;";

 //          mysql_query($query) or die("BWR : Query failed : " . mysql_error." ".$query);
           echo "$query\n";
           

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
