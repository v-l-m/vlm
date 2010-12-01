<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    $ws = new WSBaseBoat();
    $now = time();
    
    $users = getUserObject($ws->idu);
    if (is_null($users)) $ws->reply_with_error('IDU03');

    $idr = $ws->check_cgi_int('idr', 'IDR01', 'IDR02', $users->engaged);
 
    if (!raceExists($idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !
    $races = new races($idr);
    
    $notmine = (!in_array($ws->idu, getLoggedPlayerObject()->getManageableBoatIdList())); //impact sur les perfs ?

    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    //FIXME if debug
    $ws->answer['request'] = Array('time_request' => $now, 'idu' => $users->idusers, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);

    if ($races->bobegin < $now && $races->boend > $now && $notmine) {
        //BlackOut in place
        $endtime = $races->bobegin;
        $ws->answer['blackout'] = True;
        $ws->answer['blackout_start'] = $races->bobegin;
        $ws->answer['blackout_end'] = $races->boend;
    }

    if ($users->hasTrackHidden() && $notmine) {
        $ws->answer['tracks_hidden'] = True;
        $ws->answer['nb_tracks'] = 0;
        $ws->reply_with_success();
    }
        
    $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime);
    $ws->answer['nb_tracks'] = count($pi->records);
    $ws->answer['tracks'] = $pi->records;

    $ws->reply_with_success();

?>
