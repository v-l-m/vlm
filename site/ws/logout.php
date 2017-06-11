<?php
include_once("config.php");
include_once("functions.php");
//include_once("wslib.php");

header("content-type: text/plain; charset=UTF-8");

// now start the real work
logout();
if (true) {
    $fmt = "json" ;  // deprecate text as of today get_requested_output_format();

    switch ($fmt) {
    case "json":
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["Value"=>"OK","success"=>"true"]);
        break;
    case "text":
    default:
        header("Content-Type: text/plain; charset=UTF-8");
        echo "OK";
    }
}

?>

