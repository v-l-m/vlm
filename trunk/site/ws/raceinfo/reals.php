<?
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked
    $ws = new WSBaseRace();
    $now = time();
    
    $ws->require_idr();
    $races = new races($ws->idr);
    
    $query_positions = "SELECT `time`, `idusers`, `lat`, `long` FROM `positions`" .
      " WHERE `race` = "  . $races->idraces .
      " AND `time` > ".($now - 24*3600) .
      " AND `idusers` < 0" .
      " ORDER BY `time` desc, `idusers`";

    $res = $ws->queryRead($query_positions);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time_request' => $now);
    $ws->answer['realpositions'] = array();

    while ($row = mysql_fetch_assoc($res)) {
        if (!isset($ws->answer['realpositions'][$row['idusers']])) $ws->answer['realpositions'][$row['idusers']] = Array();
        $ws->answer['realpositions'][$row['idusers']][] = Array($row['time'], $row['lat'], $row['long']);
    }

    $ws->answer['nb_boats']  = count($ws->answer['realpositions']);

    $ws->reply_with_success();

?>
