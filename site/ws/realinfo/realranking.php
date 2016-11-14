<?
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked
    $ws = new WSBaseRace();
    $now = time();
    
    $ws->require_idr();
    $races = new races($ws->idr);
    
    $query_ranking = "SELECT US.idusers, US.username boatpseudo, boatname, US.color color, US.country country, '0' nwp," .
        " '0' as dnm, '0' deptime, '0' loch, releasetime, pilotmode pim, US.pilotparameter pip, po1.lat/1000 latitude," . 
        " po1.long/1000 longitude, '0' last1h, '0' last3h, '0' last24h " . 
        " FROM  users US , positions po1" .
        " where US.idusers = po1.idusers" .
        " and US.idusers < 0 and po1.time in (select max(time) from positions po2 where po1.idusers=po2.idusers) " . 
        " AND   po1.race = "  . $races->idraces;

    //echo $query_ranking;

    $res = $ws->queryRead($query_ranking);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time' => $now);
    $ws->answer['ranking'] = array();
    $position = 0;

    $not_started = array();

    while ($row = mysql_fetch_assoc($res)) 
    {
        // N'entrent dans les tableaux que les bateaux effectivement en course
        $has_not_started = (!array_key_exists('nwp',$row) || 
			    (($row['dnm'] == 0.0) && ($row['loch'] == 0.0)));
        // Calcul du status
        if ( $row['releasetime'] > $now ) 
        {
            $row['status'] = 'locked';
        } 
        else if ( $row['pim'] == 2 && abs($row['pip']) <= 1 ) 
        {
            $row['status'] = 'on_coast';
        } 
        else 
        {
            $row['status'] = 'sailing';
        }
        unset($row['pim']);
        unset($row['pip']);
        
        if ($has_not_started) 
        {
            array_push($not_started, $row);
        } 
        else 
        {
            $position += 1;
            $row['rank'] = $position;
            $ws->answer['ranking'][$row['idusers']] = $row;
        }
    }
    // et on copie a la fin les bateaux non partis.
    foreach($not_started as $key => $ns_row) 
    {
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

    /*Compute MaxAge - minimum maxage is UPDATEDURATION*/
    $ws->maxage = $races->getTimeToUpdate($now);

    $ws->reply_with_success();

?>
