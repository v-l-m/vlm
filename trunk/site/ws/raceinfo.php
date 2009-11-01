<?php
include_once("config.php");

$trigram_mapping = Array(
/*
    FIXME : Not sure if we should map all the field name to trigram ???
    "idraces" => "RAC",
    "boattype" => "POL",
    "racename" => "RAN",
*/  
    );
   

function get_output_format() {
    //nothing more for now
    return "json";
}

function map_trigram($ar) {
    global $trigram_mapping;
    $ret = Array();
    foreach ($ar as $k => $v) {
        if (array_key_exists($k, $trigram_mapping)) {
            $ret[$trigram_mapping[$k]] = $v;
        } else {
            $ret[$k] = $v;
        }
    }
    return $ret;
}

function get_info_array($idrace) {
    $res = wrapper_mysql_db_query_reader("SELECT * FROM races WHERE idraces = ".$idrace);
    $info = map_trigram(mysql_fetch_assoc($res));
    $info["WPS"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT rw.idwaypoint as idwaypoint, wporder, laisser_au, wptype, latitude1, longitude1, latitude2, longitude2, libelle, maparea FROM races_waypoints as rw LEFT JOIN waypoints as w ON (w.idwaypoint = rw.idwaypoint) WHERE rw.idraces  = ".$idrace);
    while ($wp = mysql_fetch_assoc($res)) {
        $info["WPS"][$wp["wporder"]] = map_trigram($wp);
        }
    
    return $info;
}

function usage() {
    header("Content-type: text/plain; charset=UTF-8");
    echo "usage : http://virtual-loup-de-mer.org/ws/raceinfo.php?idrace=X\n";
    echo "\nX = numero de la course";
}

// now start the real work

$idrace=htmlentities(quote_smart($_REQUEST['idrace']));
if (intval($idrace) == 0) {
    usage();
    exit();
}

$fmt = get_output_format();
$info_array = get_info_array($idrace);
switch ($fmt) {
    case "json":
    default:
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($info_array);
}

?>

