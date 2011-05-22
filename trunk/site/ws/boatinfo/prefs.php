<?php
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    $ws = new WSBaseBoat();
    $ws->maxage = 60; //client cache duration

    if (!in_array($ws->idu, getLoggedPlayerObject()->getManageableBoatIdList())) {
       $ws->reply_with_error('IDU04');
    }
    
    $users = getUserObject($ws->idu);
    if (is_null($users)) $ws->reply_with_error('IDU03');

    $fullusers = new fullUsers(0, $users);

    //FIXME : getter should use also json ?
    $pkeys = get_cgi_var("prefs", USER_PREF_ALLOWED);
    if (is_null($pkeys)) $ws->reply_with_error('PREFS02');
    
    $keys = explode(',', $pkeys);
    $ws->answer['prefs'] = Array();
    foreach ($keys as $pkey) {
        if (!in_array($pkey, explode(',', USER_PREF_ALLOWED))) {
            $ws->reply_with_error('PREFS02', "BAD KEY:$pkey");
        }
        $ws->answer['prefs'][$pkey] = $fullusers->getMyPref($pkey);
    }
    $ws->reply_with_success();

?>
