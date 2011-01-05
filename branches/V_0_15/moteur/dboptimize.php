<?php
include_once("vlmc.php");
include_once("functions.php");

define('MOTEUR','Yes');

/**
 * This script is in charge of optimizing tables in VLM
 * launched every day or every week 
*/
include("config.php");

$verbose=0;

$engine_start=microtime(true);

echo "\n1- === OPTIMIZE TABLES ===\n";
// we avoid coastline_* and histpos
$alltables = array("admin_changelog", "auto_pilot", "flags", "modules_status",
		   "players", "players_pending", "playerstousers",
		   "positions", "races", "races_instructions", "races_ranking",
		   "races_results", "races_waypoints", "racesmap", "updates",
		   "user_action", "user_prefs", "users", 
		   "waypoints", "waypoints_crossing");

foreach ($alltables as $table) {
  $opt_start = microtime(true); 
  $cmd    = "OPTIMIZE TABLE $table";
  $result = wrapper_mysql_db_query_writer($cmd);
  $opt_end   = microtime(true);
  echo "Optimized table $table in ".($opt_end - $opt_start)."s\n";
}
$engine_end=microtime(1);
echo "\n  TIMINGS: total execution time - ".($engine_end-$engine_start).
     "\n";
?>
