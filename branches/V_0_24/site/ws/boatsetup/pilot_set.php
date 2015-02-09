<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $pim = $ws->check_pim();

    switch ($pim) {
        case PILOTMODE_HEADING:
        case PILOTMODE_WINDANGLE:
            $pip = $ws->check_pip_with_float();
            //OK, on a un pip et un pim
            $ws->fullusers->writeNewheading($pim, $pip, $pip);
            break;
        case PILOTMODE_ORTHODROMIC:
        case PILOTMODE_BESTVMG:
        case PILOTMODE_VBVMG:
            if (isset($ws->request['pip'])) {
                $pip = $ws->check_pip_with_wp();
                //OK, on a un pip, et il est valide
                $ws->fullusers->updateTarget($pip['targetlat'], $pip['targetlong'], $pip['targetandhdg']);
            }
            $ws->fullusers->writeNewheading($pim);
            break;
        default :
            $ws->reply_with_error('PIM03');
    }                

    $ws->finish();
?>
