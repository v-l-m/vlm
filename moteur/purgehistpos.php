<?php
include_once("config.php");
include_once('functions.php');

header("content-type: text/plain; charset=UTF-8");

$PURGE_HORIZON_YEARS = 10;
$PurgeEndEpoch = time() - $PURGE_HORIZON_YEARS*365*24*3600;

$StartTick = microtime(true);
print("======================================================\n");
print("HistPos purge started....".$StartTick."\n");
print("Purging races completed until ".date('r', $PurgeEndEpoch))." (".$PurgeEndEpoch.")\n";

$query="select idraces from races , (select distinct race from (select distinct idusers,race from histpos where idusers > 0) T) T2 ";
$query .= "where races.idraces = T2.race and closetime < ".$PurgeEndEpoch.";";

$races=[];
$res = wrapper_mysql_db_query_reader($query) or die("Query [".$query."] failed \n");
while ($row = mysqli_fetch_assoc($res) ) array_push( $races,$row["idraces"]);

$TotalDeletedRows = 0;
foreach ($races as $race)
{
  print("processing race ".$race."\n");

  // Save race tracks in zipped file.
  

  // Get user list from rankings (until index are back available)
  $query="select distinct idusers from histpos where race=".$race.";";
  $ulist=[];
  $res = wrapper_mysql_db_query_reader($query) or die("Query [".$query."] failed \n");
  while ($row = mysqli_fetch_assoc($res) ) array_push($ulist,$row["idusers"]);

  // delete user positions from race
  $RaceDeletedRows=0;
  foreach($ulist as $user)
  {
    $q2="delete from histpos where idusers=".$user." and race=".$race.";";
    //print_r($q2,true);
    $res = wrapper_mysql_db_query_reader($q2) or die("Delete Query Failed ".$q2."\n");
    //print_r($res,true);
    $DeletedRows = mysqli_affected_rows($GLOBALS['masterdblink']);
    if ($DeletedRows)
    {
      print("\t user ".$user. " deleted ".mysql_affected_rows()." positions\n");
      $RaceDeletedRows += $DeletedRows;
    }
  }

  $TotalDeletedRows += $RaceDeletedRows;

}

print ("Complete after deleting ".$TotalDeletedRows." in " .(microtime(true)-$StartTick)."\n");


?>
  
