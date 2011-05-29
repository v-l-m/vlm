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

    if (!in_array($ws->idu, getLoggedPlayerObject()->getManageableBoatIdList())) {
       $ws->reply_with_error('IDU04');
    }

    $idr = $ws->check_cgi_int('idr', 'IDR01', 'IDR02', $users->engaged);
 
    if (!raceExists($idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !
    $races = new races($idr);
    
    //cache headers
    $ws->maxtime = $races->getTimeToUpdate($now);
    
    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    if ($starttime != 0 && $endtime != 0 && $starttime > $endtime) $ws->reply_with_error("RTFM02");
    //Protect against restart in the same record race
    if ($users->engaged == $idr) $starttime = max(intval($users->userdeptime), $starttime);

    
    //FIXME if debug
    $ws->answer['request'] = Array('time_request' => $now, 'idu' => $users->idusers, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);
        
    $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $nbtracks = count($pi->records);
    if ($nbtracks < 1) {
        $pi = new fullPositionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
        $nbtracks = count($pi->records);
    }
    $ws->answer['nb_tracks'] = $nbtracks;
    $ws->answer['tracks'] = $pi->records;

    $ws->reply_with_success();

?>
