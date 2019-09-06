<?php
    include_once("config.php");
    include_once("wslib.php");
    require_once('exclusionzone.class.php');

    $ws = new WSBaseRace();
    $now = time();

    $ws->require_idr();

    //All good, get the zones
    $zones = new exclusionZone($ws->idr);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time' => $now);
    $ws->answer['Exclusions'] = $zones->Exclusions;
    $ws->answer['activeZoneName'] = $zones->activeZoneName;

    //le cas est suffisament rare d'un changement après publication pour qu'on mette un cache de 24h coté client.   
    $ws->maxage = WS_NSZ_CACHE_DURATION;
    $ws->reply_with_success();

?>
