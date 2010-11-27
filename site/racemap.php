<?php
    include_once ("functions.php");

    if (!isset($_REQUEST["idraces"])) {
        die();
    }
    $idraces=$_REQUEST["idraces"];

    $original=getRacemap($idraces,  get_cgi_var('force', 'no'));

    if ($original ===False) {
        header("Cache-Control: no-cache"); // no cache for dummy answer
        die("No racemap with such id");
    }

    // Envoi de la miniature
    header("Content-Type: image/jpg");
    header("Content-Length: " . filesize($original));
    header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
    header("Content-Location: " . $original );
    // FIXME do we want to send a redirect, here ?

    readfile($original);

?> 
