<?php
  include_once("config.php");
  include_once("wslib.php");

  //instancie un bateau qui existe et qu'on peut manipuler
  $ws = new WSBaseBoatsetup();

  //vérifie que le bateau n'est pas déjà en course
  if (!$ws->fullusers->users->isEngaged()) $ws->reply_with_error('ENG04', "Cannot leave race ");
  
  //récupère l'idrace d'une course (idr > 0)
  $idr = $ws->check_idr();

  //courses disponibles - FIXME, devrait être factorisé    
  if ( $idr == $ws->fullusers->users->engaged ) 
  {
    //tente de quitter la course
    $ws->fullusers->removeFromRaces();
  } else 
  {
    $ws->reply_with_error('ENG05', "boat is not currently engaged in race ($idr).");
  }

  if ($ws->fullusers->users->isEngaged()) 
  {
    $ws->reply_with_error('ENG06', "Discontinue race ($idr) failed");
  }

  $ws->finish();
?>
