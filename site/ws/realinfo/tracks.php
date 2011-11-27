<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');
    include_once('reals.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    $ws = new WSRealBoat();
    $now = time();
    
    $reals = new reals($ws->idreals);
    if (is_null($reals)) $ws->reply_with_error('REALS03');
    if ($reals->engaged < 0) {
        $defidr = 99999999;
    } else {
        $defidr = $reals->engaged;
    }
    
    $idr = $ws->check_cgi_int('idr', 'IDR01', 'IDR02', $defidr);
    if ($idr == 99999999) $ws->reply_with_error("RTFM01");
 
    if (!raceExists($idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !
    $races = new races($idr);
    
    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    if ($starttime != 0 && $endtime != 0 && $starttime > $endtime) $ws->reply_with_error("RTFM02");
    //Protect against restart in the same record race
    if ($reals->engaged == $idr) $starttime = max(intval($reals->userdeptime), $starttime);

    $ws->answer['request'] = Array('time_request' => $now, 'idreals' => $reals->idreals, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);

    $pi = new positionsIterator(-$reals->idreals, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $nbtracks = count($pi->records);
    if ($nbtracks < 1 && !$isBo) {
        $pi = new fullPositionsIterator(-$reals->idreals, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
        $nbtracks = count($pi->records);
    }
    $ws->answer['nb_tracks'] = $nbtracks;
    $ws->answer['tracks'] = $pi->records;
    //cache headers = distance temps moyenne entre chaque trace
    $ws->maxtime = ($pi->maxtime - $pi->mintime)/$nbtracks;
    $ws->answer['maxtime'] =  $ws->maxtime;

    $ws->reply_with_success();

?>
