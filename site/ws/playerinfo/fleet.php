<?php
include_once("config.php");
include_once("wslib.php");

$ws = new WSBasePlayer();

$idp = get_cgi_var('idp', getPlayerId());
if (is_null($player = getPlayerObject($idp))) $ws->reply_with_error('PLAYER02');

$ws->answer['fleet'] = Array();
foreach ($player->getOwnedBoatIdList() as $idb) {
    $idb = intval($idb);
    $u = getUserObject($idb);
    if (!is_null($u)) {
        $b = Array();
        $b['idu'] = intval($u->idusers);
        $b['boatname'] = $u->username;
        $b['engaged'] = intval($u->engaged);
        $ws->answer['fleet'][$b['idu']] = $b;
    }
}

$ws->reply_with_success();

?>

