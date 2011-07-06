<?php
    include_once("griblib.php");

    //FIXME: devrait être factorisé avec windgrid
    $answer = get_grib_timestamp_array();
    $update_time = get_grib_update_time();

    $cache = get_grib_validity_from_array($answer, $update_time);

    header("Cache-Control: max-age=".$cache.", must-revalidate");
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($answer);
?>

