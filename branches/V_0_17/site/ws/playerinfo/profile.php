<?php
include_once("config.php");
include_once("wslib.php");

$ws = new WSBasePlayer();

$idp = get_cgi_var('idp', getPlayerId());
if (is_null($player = getPlayerObject($idp))) $ws->reply_with_error('PLAYER02');

$ws->answer['profile'] = Array();
$ws->answer['profile']['idp'] = intval($player->idplayers);
$ws->answer['profile']['playername'] = $player->playername;
$ws->answer['profile']['admin'] = $player->isAdmin();

$ws->reply_with_success();

?>

