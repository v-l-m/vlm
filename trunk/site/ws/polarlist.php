<?php
include_once("config.php");
include_once('vlmc.php');


function get_output_format() {
  //nothing more for now
  return "json";
}

function get_polar_list_array() {
  $boat_polar = array();

  $temp_vlmc_context = new vlmc_context();
  shm_lock_sem_construct_polar_context($temp_vlmc_context, 1); 
  $nb_polars = get_nb_polars_context($temp_vlmc_context);
  for ($i=0; $i<$nb_polars; $i++) {
    $pname = get_polar_name_index_context($temp_vlmc_context, $i);
    $boat_polar[] = $pname;
  }
  shm_unlock_sem_destroy_polar_context($temp_vlmc_context, 1);  
  return $boat_polar;
}

$fmt = get_output_format();
$polar_list = get_polar_list_array();

switch ($fmt) {
case "json":
default:
  header('Content-type: application/json; charset=UTF-8');
  echo json_encode($polar_list);
}

?>

