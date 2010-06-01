<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $taskid = $ws->check_pilototo_taskid();
    $ws->fullusers->users->pilototoDelete($taskid);

    $ws->finish();
?>
