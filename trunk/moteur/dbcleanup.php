<?php
include_once("vlmc.php");
include_once("functions.php");

define('MOTEUR','Yes');

/**
 * This script is in charge of the cleanup of position tables in VLM
 * launched every X minutes (like 5mn) 
*/
header("Cache-Control: no-store, no-cache\n\n");
header("Content-Type: text/plain\n\n");

include("config.php");

//$verbose=$_REQUEST['verbose'];
$verbose=0;

// Test des arguments
$RACE_NUM=0;
$USER_NUM=0;
$flagglobal=true;
// Si on a un argument, c'est le numéro d'une course
// Si on en a 2, c'est la course PUIS le numéro du bateau
if ( $argc > 1 ) {
  $RACE_NUM=$argv[1];
  $flagglobal=false;
}
if ( $argc > 2 ) {
  $USER_NUM=$argv[2];
}

$engine_start_float=microtime(true);

// Purge des anciennes positions (on ne garde une trace que sur MAX_POSITION_AGE)
////////////////////////////////////////CHECK IF SOMEONE END RACE
echo "\n1- === PURGE OLD POSITIONS AND CREATE TEMP TABLES\n";
$queryhistopositions = "INSERT INTO histpos SELECT * FROM positions WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = wrapper_mysql_db_query($queryhistopositions);

$querypurgepositions = "DELETE FROM positions WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = wrapper_mysql_db_query($querypurgepositions);

$querypurgeupdates = "DELETE FROM updates WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = wrapper_mysql_db_query($querypurgeupdates);


//echo "\n".$querypurgepositions;
$step_stop_float=microtime(true);
echo "\n  TIMINGS: duration step 1 - ".($step_stop_float-$engine_start_float).
     "\n";

echo "\n3- === CHECKING FOR GARBAGE IN DATABASE\n";
include "clean_garbage_races.php";
include "clean_event_log.php";

$next_step_stop_float=microtime(true);
echo "\n  TIMINGS: duration step 3 - ".($next_step_stop_float-$step_stop_float).
     "\n";
$step_stop_float=$next_step_stop_float;

?>
