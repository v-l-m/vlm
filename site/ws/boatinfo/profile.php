<?php
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    $ws = new WSBaseBoat();
    
    $users = getUserObject($ws->idu);
    if (is_null($users)) $ws->reply_with_error('IDU03');
    $fullusers = new fullUsers(0, $users);

    //FIXME factorisation avec boatinfo.php
    $info     = array();
    $ownerId  = intval($fullusers->users->getOwnerId());
    $ownerObj = ($ownerId != 0) ? getPlayerObject($ownerId) : NULL;

    $info['IDU'] = $fullusers->users->idusers;
    $info['IDP'] = $ownerId;
    $info['IDB'] = $fullusers->users->boatname;
    $info['COL'] = $fullusers->users->color;
    $info['CNT'] = $fullusers->users->country;

    if ($ownerObj != NULL) {
      $info['OWN'] = $ownerObj->playername;
    }   
  
    $ws->answer['profile'] = $info;
    $ws->reply_with_success();

?>
