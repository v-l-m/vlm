<?php
include_once("functions.php");
//////////////RACES CLEANING //////////////
//get races
$raceList=array();

// 1 - DROP Players engaged in a finished race
$query = "SELECT distinct idraces FROM races_ranking 
            WHERE idraces in ( select idraces from races where started = -1 ) ";

$result = wrapper_mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
//for every race
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      echo "Finished Race " . $row[0] . "\n";
      array_push($raceList, $row[0]);
}

foreach ( $raceList as $idraces ) {

      // Cleaning races_ranking
      $query = " DELETE from races_ranking 
                  WHERE idraces = " . $idraces;
      $result2 = wrapper_mysql_db_query(DBNAME,$query);

      // Unsubscribing users
      $query = " UPDATE users SET engaged=0
                  WHERE engaged = " . $idraces;
      $result3 = wrapper_mysql_db_query(DBNAME,$query);

}


// 2 - drop Players ABD when a race is not yes started
$query = " select idraces from races where started = 0 ";

$result = wrapper_mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
//for every race
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      echo "Not Started Race " . $row[0] . "\n";
      array_push($raceList, $row[0]);
}

foreach ( $raceList as $idraces ) {

      // Cleaning Races_results only 
      $query = " DELETE from races_results
                  WHERE idraces = " . $idraces;
      $result = wrapper_mysql_db_query(DBNAME,$query);

}


?>
