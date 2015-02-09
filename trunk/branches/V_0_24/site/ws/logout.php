<?php
include_once("config.php");
include_once("wslib.php");

header("content-type: text/plain; charset=UTF-8");

// now start the real work
if (logout_if_not()) {
    $fmt = get_requested_output_format();

    switch ($fmt) {
    case "json":
        header("Content-Type: text/plain; charset=UTF-8");
        echo json_encode("OK");
        break;
    case "text":
    default:
        header("Content-Type: text/plain; charset=UTF-8");
        echo "OK";
    }
}

?>

