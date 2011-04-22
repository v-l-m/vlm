<?php
    include_once("config-defines.php");
    include_once("vlmc.php");

function get_grib_timestamp_array() {
  $ts_array = array();
  
  $global_vlmc_context = new vlmc_context();
  global_vlmc_context_set($global_vlmc_context);
  shm_lock_sem_construct_grib(1);
  $nb_grib = get_prevision_count();
  for ($i=0; $i < $nb_grib; $i++) { 
    array_push($ts_array, get_prevision_time_index($i));
  }
  shm_unlock_sem_destroy_grib(1);
  return $ts_array;
}

//FIXME: devrait être factorisé avec windgrid
$answer = get_grib_timestamp_array();
// check if we have a full grib or a 
if (count($answer) > 10) {
  $cache = $answer[0] + 34200 - time(); /* grib offset + 9h30 */ 
} else {
  $cache = 10; /* we use 10s as the default */
}
header("Cache-Control: max-age=".$cache.", must-revalidate");
header("Content-type: application/json; charset=UTF-8");
echo json_encode($answer);
?>

