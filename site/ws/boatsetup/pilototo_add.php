<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $tasktime = $ws->check_pilototo_tasktime();
    $pim = $ws->check_pim();

    switch ($pim) {
        case PILOTMODE_HEADING:
        case PILOTMODE_WINDANGLE:
            $pip = $ws->check_pip_with_float();
            break;
        case PILOTMODE_ORTHODROMIC:
        case PILOTMODE_BESTVMG:
        case PILOTMODE_VBVMG:
            $pip = $ws->check_pip_with_wp();
            $pip = $ws->target_array2string($pip);
            break;
        default :
            reply_with_error('PIM03');
    }
    $ws->fullusers->users->pilototoAdd($tasktime, $pim, $pip);

    if ($ws->check_pilototo_list_on_success()) {
        $ws->fullusers->users->pilototoList(True);
        $ws->answer['pilototo_list'] = $ws->fullusers->users->pilototo;
    }
    
    $ws->finish();
?>
