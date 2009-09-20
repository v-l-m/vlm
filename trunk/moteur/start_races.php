<?php
///////////////////////////////RACES STARTING
//get races

$query4 = "SELECT idraces , racename FROM races WHERE started = 0";

$result4 = wrapper_mysql_db_query(DBNAME,$query4);

//for every race
while ($row4 = mysql_fetch_array($result4, MYSQL_NUM))
{
  $idraces = $row4[0];
  $racename = $row4[1];
  if ($verbose>0)
     echo "accessing race ".$idraces. " \n";
  $racesObj = new fullRaces($idraces);

  //print_r($racesObj);

  if ($verbose >0)
    echo "race $racename should start at ".gmdate("r",$racesObj->races->deptime)
        ." and it is ".gmdate("r",time())."\n\n";
  if (!$racesObj->races->started) //race has not started
    {
      if ($racesObj->races->deptime <= time())
  {
    //start the race
    // Deleting old results, waypoint_crossing, rankings... updating positions...
    echo "Cleaning Race...";
    $racesObj->cleanRaces();

    echo "Starting Race...";
    $racesObj->startRaces();

          echo "RACE $racename STARTING!!!\n\n";

    //for every boat
    foreach ($racesObj->opponents as $users) {

        // Insert a first position at start point
        $query_positions = "INSERT into positions " . 
                          " ( `time` , `long` , `lat` , `idusers` , `race` ) " .
        " values ( " . 
          $racesObj->races->deptime . ", " .
          $racesObj->races->startlong . ", " .
          $racesObj->races->startlat . ", " .
          $users->idusers . ", " .
          $idraces  . 
        ") ; ";
        if ( ! wrapper_mysql_db_query(DBNAME,$query_positions) ) {
            echo "REQUEST FAILED " . $query_positions . "\n";
        }

        // Update Users to First Waypoint, lastupdate, ...and Loch=0
        $query_users = "UPDATE users " . 
                 "   SET nextwaypoint = 1, " .
           "       userdeptime  = " . $racesObj->races->deptime . ", " .
           "       boattype     = '" . $racesObj->races->boattype . "', " .
           "       lastupdate   = " . $racesObj->races->deptime . ", " .
           "       lastchange   = " . $racesObj->races->deptime . ", " .
           "       loch         = 0" .
                 " WHERE idusers = " . $users->idusers . ";";
        if ( ! wrapper_mysql_db_query(DBNAME,$query_users) ) {
            echo "REQUEST FAILED " . $query_users . "\n";
        }


      }

  }
      //else nothing, race wont start this time :-(
    }

} // for every race
?>
