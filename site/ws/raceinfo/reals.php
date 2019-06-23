<?
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked
    $ws = new WSBaseRace();
    $now = time();
    
    $ws->require_idr();
    $races = new races($ws->idr);
    if (!raceExists($ws->idr)) $ws->reply_with_error('IDR03'); //FIXME : select on races table made two times !

    
    $query_reals = "SELECT -`idusers`as `idreals`, MAX(`time`) as `tracks_updated`, COUNT(DISTINCT `time`) as `nb_tracks` FROM `positions`" .
      " WHERE `race` = "  . $races->idraces .
      " AND `time` > ".($now - 48*3600) . //FIXME : hardcoded
      " AND `idusers` < 0" .
      " GROUP BY `idusers` ". //
      " ORDER BY `idusers`";

    $res = $ws->queryRead($query_reals);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time_request' => $now);
    $ws->answer['reals'] = array();

    while ($row = mysqli_fetch_assoc($res)) {
        $ws->answer['reals'][] = $row; //Array("idreals" => -$row['idusers'], "tracks_updated" => $row['track_updated'], "nb_tracks" => $row['nbtracks']);
    }

    $ws->answer['nb_boats'] = count($ws->answer['reals']);
    $ws->reply_with_success();

?>
