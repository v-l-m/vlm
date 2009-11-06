<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

  $idr=htmlentities(quote_smart($_REQUEST['idr']));
  if (  strlen($idr) == 0 ) {
     echo "usage : http://virtual-loup-de-mer.org/getuserlist.php?idr=X\n";
     echo "\nX = numero de la course, 0 pour toute course confondue";
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


  $query   = " select idusers, username, boatname, country, ipaddr , email ";

  if ( $idr == 0 ) {
       $query .= ", engaged ";
  }

  $query  .= " from users where idusers >= 0 ";

  if ( $idr != 0 ) {
       $query .= " and engaged = $idr " ;
  }

  $query .=  " order by  idusers";

  $result  = mysql_query($query) or die("Query [$query] failed \n");
  printf ("============================\n");
  printf ("====  %5d players  ====\n", mysql_num_rows($result));
  printf ("============================\n");
  printf ("Id;usernane;boatname;country;ipaddr;race if not given\n") ;


  while(  $row = mysql_fetch_array($result, MYSQL_NUM) ) {

     printf ("\n%s;%s;%s;%s;%s;%s", $row[0],$row[1],$row[2],$row[3],$row[4], $row[5]) ;
     if ( $idr == 0 ) {
            printf (";%d", $row[6]);
     }
  
  }
?>
  
