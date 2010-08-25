<?php
include_once("config.php");
include_once("wslib.php");

$ws = new WSBasePlayer();

$player = getLoggedPlayerObject();

$ws->answer['fleet'] = getBoatArrayFromIdList($player->getOwnedBoatIdList());
$ws->answer['fleet_boatsit'] = getBoatArrayFromIdList($player->getBoatsitIdList());

$ws->reply_with_success();

?>

