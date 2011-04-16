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
    
    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    //FIXME if debug
    $ws->answer['request'] = Array('time_request' => $now, 'idu' => $users->idusers, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);
        
    $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $ws->answer['nb_tracks'] = count($pi->records);
    $ws->answer['tracks'] = $pi->records;

    $ws->reply_with_success();

?>
