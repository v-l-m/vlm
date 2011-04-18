<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    //Note : this ws doesn't check if this is your boat or not, to be more efficient.
    //You should use tracks_private.php for yours manageable boats

    $ws = new WSBaseBoat();
    $now = time();
    
    $users = getUserObject($ws->idu);
    if (is_null($users)) $ws->reply_with_error('IDU03');
    if ($users->engaged < 0) {
        $defidr = 99999999;
    } else {
        $defidr = $users->engaged;
    }
    
    $idr = $ws->check_cgi_int('idr', 'IDR01', 'IDR02', $defidr);
    if ($idr == 99999999) $ws->reply_with_error("RTFM01");
 
    if (!raceExists($idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !
    $races = new races($idr);
    
    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    if ($starttime != 0 && $endtime != 0 && $starttime > $endtime) $ws->reply_with_error("RTFM02");

    //FIXME if debug
    $ws->answer['request'] = Array('time_request' => $now, 'idu' => $users->idusers, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);

    $isBo = ($races->bobegin < $now && $races->boend > $now);
    if ($isBo) {
        //BlackOut in place
        //FIXME BO could be just a specifi WHERE clause : AND NOT (time > bobegin AND time < boend)
        $endtime = $races->bobegin;
        $ws->answer['blackout'] = True;
        $ws->answer['blackout_start'] = $races->bobegin;
        $ws->answer['blackout_end'] = $races->boend;
    }

    if ($users->hasTrackHidden()) {
        $ws->answer['tracks_hidden'] = True;
        if (!($isBo)) {
            $endtime = $now; //Pas de BO => Force to now()
        }
        $starttime = $endtime - DELAYBETWEENUPDATE; // seulement la dernière trace
    }

    $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $nbtracks = count($pi->records);
    if ($nbtracks < 1 && !$isBo) {
        $pi = new fullPositionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
        $nbtracks = count($pi->records);
    }
    $ws->answer['nb_tracks'] = $nbtracks;
    $ws->answer['tracks'] = $pi->records;

    $ws->reply_with_success();

?>
