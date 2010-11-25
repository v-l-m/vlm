<?php
    function headeranddie($h) {
        header("Cache-Control: no-cache"); // no cache for dummy answer
        header("Status: 302 Moved Temporarily", false, 302);
        header("Location: $h");
        exit();
    }


    //Check that the want to catch this url
    if (preg_match("/\/cache\/(.*?)\/(.+)/", $_SERVER['REQUEST_URI'], $matches)) {
        include_once ("config.php");
        include_once ("functions.php");

        switch($matches[1]) {
            case "legacytiles" :
                if (preg_match("/(-?\d+)\/(-?\d+)\/(-?\d+)\.png/", $matches[2], $components)) {
                    headeranddie(sprintf("/tileslegacy.img.php?z=%d&amp;x=%d&amp;y=%d", $components[1], $components[2], $components[3]));
                }
            break;
            case "gshhstiles" :
                if (preg_match("/(-?\d+)\/(-?\d+)\/(-?\d+)\.png/", $matches[2], $components)) {
                    headeranddie(sprintf("/gshhstiles.php?z=%d&x=%d&y=%d", $components[1], $components[2], $components[3]));
                }
            break;
            case "racemaps" :
                if (preg_match("/(\d+)\.png/", $matches[2], $components)) {
                    headeranddie(sprintf("/racemap.php?idraces=%d", $components[1]));
                }
            break;
            case "minimaps" :
                if (preg_match("/(\d+)\.png/", $matches[2], $components)) {
                    headeranddie(sprintf("/minimap.php?idraces=%d", $components[1]));
                }
            break;
            case "flags" :
                if (preg_match("/(.+)\.png/", $matches[2], $components)) {
                    headeranddie(sprintf("/flagimg.php?idflags=%s", $components[1]));
                }
            break;

        }
    }
     header("Cache-Control: no-cache"); // no cache for dummy answer
     header("HTTP/1.0 404 Not Found");
?>
