<?php
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
function windAtPosition($_lat = 0, $_long = 0, $when = 0)
{
  /*
    la fonction cree une structure contenant des pointeurs vers
    le grib pour eviter tout deplacement de blocs memoire 
    voir vlm-c/useshmem.c et shmem.c pour plus de details.
    Si on se trouve en mode "MOTEUR", on evite de changer le contexte
    sous les pieds de l'appli
  */
  include_once("vlmc.php");

  //printf ("Lat=%d, Long=%d \n", $_lat, $_long);
  if (defined('MOTEUR')) {
    shm_lock_sem_construct_grib(1);
  } else {
    $temp_vlmc_context = new vlmc_context();
    shm_lock_sem_construct_grib_context($temp_vlmc_context, 1);
  }
  
  $wind_boat = new wind_info();
  if ($when)
  {
    $_time=$when;
  }
  else
  {
    $_time=time();
  }
 
  if (defined('MOTEUR')) {
    VLM_get_wind_info_latlong_millideg($_lat, $_long,
				       $_time, $wind_boat);
    shm_unlock_sem_destroy_grib(1);
  } else {
    VLM_get_wind_info_latlong_millideg_context($temp_vlmc_context,
						      $_lat, $_long,
						      $_time, $wind_boat);
    shm_unlock_sem_destroy_grib_context($temp_vlmc_context, 1);
  }
  
  //printf ("Lat=%d, Long=%d Time=%d\n", $_lat, $_long, $_time);
  //printf ("Wind=%f\n", $wind_boat->speed, $wind_boat->angle);
  return array (
    'speed' => $wind_boat->speed, 'windangle' => $wind_boat->angle
    );
}

?>
