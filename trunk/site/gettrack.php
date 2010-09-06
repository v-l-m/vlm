<?php
    include_once("config.php");

    header("content-type: text/plain; charset=UTF-8");

    $idu = get_cgi_var('idu', 0);
    $idr = get_cgi_var('idr', 0);
    $all = array_key_exists('all', $_REQUEST);

    if (  round($idr) == 0 || round($idu) == 0 
          || (strspn($idu, "0123456789") != strlen($idu)) 
          || (strspn($idr, "0123456789") != strlen($idr)) ){
        echo "Usage : http://virtual-loup-de-mer.org/gettrack.php?idu=X&idr=Y\n";
        echo "\nX = numero de votre bateau";
        echo "\nY = numero de la course";
        echo "\nNote that this service is deprecated. Please use ws/boatinfo/tracks.php";
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

    $now = time();
    
    $users = getUserObject($idu);
    if (is_null($users)) die("No such user/boat : $idu");
    
    if (!raceExists($idr)) die("No such race : $idr\n"); //FIXME : select on races table made two times !
    $races = new races($idr);

    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now

    if ($users->hidepos) die("User/Boat $idu is hidden");
            
    if ($races->bobegin < $now && $races->boend > $now) {
        //BlackOut in place
        $endtime = $races->bobegin;
    }

    if ($all) {
        $pi = new fullPositionsIterator($users->idusers, $races->idraces, $starttime, $endtime);
    } else {
        $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime);
    }

    printf ("============================\n");
    printf ("====  %6d positions  ====\n", count($pi->records));
    printf ("============================\n");
    printf ("Timestamp;latitude;longitude\n") ;
    printf ("============================\n");

    foreach ($pi->records as $row) {
        printf ("%d;%5.6f;%5.6f\n", $row[0],$row[1]/1000,$row[2]/1000) ;
    }


?>
