<?php
    include_once("griblib.php");

    //FIXME: devrait être factorisé avec windgrid
    $grib_array = get_grib_timestamp_array();
    $update_time = get_grib_update_time();    
    $answer = Array("update_time"     => $update_time, 
		    "grib_timestamps" => $grib_array);

    $cache = get_grib_validity_from_array($grib_array, $update_time);

    header("Cache-Control: max-age=".$cache.", must-revalidate");
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($answer);
?>

