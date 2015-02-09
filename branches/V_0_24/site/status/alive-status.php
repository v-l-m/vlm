<?php
  //smallest possible code

  @include_once('config-mysql.php');

  @mysql_query("SELECT count(*) from players", $GLOBALS['slavedblink']) or die503("KO");   //smallest table is players
  echo "OK";
?>
