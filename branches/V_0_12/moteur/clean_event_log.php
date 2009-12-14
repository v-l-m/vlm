<?php
//////////////RACES CLEANING //////////////
//get races

// Cleaning Races_results only 
   $query = " DELETE FROM `user_action`
              WHERE UNIX_TIMESTAMP(`time`) < " . (time()-MAX_LOG_USER_ACTION_AGE)  ;
   $result = wrapper_mysql_db_query_writer($query);


?>
