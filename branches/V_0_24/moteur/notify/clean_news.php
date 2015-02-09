<?php
  include("config.php");
  include("functions.php");

  // Cleaning News_results only 
   $query = " DELETE FROM `news`
              WHERE timetarget < " . (time() - VLM_NOTIFY_NEWS_MAX_AGE)  ;
   $result = wrapper_mysql_db_query_writer($query);

   printf("News deleted as too old: %d\n", mysql_affected_rows());
?>
