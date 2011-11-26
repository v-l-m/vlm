<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once("reals.class.php");

    header("content-type: text/plain; charset=UTF-8");

    $ws = new WSBaseBoat(); // attention, l'API prends un id > 0 (qui est rendu négatif pour utilisation du modèle de données)
    $ws->maxage = 24*3600; //client cache duration : 24h


//    print $ws->idu;
    $reals = new reals($ws->idu);    
//    $reals = getUserObject(-$ws->idu); // négation de l'id
    if (is_null($reals)) $ws->reply_with_error('IDU03');

    $info['idreals'] = $reals->idreals;
    $info['boatname'] = $reals->boatname;
    $info['color'] = $reals->color;
    $info['flag'] = $reals->flag;
 
    $ws->answer['profile'] = $info;
    $ws->reply_with_success();

?>
