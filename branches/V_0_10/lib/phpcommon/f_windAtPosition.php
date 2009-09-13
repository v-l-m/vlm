<?php
// ========== WIND AT POSITION ========
// Pour les tests (depuis automne 2007..), pour passer facilement d'une
// version à l'autre de la fonction "windAtPosition"
// ==> Abandon de ce système (pour les cartes) à prévoir...
function windAtPosition($_lat = 0, $_long = 0, $when = 0, $version = SYSTEME_WIND_AT_POSITION )
{
  $versions=array("SPF","NO");
  
  // On force le mode de gestion du vent à ce qui est dit dans la config (param.php)
  $version = SYSTEME_WIND_AT_POSITION ;
  
  if ( in_array($version , $versions) ) {
    return call_user_func($version . 'windAtPosition' , $_lat, $_long, $when);
  } else {
    return call_user_func('NOwindAtPosition' , $_lat, $_long);
  }
}

/*
   Cette fonction envoie du vent de nord-ouest
*/
function NOwindAtPosition($_lat , $_long, $when = 0)
{
  $vitesse = 25;
  $angle   = 135;
  
  //                Force         Direction
  //printf ("Lat=%d, Long=%d\n", $_lat, $_long);
  //printf ("Wind=%f\n", $vitesse, $angle);
  return array (
		$vitesse, $angle
		);
}

/*
 *  Cette fonction s'appuie sur le moulin a vent de Yves
 * @input $_lat latitude, en millieme de degres.
 * @input $_long longitude, en millieme de degres.
 * @input $when, offset de temps en secondes par rapport a "maintenant"
 *               defaut a "0".
 * //FIXME ($when si different devrait etre un temps absolu, pour faire
 *          les calculs a un meme instant 
 * @return une array ( vitesse (kts), angle (degres) )
*/
function SPFwindAtPosition($_lat , $_long, $when = 0)
{
  /*
    la fonction cree une structure contenant des pointeurs vers
    le grib pour eviter tout deplacement de blocs memoire 
    voir vlm-c/useshmem.c et shmem.c pour plus de details.
    Si on se trouve en mode "MOTEUR", on evite de changer le contexte
    sous les pieds de l'appli
  */
  include_once("vlmc.php");

  if (defined('MOTEUR')) {
    shm_lock_sem_construct_grib(1);
  } else {
    $temp_vlmc_context = new vlmc_context();
    shm_lock_sem_construct_grib_context($temp_vlmc_context, 1);
  }
  
  $wind_boat = new wind_info();
  $_time=time()+$when;

  if (defined('MOTEUR')) {
    VLM_get_wind_info_latlong_millideg_selective_TWSA($_lat, $_long,
						      $_time, $wind_boat);
    shm_unlock_sem_destroy_grib(1);
  } else {
    VLM_get_wind_info_latlong_millideg_selective_TWSA_context(
					     $temp_vlmc_context, $_lat, $_long,
					     $_time, $wind_boat);
    shm_unlock_sem_destroy_grib_context($temp_vlmc_context, 1);
  }
  
  //printf ("Lat=%d, Long=%d\n", $_lat, $_long);
  //printf ("Wind=%f\n", $wind_boat->speed, $wind_boat->angle);
  return array (
    $wind_boat->speed, $wind_boat->angle
    );
}

?>
