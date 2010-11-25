<?php
include_once("vlmc.php");
include_once("functions.php");

define('MOTEUR','Yes');

/**
 * This script is in charge of the cleanup of historic position tables in VLM
 * launched every X minutes (like 1 day) 
*/
include("config.php");

$engine_start=microtime(true);

////////////////////////////////////////CHECK IF SOMEONE END RACE
echo "\n1- === PURGE OLD HISTORIC POSITIONS\n";

$queryracelist = "SELECT MAX(`time`) AS t, race FROM histpos,races WHERE started=".RACE_ENDED." AND race=idraces GROUP BY race";

$result = wrapper_mysql_db_query_writer($queryracelist);

$min_time = time() - RACE_EXPORT_DURATION;
$racelist = array();
while( $row = mysql_fetch_array( $result, MYSQL_ASSOC) ) {
  if ($row['t'] < $min_time) {
    array_push($racelist, $row['race']);
  }
}
foreach ($racelist as $race) {
  echo "\n - Working on race $race\n";
  // FIXME we need the format ready
}

$step_stop=microtime(true);
echo "\n  TIMINGS: duration step 1 - ".($step_stop-$engine_start).
     "\n";

?>
