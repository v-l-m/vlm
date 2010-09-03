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
    $res = wrapper_mysql_db_query_reader("SELECT idraces, racename, started, deptime, startlong, startlat, boattype, closetime, racetype, firstpcttime, depend_on, qualifying_races, idchallenge, coastpenalty, bobegin, boend, maxboats, theme, vacfreq FROM races WHERE idraces = ".$idrace);
    
    //if nothing, then return null.
    if (mysql_num_rows($res) == 0) return 0;
    
    //Race info in the main table
    $info = map_trigram(mysql_fetch_assoc($res));

    //Now fetch the waypoints
    $info["races_waypoints"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT rw.idwaypoint AS idwaypoint, wpformat, wporder, laisser_au, wptype, latitude1, longitude1, latitude2, longitude2, libelle, maparea FROM races_waypoints AS rw LEFT JOIN waypoints AS w ON (w.idwaypoint = rw.idwaypoint) WHERE rw.idraces  = ".$idrace);
    while ($wp = mysql_fetch_assoc($res)) {
      // remove irrelevant information
      switch ($wp["wpformat"] & 0xF) {
      case WP_ONE_BUOY:
	unset($wp["latitude2"]);
	unset($wp["longitude2"]);
	break;
      case WP_TWO_BUOYS:
      default:
	unset($wp["laisser_au"]);
      }
      $info["races_waypoints"][$wp["wporder"]] = map_trigram($wp);
    }

    //... and the race instructions
    $info["races_instructions"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT * FROM races_instructions WHERE idraces  = ".$idrace." AND MOD(flag, 2) = 1");
    while ($ri = mysql_fetch_assoc($res)) {
        $info["races_instructions"][] = map_trigram($ri);
    }
    
    //the racemap ???
    return $info;
}

function usage() {
    $usage = "usage : http://virtual-loup-de-mer.org/ws/raceinfo.php?idrace=X\n";
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
	echo json_encode($info_array);
}

?>

