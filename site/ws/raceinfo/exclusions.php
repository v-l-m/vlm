<?php
include_once("config.php");
require_once('functions.php');
require_once('exclusionzone.class.php');

function get_output_format() {
    //nothing more for now
    return "json";
}

function get_info_array($idRace) 
{
  $zones = new exclusionZone($idRace);

  $info=$zones;
  
  return $info;
}

function usage() {
    $usage = "usage : ".WWW_SERVER_URL."/ws/raceinfo/exclusions.php?idrace=X\n";
    $usage .= "\nX = numero de la course";
    return $usage;
}

// now start the real work

$idrace=htmlentities(quote_smart($_REQUEST['idrace']));
if (intval($idrace) == 0) {
    header("Content-type: text/plain; charset=UTF-8");
    echo usage();
    exit();
}

$fmt = get_output_format();
$info_array = get_info_array($idrace);
switch ($fmt) {
    case "json":
    default:
        header('Content-type: application/json; charset=UTF-8');
        //le cas est suffisament rare d'un changement après publication pour qu'on mette un cache de 24h coté client.
        header("Cache-Control: max-age=". (24*3600) .", must-revalidate");
        echo json_encode($info_array);
}

?>

