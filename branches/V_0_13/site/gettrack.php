<?php
    include_once("config.php");

    header("content-type: text/plain; charset=UTF-8");

    $idu=htmlentities(quote_smart($_REQUEST['idu']));
    $idr=htmlentities(quote_smart($_REQUEST['idr']));
    $all=array_key_exists('all', $_REQUEST);

    if (  round($idr) == 0 || round($idu) == 0 
          || (strspn($idu, "0123456789") != strlen($idu)) 
          || (strspn($idr, "0123456789") != strlen($idr)) ){
        echo "Usage : http://virtual-loup-de-mer.org/gettrack.php?idu=X&idr=Y\n";
        echo "\nX = numero de votre bateau";
        echo "\nY = numero de la course";
        exit();
    }

    /* La table :
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

    $query_race = "SELECT deptime FROM races WHERE idraces = ".round($idr);
    $result = wrapper_mysql_db_query_reader($query_race) or die("Query [$query_race] failed \n");
    if ($row = mysql_fetch_assoc($result)) {
        $deptime = $row['deptime'];
    } else {
        die("No such race : $idr\n");
    }

    $query =  "SELECT histpos.time,histpos.lat,histpos.long FROM histpos" .
              " WHERE histpos.idusers=" . round($idu) . 
              " AND histpos.race=" . round($idr) . 
              " AND histpos.time >= ".$deptime.
              " ORDER BY time ASC";

    $result = wrapper_mysql_db_query_reader($query) or die("Query [$query] failed \n");
    $nbresults =  mysql_num_rows($result);

    if ($all) {
        $querynow = "SELECT positions.time,positions.lat,positions.long FROM positions" .
                    " WHERE positions.idusers=" . round($idu) .
                    " AND positions.race=" . round($idr) .
                    " AND positions.time >= $deptime".
                    " ORDER BY time ASC";
        $resultnow  = wrapper_mysql_db_query_reader($querynow) or die("Query [$querynow] failed \n");
        $nbresults  += mysql_num_rows($resultnow);
    }

    printf ("============================\n");
    printf ("====  %6d positions  ====\n", $nbresults);
    printf ("============================\n");
    printf ("Timestamp;latitude;longitude\n") ;
    printf ("============================\n");

    while(  $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
        printf ("%d;%5.6f;%5.6f\n", $row['time'],$row['lat']/1000,$row['long']/1000) ;
    }

    if ($all) {
        while(  $row = mysql_fetch_array($resultnow, MYSQL_ASSOC) ) {
            printf ("%d;%5.6f;%5.6f\n", $row['time'],$row['lat']/1000,$row['long']/1000) ;
        }
    }

?>
