<?php
    include_once("config.php");
    include_once("wslib.php");
    require_once("playersPrefs.class.php");

    $ws = new WSBasePlayersetup();

    $prefs = $ws->check_prefs_list();

    $pprefs = new playersPrefs($ws->player->idplayers);

    foreach ($prefs as $k => $v) {
        $perm = null;
        if (isset($v['permissions'])) $perm = $v['permissions'];
        if (!$pprefs->checkInputPref($k, $v['pref_value'], $perm)) break;
    }

    if ($pprefs->error_status) {
        $ws->reply_with_error("PREFS06", $pprefs->error_string);
    }
    $ws->finish();
?>
