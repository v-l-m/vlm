<?php
include_once('vlmc.php');
require_once('functions.php');

function get_output_format() {
  //nothing more for now
  return "json";
}

$fmt = get_output_format();
$answer = array();
$answer['list'] = get_polar_list_array();
$answer['urls'] = Array();
$answer['urls']['pol'] = '/speedchart.php?format=pol&boattype=%s';
//FIXME : a factoriser avec la publication d'autres paramètres statiques
$answer['urls']['csv'] = '/Polaires/boat_%s.csv';

switch ($fmt) {
case "json":
default:
    header('Content-type: application/json; charset=UTF-8');
    //le cas est suffisament rare d'un changement de polaire pour mettre un cache de 24h
    header("Cache-Control: max-age=". (24*3600) .", must-revalidate");
    echo json_encode($answer);
}

?>

