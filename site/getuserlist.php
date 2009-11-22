<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

  $idr=htmlentities(quote_smart($_REQUEST['idr']));
  if (  strlen($idr) == 0 ) {
     echo "Usage : http://virtual-loup-de-mer.org/getuserlist.php?idr=X\n";
     echo "\nX = numero de la course, 0 pour toute course confondue";
     exit;
  }

  $query   = "SELECT idusers, username, boatname, country, ";

  if ( $idr == 0 ) {
       $query .= ", engaged ";
  }

  $query  .= " FROM users WHERE idusers >= 0 ";

  if ( $idr != 0 ) {
       $query .= " AND engaged = $idr " ;
  }

  $query .=  " ORDER BY idusers";

  $result  = mysql_query($query) or die("Query [$query] failed \n");
  printf ("============================\n");
  printf ("====  %5d players  ====\n", mysql_num_rows($result));
  printf ("============================\n");
  printf ("Id;usernane;boatname;country;ipaddr;race if not given\n") ;


  while(  $row = mysql_fetch_array($result, MYSQL_NUM) ) {

     printf ("\n%s;%s;%s;%s", $row[0],$row[1],$row[2],$row[3]) ;
     if ( $idr == 0 ) {
            printf (";%d", $row[4]);
     }
  
  }
?>
  
