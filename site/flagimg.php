<?php
    include_once ("functions.php");

    if (!isset($_REQUEST["idflags"])) {
        die();
    }
    $idflags = get_cgi_var("idflags");

    $original = getFlag($idflags,  get_cgi_var('force'));

    if ($original === False) {
        header("Cache-Control: no-cache"); // no cache for dummy answer
        die("No flags with such id");
    }

    // Envoi de la miniature
    header("Content-Type: image/png");
    header("Content-Length: " . filesize($original));
    header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
    header("Content-Location: " . $original );

    readfile($original);

?> 
