<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    $ws = new WSBaseBoat();
    
    $users = getUserObject($ws->idu);
    if (is_null($users)) $ws->reply_with_error('IDU03');
    $fullusers = new fullUsers(0, $users);

    //FIXME : getter should use also json ?
    $pkey = get_cgi_var("prefs");
    if (is_null($pkey)) $ws->reply_with_error('PREFS02');
    if (!in_array($pkey, explode(',', USER_PREF_ALLOWED))) {
        $ws->reply_with_error('PREFS02', "BAD KEY:$pkey");
    }
    $ws->answer['value'] = $fullusers->getMyPref($pkey);
    $ws->answer['key'] = $pkey;

    $ws->reply_with_success();

?>
