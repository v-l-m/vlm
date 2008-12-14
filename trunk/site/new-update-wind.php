<?php

include("config.php");
//print_r($_SERVER);
//if ( in_array($_SERVER["PHP_SELF"], $engineOnlyScripts ) ) {
//     printf("<H1>You should not do that. This script should not be invoked by a client.</H1>\n");
//     exit;
//}
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
