<?
    include_once("config.php");
    include_once("wslib.php");
    require_once('exclusionzone.class.php');

    $ws = new WSBaseRace();
    $now = time();

    //COMPAT with previous params idrace => idr
    if (!isset($_REQUEST['idr']) && isset($_REQUEST['idrace'])) $_REQUEST['idr'] = $_REQUEST['idrace'];    
    $ws->require_idr();

    //All good, get the zones
    $zones = new exclusionZone($ws->idr);

    $ws->answer['request'] = array('idr' => $ws->idr, 'time' => $now);
    $ws->answer['Exclusions'] = $zones->Exclusions;
    $ws->answer['activeZoneName'] = $zones->activeZoneName;

    //le cas est suffisament rare d'un changement après publication pour qu'on mette un cache de 24h coté client.   
    $ws->maxage = 24*3600;
    $ws->reply_with_success();

?>
