<?php
include_once("config.php");
include_once("functions.php");
date_default_timezone_set('UTC');

define ('MAX_WIND_POINTS', 2048);

function invalid_values() {
  header("HTTP/1.1 400 Bad Request");
  echo "Invalid values";
  exit;
}

function send_json_header() {
  header("content-type: application/json");
}

function get_wind_info_deg($_lat, $_long, $_time) {

  $temp_vlmc_context = new vlmc_context();
  shm_lock_sem_construct_grib_context($temp_vlmc_context, 1);
  
  $wind_boat = new wind_info();

  VLM_get_wind_info_latlong_deg_context($temp_vlmc_context, $_lat, $_long,
					$_time, $wind_boat);
  shm_unlock_sem_destroy_grib_context($temp_vlmc_context, 1);
  
  return array ("speed" => $wind_boat->speed, "heading" => fmod($wind_boat->angle+180., 360.));
}

$north = ceil(floatval(get_cgi_var('north')));
$south = floor(floatval(get_cgi_var('south'))); 
$east  = ceil(floatval(get_cgi_var('east'))); 
$west  = floor(floatval(get_cgi_var('west'))); 
$step  = floatval(get_cgi_var('step', 0.5)); 

$time_offset = intval(get_cgi_var('timeoffset'), 0);

if (($north < $south) || ($north > 90) || ($south < -90)) {
  invalid_values();
} 

$east = fmod($east, 360);
if ($east < -180) {
  $east += 360;
} else if ($east > 180) {
  $east -= 360;
}

$west = fmod($west, 360);
if ($west < -180) {
  $west += 360;
} else if ($west > 180) {
  $west -= 360;
}

if ($east == $west) {
  invalid_values();
} 

if (($step <= 0.0) || ($time_offset < 0) || ($time_offset > 2592000)) {
  invalid_values();
}

$windgrid = array();
$now = time()+$time_offset;

if ($west < $east) {
  $nb_points = (($north - $south) / $step) * (($east - $west) / $step);
  if ($nb_points > MAX_WIND_POINTS) {
    invalid_values();
  }
  // at least we can do work!
  for ($lat = $south; $lat <= $north; $lat += $step) {
    for ($lon = $west; $lon <= $east; $lon += $step) {
      $twa = get_wind_info_deg($lat, $lon, $now);
      array_push(&$windgrid, array("lat" => $lat, "lon" => $lon,
				   "wspd" => $twa['speed'], 
				   "whdg" => $twa['heading']));
    }
  }
} else {
  $nb_points = (($north - $south) / $step) * ((360 - ($west - $east)) / $step);
  if ($nb_points > MAX_WIND_POINTS) {
    invalid_values();
  } 
  for ($lat = $south; $lat <= $north; $lat += $step) {
    for ($lon = $west; $lon <= 180.; $lon += $step) {
      $twa = get_wind_info_deg($lat, $lon, $now);
      array_push(&$windgrid, array("lat" => $lat, "lon" => $lon,
				   "wspd" => $twa['speed'], 
				   "whdg" => $twa['heading']));
    }
  } 
  $min_lon = -180+$step-fmod((180-$west), $step);
  for ($lat = $south; $lat <= $north; $lat += $step) {
    for ($lon = $min_lon; $lon <= $east; $lon += $step) {
      $twa = get_wind_info_deg($lat, $lon, $now);
      array_push(&$windgrid, array("lat" => $lat, "lon" => $lon,
				   "wspd" => $twa['speed'], 
				   "whdg" => $twa['heading']));
    }
  } 
}
send_json_header();
echo json_encode($windgrid);

?>
