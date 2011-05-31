<?
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked
    $ws = new WSBaseRace();
    $now = time();
    
    $ws->require_idr();
    $limit = intval($ws->check_cgi('limit', "LIMIT01", 99999));
    $races = new races($ws->idr);
    
    $query_ranking = "SELECT RR.idusers idusers, US.username boatpseudo, US.boatname boatname, US.color color, US.country country, nwp, dnm, userdeptime as deptime, RR.loch loch, US.releasetime releasetime, US.pilotmode pim, US.pilotparameter pip, latitude, longitude, last1h, last3h, last24h " . 
      " FROM  races_ranking RR, users US " . 
      " WHERE RR.idusers = US.idusers " . 
      " AND   RR.idraces = "  . $races->idraces .
      " ORDER BY nwp desc, dnm asc".
      " LIMIT ".$limit;

    $res = $ws->queryRead($query_ranking);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time' => $now);
    $ws->answer['ranking'] = array();
    $position = 0;

    $not_started = array();

    while ($row = mysql_fetch_assoc($res)) {
        // N'entrent dans les tableaux que les bateaux effectivement en course
        $has_not_started = (!array_key_exists('nwp',$row) || 
			    (($row['dnm'] == 0.0) && ($row['loch'] == 0.0)));
        // Calcul du status
        if ( $row['releasetime'] > $now ) {
            $row['status'] = 'locked';
        } else if ( $row['pim'] == 2 && abs($row['pip']) <= 1 ) {
            $row['status'] = 'on_coast';
        } else {
            $row['status'] = 'sailing';
        }
        unset($row['pim']);
        unset($row['pip']);
        $row['latitude'] /= 1000.;
        $row['longitude'] /= 1000.;
	
	if ($has_not_started) {
	  array_push($not_started, $row);
	} else {
	  $position += 1;
	  $row['rank'] = $position;
	  $ws->answer['ranking'][$row['idusers']] = $row;
	}
    }
    // et on copie a la fin les bateaux non partis.
    foreach($not_started as $key => $ns_row) {
        $position += 1;
        $ns_row['rank'] = $position;
        $ws->answer['ranking'][$ns_row['idusers']] = $ns_row;
    }

    list ($num_arrived , $num_racing, 
	  $num_engaged) = getNumOpponents($races->idraces);    
    $ws->answer['nb_arrived'] = $num_arrived;
    $ws->answer['nb_racing']  = $num_racing;
    $ws->answer['nb_engaged'] = $num_engaged;
    $ws->answer['nb_not_started'] = count($not_started);

    $ws->reply_with_success();

?>
