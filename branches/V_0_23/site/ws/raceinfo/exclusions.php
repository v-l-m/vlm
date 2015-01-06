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
  
    //FIXME : tout cela devrait être partiellement factorisé dans wslib
    $res = wrapper_mysql_db_query_reader("SELECT idraces, racename, started, deptime, startlong, startlat, boattype, closetime, racetype, firstpcttime, depend_on, qualifying_races, idchallenge, coastpenalty, bobegin, boend, maxboats, theme, vacfreq FROM races WHERE idraces = ".$idrace);
    
    /*
    //if nothing, then return null.
    if (mysql_num_rows($res) == 0) return 0;
    
    //Race info in the main table
    $info = mysql_fetch_assoc($res);

    //Now fetch the waypoints
    $info["races_waypoints"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT rw.idwaypoint AS idwaypoint, wpformat, wporder, laisser_au, wptype, latitude1, longitude1, latitude2, longitude2, libelle, maparea FROM races_waypoints AS rw LEFT JOIN waypoints AS w ON (w.idwaypoint = rw.idwaypoint) WHERE rw.idraces  = ".$idrace);
    while ($wp = mysql_fetch_assoc($res)) {
        // remove irrelevant information
        switch ($wp["wpformat"] & 0xF) {
            case WP_ONE_BUOY:
                if (array_key_exists('latitude2', $wp)) {
                    unset($wp["latitude2"]);
            }
                if (array_key_exists('longitude2', $wp)) {
                    unset($wp["longitude2"]);
                }
                break;
            case WP_TWO_BUOYS:
            default:
                if (array_key_exists('laisser_au', $wp)) {
                    unset($wp["laisser_au"]);
                }
        }
        $info["races_waypoints"][$wp["wporder"]] = $wp;
    }

    //... and the race instructions
    $info["races_instructions"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT * FROM races_instructions WHERE idraces  = ".$idrace." AND MOD(flag, 2) = 1");
    while ($ri = mysql_fetch_assoc($res)) {
        $info["races_instructions"][] = $ri;
    }

    //... and the races groups
    $info["races_groups"] = Array();
    $res = wrapper_mysql_db_query_reader("SELECT * FROM racestogroups WHERE idraces  = ".$idrace);
    while ($ri = mysql_fetch_assoc($res)) {
        $info["races_groups"][] = $ri['grouptag'];
    }
    */
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

