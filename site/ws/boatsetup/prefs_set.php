<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $prefs = $ws->check_prefs_list();

    foreach ($prefs as $k => $v) {
        if (strlen($v)>255 && !in_array($k, explode(',', LARGE_USER_PREF_ALLOWED))) $ws->reply_with_error("PREFS03", "With key=$k");
        if (!$ws->fullusers->setPref($k, $v)) break;
    }

    $ws->finish();
?>
