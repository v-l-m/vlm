<?php
include_once ("config.php");
include_once ("functions.php");

if (!isset($_REQUEST["idraces"])) {
    die();
}
$idraces=$_REQUEST["idraces"];

$image="regate".$idraces;
$thumb="images/minimaps/" . $image . ".png";
$original=getRacemap($idraces,  $_REQUEST['force']);

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
