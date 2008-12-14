<?php

/*
This script is the engine of VLM
launched every 3 hours, it will make a number of thing that will
make the game alive :
- check if the races should start and start them if necessary
- move the boats, create new positions
- change boats heading if necessary (autopilots)
- check if someone has won the race
- update weather
- write a new value in the "update" table
*/
header("Cache-Control: no-store, no-cache, must-revalidate\n\n");
header("Content-Type: text/plain\n\n");

include("config.php");
//$verbose=$_REQUEST['verbose'];
$verbose=0;

// Test des arguments
$RACE_NUM=0;
$USER_NUM=0;
$flagglobal=true;
// Si on a un argument, c'est le num�ro d'une course
// Si on en a 2, c'est la course PUIS le num�ro du bateau
if ( $argc > 1 ) {
	$RACE_NUM=$argv[1];
        $flagglobal=false;
}
if ( $argc > 2 ) {
	$USER_NUM=$argv[2];
}


$engine_start=time();
// Purge des anciennes positions (on ne garde une trace que sur MAX_POSITION_AGE)
////////////////////////////////////////CHECK IF SOMEONE END RACE
echo "\n1- === PURGE OLD POSITIONS AND CREATE TEMP TABLES\n";
$queryhistopositions = "INSERT INTO histpos SELECT * FROM positions WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = mysql_db_query(DBNAME,$queryhistopositions);

$querypurgepositions = "DELETE FROM positions WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = mysql_db_query(DBNAME,$querypurgepositions);

$querypurgeupdates = "DELETE FROM updates WHERE time < " . ($engine_start - MAX_POSITION_AGE) .";";
$result = mysql_db_query(DBNAME,$querypurgeupdates);


//echo "\n".$querypurgepositions;

// Creates the temporary table for tmpcoastline
$querydroptemp = "DROP TABLE IF EXISTS temporary.tmpcoastline;";
$result = mysql_db_query('temporary',$querydroptemp) or die ("PB drop tmpcoastline");

$querycreatetemp = "CREATE TEMPORARY TABLE temporary.tmpcoastline (
                `idpoint` int(11) NOT NULL auto_increment,
                `idcoast` int(11) NOT NULL default '0',
                `longitude` double NOT NULL default '0',
                `latitude`  double NOT NULL default '0',
                        PRIMARY KEY  (`idpoint`),
                        KEY `latitude` (`latitude`),
                        KEY `longitude` (`longitude`),
                        KEY `idcoast` (`idcoast`)
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='tmpcoastline for crossing verification';";
$result = mysql_db_query('temporary',$querycreatetemp) or die ("PB create tmpcoastline");
//echo "\n".$querycreatetemp;


// ========================================
echo "\n2- === DO THE JOB FOR EACH RACE\n";

// Compteurs
$nb_boats=0;
$nb_races=0;

$racesListObj = new startedRacesList();
//print_r($racesListObj);

//for every race
foreach($racesListObj->records as $idraces) {

  if (    ( $RACE_NUM != 0 && $idraces == $RACE_NUM ) || ( $RACE_NUM == 0)   ) {

        $fullRacesObj = new fullRaces( $idraces )  ;
        // Check only the race given in first arg if one is given, else check all races
	include "check_race.php";
	$nb_races++;

  }

} // Foreach race


//////////// CLEANING GARBAGES RACES 
//     (Race ended, but some boats still engaged on it...)
//     (Race ended, but races_ranking having lines for this race ...)
echo "\n3- === CHECKING FOR GARBAGE IN DATABASE\n";
include "clean_garbage_races.php";
include "clean_event_log.php";

/////////////////////////////WRITE UPDATE DATE IN DATABASE
$engine_stop=time();
$engine_elapsed=1+$engine_stop - $engine_start;

// Demarrage des courses � d�marrer... 
echo "\n4- === CHECKING IF A RACE STARTS\n";
include "start_races.php";

echo "\n5- === TIMESTAMPING : ".gmdate("M d Y H:i:s",time())." (UTC)... ";
// Only if full engine run (not for a "one race or one boat" run) ==> No arg.
if ( $flagglobal == true ) {
     $query5 = "INSERT INTO updates  VALUES (" 
		  .time()    . ","
		  .$nb_races . ","
		  .$nb_boats . ","
		  .$engine_elapsed
		  . ")";
     echo "writing timestamp...";
     $result5 = mysql_db_query(DBNAME,$query5);//  or echo("Query failed : " . mysql_error." ".$query5);
}
echo "done\n";
echo "\n\tFINISHED ** Races=" . $nb_races . ", Boats=". $nb_boats . ", ";
echo "Time=" . $engine_elapsed . "sec.  rate=". $nb_boats/$engine_elapsed . " boats/sec **\n";

?>
