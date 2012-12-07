<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    class StaticTracks extends WSBase {
        #FIXME coulde be generic
        public $answer = Array();

        function __construct() {
        }
        
        function saveJson($filename, $force = 'no') {
            $path = dirname($filename);
            // Création et mise en cache
            if ( ( ! file_exists($filename) ) ||  ($force == 'yes') ) {
                if (!is_dir($path)) mkdir($path, 0777, True);
                return file_put_contents ($filename , json_encode($this->answer));
            }
            return True;
        }
    }


    $ws = new StaticTracks();
    $now = time();
    
    $users = getUserObject($ws->check_cgi_int('u', 'IDU01', 'IDU02'));
    if (is_null($users)) $ws->reply_with_error('IDR03');
    $idr = $ws->check_cgi_int('r', 'IDR01', 'IDR02');
    if (!raceExists($idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !
    $races = new races($idr);

    //DECODE TIME PATH
    $y = intval(substr(get_cgi_var('ym'), 0, 4));
    $m = intval(substr(get_cgi_var('ym'), 4, 2));
    $d = intval(get_cgi_var('d'));

    $hh = intval(get_cgi_var('h'));
    $min = 0; $s = 0;

    $h = $hh;
    if ($hh > 24 or $hh < 0) {
        die("bad hh");
    } else if ($hh == 24) {
        $h = 0;
        $l = 24*3600;
    } else if ( $hh % 8 == 0 ) {
        $l = 8*3600;
    } else if ( $hh % 4 == 0 ) {
        $l = 4*3600;
    } else if ( $hh % 2 == 0 ) {
        $l = 2*3600;
    } else {
        $l = 3600;
    }    
    $starttime = gmmktime($h, $min, $s, $m, $d, $y);
    $endtime = $starttime + $l;

    if ($starttime > $now or $endtime > $now) $ws->reply_with_error('RTFM02');

    $isBo = ($races->bobegin < $startime && $races->boend > $endtime && $races->bobegin > $now && $races->boend < $now );
    if ($isBo) {
        $ws->reply_with_error('RTFM02');
    }

    $ws->answer['request'] = Array('idu' => $users->idusers, 'idr' => $races->idraces, 'starttime' => $starttime, 'endtime' => $endtime);

    $pi = new positionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $nbtracks = count($pi->records);
    if ($nbtracks < 1) {
        $pi = new fullPositionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
        $nbtracks = count($pi->records);
    }
    $ws->answer['nb_tracks'] = $nbtracks;
    $ws->answer['tracks'] = $pi->records;

    $u2 = intval($users->idusers/100);
    $u1 = $users->idusers - 100*$u2;

    $fileref = sprintf("%04d%02d/%02d/%02d/%d/%02d/%d.json", $y, $m, $d, $hh, $races->idraces, $u1, $u2);
    $filepath = sprintf("%s/%s", DIRECTORY_TRACKS, $fileref);
    $ws->answer['fileref'] = $fileref;
    $ws->answer['success'] = True;
    $ws->maxage = 2592000;
    $ws->saveJson($filepath);
    $ws->reply_with_success();

?>
