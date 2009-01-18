<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

  $idu=htmlentities(quote_smart($_REQUEST['idu']));
  $idr=htmlentities(quote_smart($_REQUEST['idr']));
if (  round($idr) == 0 || round($idu) == 0 
      || (strspn($idu, "0123456789") != strlen($idu)) 
      || (strspn($idr, "0123456789") != strlen($idr)) ){
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
  // SELECT histpos.* FROM histpos,races WHERE histpos.idusers=$idu AND histpos.race=$idr
  // AND histpos.race=races.idraces AND histpos.time > races.deptime ORDER BY time ASC;

  $query   =  "SELECT histpos.* FROM histpos,races" .
              " WHERE histpos.idusers=" . round($idu) . 
              " AND histpos.race" . round($idr) . 
              " AND histpos.race=races.idraces" .
              " AND histpos.time > races.deptime".
              " ORDER BY time ASC";

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
  
