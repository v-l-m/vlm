<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

  $idu=htmlentities(quote_smart($_REQUEST['idu']));
  $idr=htmlentities(quote_smart($_REQUEST['idr']));
  if (  round($idr) == 0 || round($idu) == 0 ) {
     echo "usage : http://virtual-loup-de-mer.org/gettrack.php?idu=X&idr=Y\n";
     echo "\nX = numero de votre bateau";
     echo "\nY = numero de la course";
     exit;
  }

  // La table :
  /*
+---------+------------+------+-----+---------+-------+
| Field   | Type       | Null | Key | Default | Extra |
+---------+------------+------+-----+---------+-------+
| time    | bigint(20) | YES  | MUL | NULL    |       |
| long    | double     | YES  |     | NULL    |       |
| lat     | double     | YES  |     | NULL    |       |
| idusers | int(11)    | NO   | MUL | 0       |       |
| race    | int(11)    | YES  | MUL | NULL    |       |
| wind    | text       | YES  |     | NULL    |       |
+---------+------------+------+-----+---------+-------+
  */
  // La requête : 
  // select * from histpos where idusers=$idu and race=$idr order by time asc

  $query   =  "select deptime from races " .
              " where race=" . round($idr);

  $result  = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
  $row = mysql_fetch_array($result, MYSQL_NUM)
  $deptime = $row[0]; // départ

  $query   =  "select * from histpos " .
              " where idusers=" . round($idu) . 
              "   and race=" . round($idr) .
              "   and time >= ". $deptime . 
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
  
