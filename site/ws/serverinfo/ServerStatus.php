<?php
  include_once("config-defines.php");

  header('Content-type: application/json; charset=UTF-8');
  header("Cache-Control: max-age=60, must-revalidate");

  include(CACHE_DIRECTORY."/SrvrStats.json");
?>