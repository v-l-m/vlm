<?php
//////////////RACES CLEANING //////////////
//get races

// Cleaning Races_results only 
   $query = " DELETE from user_action
              WHERE time < " . (time()-604800)  ;
   $result = mysql_db_query(DBNAME,$query);


?>
