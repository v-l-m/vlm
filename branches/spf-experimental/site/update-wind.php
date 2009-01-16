<?php

include("config.php");
$verbose=1;

// UPDATE WEATHER
echo "\n **************  UPDATE WEATHER ************* \n";

$gribFileObj = new gribFile;
if ( $argv[1] != "-clean" ) {
    $gribFileObj->store($argv[1]);
    $gribFileObj->zerowind(85);
} else {
    $gribFileObj->clean();
}

?>
