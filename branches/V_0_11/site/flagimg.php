<?php
include_once ("config.php");
include_once ("functions.php");

if (!isset($_REQUEST["idflags"])) {
    die();
}
$idflags = $_REQUEST["idflags"];

$original = getFlag($idflags,  $_REQUEST['force']);

if ($original === False) {
            header("Cache-Control: no-cache"); // no cache for dummy answer
            die("No flags with such id");
}

// Envoi de la miniature
header("Content-Type: image/png");
header("Content-Length: " . filesize($original));
header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
header("Content-Location: " . $original );
// FIXME do we want to send a redirect, here ?

readfile($original);

?> 
