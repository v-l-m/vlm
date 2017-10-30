<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    //FIXME : types are badly checked

    //Note : this ws doesn't check if this is your boat or not, to be more efficient.
    //You should use tracks_private.php for yours manageable boats

    $ws = new WSBaseBoat();
    $now = time();
    
    $users = getUserObject($ws->idu);
    $ws->answer['palmares']=$users->GetUserPalmares();
    $ws->answer['boat']['name']=$users->boatname;
    $ws->reply_with_success();

?>
