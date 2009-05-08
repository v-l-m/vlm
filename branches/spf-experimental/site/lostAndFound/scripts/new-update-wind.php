<?php

include("config.php");

$verbose=1;

// UPDATE WEATHER
echo "\n **************  UPDATE WEATHER ************* \n";

$gribFileObj = new gribFile;
if ( $argv[1] != "-clean" ) {
  $gribFileObj->newstore($argv[1]);
} else {
  $gribFileObj->newclean();
}

?>
