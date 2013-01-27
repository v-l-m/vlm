<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");
    
    //Note : You normally shouldn't call directly statictracks.php, but use it when redirect
    //this ws doesn't check if this is your boat or not, to be more efficient. 
    //You should use tracks_private.php for yours manageable boats
    // NOT AUTHENTIFIED

    $ws = new WSTracks();
    
    //DECODE TIME PATH
    $y = intval(substr(get_cgi_var('ym'), 0, 4));
    $m = intval(substr(get_cgi_var('ym'), 4, 2));
    $d = intval(get_cgi_var('d'));

    $hh = intval(get_cgi_var('h'));
    $min = 0; $s = 0;

    $h = $hh;
    if ($hh > 24 or $hh <= 0) {
        $ws->reply_with_error('TRK01');
    }
    $l = $ws->delay_modulo($hh);
    //we help browser to clean-up cache
    if ($l < 24*3600) {
        $ws->maxage = 24*3600;
    } else {
        $ws->maxage = 2592000;
    }
    $endtime = gmmktime($h, $min, $s, $m, $d, $y);
    $starttime = $endtime - $l;

    //On ne veut rien dans le futur
    if ($endtime > $ws->now) $ws->reply_with_error('RTFM02');

    //On ne veut rien faire s'il y a un BO : EN COURS, et qu'on demande la période du BO
    if ($ws->isBo($starttime, $endtime)) {
        $ws->reply_with_error('RTFM02');
    }
    $ws->answer['request'] = Array('idu' => $ws->users->idusers, 'idr' => $ws->races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);

    // si on demande des traces récentes, on prends la table positions, sinon, on prends large
    if ( ($ws->now - $starttime) < MAX_POSITION_AGE) {
        $pi = new positionsIterator($ws->users->idusers, $ws->races->idraces, $starttime, $endtime, $ws->races->vacfreq*60);
    } else {
        $pi = new fullPositionsIterator($ws->users->idusers, $ws->races->idraces, $starttime, $endtime, $ws->races->vacfreq*60);
    }
    $nbtracks = count($pi->records);
    $ws->answer['nb_tracks'] = $nbtracks;
    $ws->answer['tracks'] = $pi->records;

    $fileref = $ws->trackurl(getdate($endtime));
    $filepath = sprintf("%s/%s", DIRECTORY_TRACKS, $fileref);
    $ws->answer['urlref'] = $fileref;
    $ws->answer['success'] = True;

    $ws->saveJson($filepath);
    $ws->reply_with_success();
?>
