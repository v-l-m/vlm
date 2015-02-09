<?php
    include_once("config.php");
    include_once("wslib.php");

    //instancie un bateau qui existe et qu'on peut manipuler
    $ws = new WSBaseBoatsetup();

    //vérifie que le bateau n'est pas déjà en course
    if ($ws->fullusers->users->isEngaged()) $ws->reply_with_error('ENG01', "Already engaged in ".$ws->fullusers->users->engaged);
    
    //récupère l'idrace d'une course (idr > 0)
    $idr = $ws->check_idr();

    //courses disponibles - FIXME, devrait être factorisé    
     $avRaces=availableRaces($ws->fullusers->users->idusers);

     if ( in_array($idr, $avRaces) ) {
        //tente de s'inscrire
        $ws->fullusers->subscribeToRaces($idr);
     } else {
        $ws->reply_with_error('ENG02', "This race ($idr) is not available for this boat.");
     }

    if (!$ws->fullusers->users->isEngaged()) $ws->reply_with_error('ENG03', "Subscribe to race ($idr) failed");

    $ws->finish();
?>
