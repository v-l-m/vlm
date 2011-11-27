<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once("reals.class.php");

    header("content-type: text/plain; charset=UTF-8");

    $ws = new WSRealBoat(); // attention, l'API prends un id > 0 (qui est rendu négatif pour utilisation du modèle de données)
    $ws->maxage = 24*3600; //client cache duration : 24h

    $reals = new reals($ws->idreals);
    if (is_null($reals)) $ws->reply_with_error('IDU03');

    $info['idreals'] = $reals->idreals;
    $info['boatname'] = $reals->boatname;
    $info['color'] = $reals->color;
    $info['flag'] = $reals->flag;
    $info['engaged'] = $reals->engaged;
    $info['shortname'] = $reals->shortname;
    $info['description'] = $reals->description;
    //$info['updated'] = $reals->updated; // Not working for now
 
    $ws->answer['profile'] = $info;
    $ws->reply_with_success();

?>
