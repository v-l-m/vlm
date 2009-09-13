<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

if ( $argc == 1 ) {
     $RACE_NUM=$argv[1];
} else {
     $RACE_NUM=0;
}

// On cherche des couples IDRACES, IDUSERS dans races_ranking
// Pour chaque couple, on purge les positions

// Si pas de course précisée, on purge toutes les courses terminées
if ( $RACE_NUM != 0 ) {
     $query = " select idraces from races where started = -1 order by idraces";
     $result  = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
} 


// La requête : 
// select * from histpos where idusers=$idu and race=$idr order by time asc

  $query   =  "select * from histpos " .
              " where idusers=" . round($idu) . 
              "   and race=" . round($RACE_NUM) . 
              " order by time asc";

  $result  = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");

  printf ("============================\n");
  printf ("====  %6d positions  ====\n", mysql_num_rows($result));
  printf ("============================\n");
  printf ("Timestamp;latitude;longitude\n") ;
  printf ("============================\n");

  while(  $row = mysql_fetch_array($result, MYSQL_NUM) ) {

     printf ("%d;%5.6f;%5.6f\n", $row[0],$row[2]/1000,$row[1]/1000) ;
  
  }
?>
  
