<?php
    include_once("config.php");
    include_once("wslib.php");
    require_once('playersPrefs.class.php');

    $ws = new WSBasePlayer();

    $idp = get_cgi_var('idp', getPlayerId());
    if (is_null($player = getPlayerObject($idp))) $ws->reply_with_error('PLAYER02');

    $ws->answer['profile'] = Array();
    $ws->answer['profile']['idp'] = intval($player->idplayers);
    $ws->answer['profile']['playername'] = $player->playername;
    $ws->answer['profile']['admin'] = $player->isAdmin();

    //FIXME : il faut factoriser les constitutions de masque

    $masque = VLM_ACL_AUTH;
    //is boatsitter
    if (in_array(getPlayerId(),$player->getBoatsitterList())) $masque |= VLM_ACL_BOATSIT;

    //FIXME : getter should use also json ?
    $pkeys = get_cgi_var("prefs", PLAYER_PREF_ALLOWED);
    if (is_null($pkeys)) $ws->reply_with_error('PREFS02');
    
    $keys = explode(',', $pkeys);
    $ws->answer['prefs'] = Array();
    foreach ($keys as $pkey) {
        if (!in_array($pkey, explode(',', PLAYER_PREF_ALLOWED))) {
            $ws->reply_with_error('PREFS02', "BAD KEY:$pkey");
        }
        $pval = $player->getPref($pkey);
        if (!is_null($pval) && (getPlayerId() == $player->idplayers || ($pval['permissions'] & $masque)) ) {
            $ws->answer['prefs'][$pkey] = $pval;
        }
    }
    $ws->reply_with_success();

?>

