<?php

/*
This script gives the ortho distance between all the pairs of positions for a boat.

*/
header("Cache-Control: no-store, no-cache, must-revalidate\n\n");
header("Content-Type: text/plain\n\n");

include("config.php");
//$verbose=$_REQUEST['verbose'];
$verbose=0;

// Test des arguments
$RACE_NUM=0;
$USER_NUM=0;
// Si on a un argument, c'est le numéro d'une course
// Si on en a 2, c'est la course PUIS le numéro du bateau
if ( $argc < 2 ) {
  echo "need race  + player args\n";
  exit;
}

$RACE_NUM=$argv[1];
$USER_NUM=$argv[2];

// Purge des anciennes positions (on ne garde une trace que sur MAX_POSITION_AGE)
////////////////////////////////////////CHECK IF SOMEONE END RACE
//echo "\n0- PURGE OLD POSITIONS \n";
$qp = "SELECT `time`,`long`,`lat` FROM histpos WHERE race = $RACE_NUM and idusers=$USER_NUM order by time";
$result = wrapper_mysql_db_query_writer($qp);

$n=0;
$time=0;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $n++;

    // we skip the first timestamp.
    if ( $time != 0 ) {
        $elapsed = $time - $row[0];
        $dist    = ortho($lat, $long, $row[2], $row[1]); 
    if ($elapsed != 0 ) { 
        $speed   = abs(3600 * $dist / $elapsed);
    } else {
        $speed = -1;
    };


        $long=$row[1];
        $lat=$row[2];
    printf ("%s: Long=%f, Lat=%f, Dist=%f, Speed=%f\n", gmdate("Y/m/d H:i:s",$time), $long, $lat, $dist , $speed );
    }
    $time=$row[0];
}
//echo "\n".$querypurgepositions;
echo "N=$n\n";



?>
