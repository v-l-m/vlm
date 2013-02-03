<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $pip = $ws->check_pip_with_wp();
    //OK, on a un pip, et il est valide
    $ws->fullusers->updateTarget($pip['targetlat'], $pip['targetlong'], $pip['targetandhdg']);

    $ws->finish();
?>
