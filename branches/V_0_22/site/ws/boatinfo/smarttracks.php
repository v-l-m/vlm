<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //Note : this ws doesn't check if this is your boat or not, to be more efficient. 
    //You should use tracks_private.php for yours manageable boats
    // NOT AUTHENTIFIED

    $ws = new WSTracks();
        
    //Il faut déterminer les bons starttime / endtime comme pour les tracks
    //FIXME: shouldn't this be explicit ?
    $starttime = intval(get_cgi_var('starttime', 0)); //0 means now -1h
    if ($starttime == 0) $starttime = $ws->now -3600;
    $endtime = intval(get_cgi_var('endtime', 0)); //0 means now
    if ($endtime == 0) $endtime = $ws->now;
    if ($starttime > $endtime) $ws->reply_with_error("RTFM02");

    //Protect against restart in the same record race - FIXME : should we keep that ? It's client's job ?
    //if ($ws->users->engaged == $idr) $starttime = max(intval($ws->users->userdeptime), $starttime);
    //IMHO it's useless, because for now we don't track multiple race attempt in the same day, thus there will be only one track file !

    //Préremplissagge
    $ws->answer['request'] = Array('time_request' => $ws->now, 'idu' => $ws->users->idusers, 'idr' => $ws->races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);
    $ws->answer['nb_tracks'] = 0;
    $ws->answer['tracks'] = Array();
    $ws->answer['tracks_url'] = Array();
    
    //BO en cours (test sur $ws->now) et BO concerné par la période demandée
    if ($isbo = $ws->isBo($starttime, $endtime)) {
        //BlackOut in place
        //FIXME BO could be just a specific WHERE clause : AND NOT (time > bobegin AND time < boend)
        $endtime = $ws->races->bobegin;
        $ws->maxage = $this->races->boend - $ws->now; //Set up maxage just to the end of the BO. // FIXME : is it safe ?
        $ws->answer['blackout'] = True;
        $ws->answer['blackout_start'] = $ws->races->bobegin;
        $ws->answer['blackout_end'] = $ws->races->boend;
        if ($starttime > $endtime) {
            // if starttime was inside BO and BO in place, return None
            $ws->reply_with_success();
        }
    }

    //OK, here we are, with correct starttime and endtime, and no BO in place for the time period
    //Now cut this into small pieces
    
    //WARNING : the server has to be in UTC
    $starttime_lt = getdate($starttime);
    $endtime_lt = getdate($endtime);

    if(($endtime+3600) > $ws->now) {
        //Si on travaille sur l'heure courante
        $cur_lt = $ws->H($endtime_lt);  //Début de l'heure courante
        $pi = new positionsIterator($ws->users->idusers, $ws->races->idraces, $cur_lt["0"], $endtime, $ws->races->vacfreq*60);        
        $ws->answer['nb_tracks'] = count($pi->records);
        $ws->answer['tracks'] = $pi->records;
        $ws->maxage = $ws->races->getTimeToUpdate($ws->now); //cache headers, wait for next crank
    } else {
        //on est dans le passé de plus d'une heure, on prends l'heure supérieure comme début
        $cur_lt = $ws->H(getdate($endtime+3600));
        $ws->maxage = 2592000; //cache headers, adlib
    }
    
    while ($cur_lt["0"] > $starttime_lt["0"]) { // Tant qu'on a pas atteint le début de la plage
        $delay = $ws->M($cur_lt);
        //$ws->answer['tracks_url'][] = Array("url" => $ws->trackurl($cur_lt), "delai" => $delay, "curlt" => $cur_lt);
        $ws->answer['tracks_url'][] = $ws->trackurl($cur_lt);
        $cur_lt = $ws->H(getdate($cur_lt["0"]-$delay));
    }

    //On restaure le maxage lié au BO si nécessaire.
    if ($isbo) $ws->maxage = $this->races->boend - $ws->now; //Set up maxage just to the end of the BO.
    $ws->reply_with_success();
?>
