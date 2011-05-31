<?php
    include_once("griblib.php");

    //FIXME: devrait être factorisé avec windgrid
    $answer = get_grib_timestamp_array();
    $cache = get_grib_validity_from_array($answer);

    header("Cache-Control: max-age=".$cache.", must-revalidate");
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode($answer);
?>

