<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $taskid = $ws->check_pilototo_taskid();
    $ws->fullusers->users->pilototoDelete($taskid);

    if ($ws->check_pilototo_list_on_success()) {
        $ws->fullusers->users->pilototoList(True);
        $ws->answer['pilototo_list'] = $ws->fullusers->users->pilototo;
    }

    $ws->finish();
?>
