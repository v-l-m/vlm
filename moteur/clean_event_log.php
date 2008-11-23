<?php
//////////////RACES CLEANING //////////////
//get races

// Cleaning Races_results only 
   $query = " DELETE from user_action
              WHERE time < " . (time()-86400)  ;
   $result = mysql_db_query(DBNAME,$query);


?>
