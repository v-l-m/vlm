<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $taskid = $ws->check_pilototo_taskid();
    #FIXME on pourrait factoriser avec _add ...
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
    $ws->users->pilototoUpdate($taskid, $tasktime, $pim, $pip);

    $ws->finish();
?>
