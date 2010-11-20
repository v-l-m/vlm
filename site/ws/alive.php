<?php
  //smallest possible code
  function die503($msg) {
        header("HTTP/1.1 503 Service Temporarily Unavailable");
        header("Status: 503 Service Temporarily Unavailable");
        die($msg);
  }
  function define_if_not($k, $v) {
      if (!defined($k)) define($k, $v);
  }

  @include_once('param.php');
  $link = @mysql_connect(DBSLAVESERVER, DBSLAVEUSER, DBSLAVEPASSWORD) or die503("Could not connect : " . mysql_error());
  @mysql_select_db(DBNAME, $link) or die503("Could not select VLM database");
  @mysql_query("SELECT count(*) from players", $link) or die503("KO");   //smallest table is players
  @mysql_close($link);
  echo "OK";
?>
