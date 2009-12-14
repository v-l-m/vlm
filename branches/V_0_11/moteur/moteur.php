<?php
include_once("vlmc.php");
include_once("functions.php");

define('MOTEUR','Yes');

/**
 * This script is the engine of VLM
 * launched every vacation (like 5mn), it will make a number of thing that will
 * make the game alive :
 * - check if the races should start and start them if necessary
 * - move the boats, create new positions
 * - change boats heading if necessary (autopilots)
 * - check if someone crossed the coast
 * - check if someone has won the race
 * - write a new value in the "update" table
*/
header("Cache-Control: no-store, no-cache, must-revalidate\n\n");
header("Content-Type: text/plain\n\n");

include("config.php");

$global_vlmc_context = new vlmc_context();
init_context($global_vlmc_context);
set_gshhs_filename($global_vlmc_context, GSHHS_FILENAME);
set_polar_definition_filename($global_vlmc_context, POLAR_DEFINITION_FILENAME);
global_vlmc_context_set($global_vlmc_context);

init_coastline();

$polar_shmid = get_polar_shmid(1);
if ( $polar_shmid < 0 ) { // safeline
  init_polar();
  create_and_fill_polar_shm();
} else {
  shm_lock_sem_construct_polar(1);  
}

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


$engine_start=time();
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

// ========================================
echo "\n2- === DO THE JOB FOR EACH RACE\n";

// Compteurs
$nb_boats=0;
$nb_races=0;
$update_races = " " ;

$racesListObj = new startedRacesList();
//echo date ('i') . "\n";
//print_r($racesListObj);

//for every race
foreach($racesListObj->records as $idraces) {

  $update_races .= $idraces . " " ;
  if (    ( $RACE_NUM != 0 && $idraces == $RACE_NUM ) || ( $RACE_NUM == 0)   ) {

        $fullRacesObj = new fullRaces( $idraces )  ;
        // Check only the race given in first arg if one is given, else check all races
  include "check_race.php";
  $nb_races++;

  }

} // Foreach race

$next_step_stop_float=microtime(true);
$step2_elapsed_float=$next_step_stop_float-$step_stop_float;
echo "\n  TIMINGS: duration step 2 - ".($step2_elapsed_float)."\n";
$step_stop_float=$next_step_stop_float;

//////////// CLEANING GARBAGES RACES 
//     (Race ended, but some boats still engaged on it...)
//     (Race ended, but races_ranking having lines for this race ...)
echo "\n3- === CHECKING FOR GARBAGE IN DATABASE\n";
include "clean_garbage_races.php";
include "clean_event_log.php";

$next_step_stop_float=microtime(true);
echo "\n  TIMINGS: duration step 3 - ".($next_step_stop_float-$step_stop_float).
     "\n";
$step_stop_float=$next_step_stop_float;

/////////////////////////////WRITE UPDATE DATE IN DATABASE
$engine_stop=time();
$engine_stop_float=microtime(true);
$engine_elapsed_float=$engine_stop_float-$engine_start_float;
if (round($engine_elapsed_float) > 0 ) {
  $engine_elapsed = round($engine_elapsed_float);
} else {
  $engine_elapsed = 1;
}

// Demarrage des courses à démarrer... 
echo "\n4- === CHECKING IF A RACE STARTS\n";
include "start_races.php";

echo "\n5- === TIMESTAMPING : ".gmdate("M d Y H:i:s",time())." (UTC)... ";
// Only if full engine run (not for a "one race or one boat" run) ==> No arg.
if ( $flagglobal == true ) {
     $query5 = "INSERT INTO updates  VALUES (" 
      .time()    . ","
      .$nb_races . ","
      .$nb_boats . ","
      .$engine_elapsed. ","
      ."'" . $update_races ."'"
      . ")";
     echo "writing timestamp...";
     $result5 = wrapper_mysql_db_query($query5); //or die("Query failed : $query5");
}
echo "done\n";
echo "\n\tFINISHED ** Races=" . $nb_races . "( " . $update_races . "), Boats=". $nb_boats . ", ";
echo "Time=" . $engine_elapsed_float . "sec.  rate=". $nb_boats/$engine_elapsed_float . " boats/sec **\n";
echo "  TIMINGS: Time race check=" . $step2_elapsed_float . "sec.  rate=". $nb_boats/$step2_elapsed_float . " boats/sec\n";

?>
