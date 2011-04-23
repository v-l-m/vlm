<?php

    include_once("config-defines.php");
    include_once("config-funcs.php");
    require_once("vlmc.php");
    require_once("griblib.php");
    date_default_timezone_set('UTC');

    //FIXME: should use wslib with custom class.
    function invalid_values($msg) {
        header("HTTP/1.1 400 Bad Request");
        echo "Invalid values : $msg";
        exit;
    }

    function send_json_header() {
        header("Content-type: application/json; charset=UTF-8");
        $cache = get_grib_validity();
        header("Cache-Control: max-age=".$cache.", must-revalidate");
    }

    //Le temps  - c'est au caller d'appeler list.php avant pour connaitre la date du grib si nécessaire
    $timerequest = intval(get_cgi_var('timerequest', time()));

    if (($timerequest-time() < -MAX_GRIBTIME_HISTORY) || ($timerequest - time() > MAX_GRIBTIME_FUTURE)) {
        invalid_values("bad time requested : $timerequest");
    }

    //Le coefficient de magnification du step ((c) spifou)
    // entre 1 et 8.
    $stepmultiple  = floor(intval(get_cgi_var('stepmultiple', 1.)));
    $step  = 0.5*$stepmultiple;

    if (($step <= 0.) || ($step > 4.)) { // on se limite à des steps raisonables
        invalid_values("bad step requested : $step");
    } 

    //La zone, arrondie, au step le plus proche
    $north = $step*ceil(floatval(get_cgi_var('north'))/$step);
    $south = $step*floor(floatval(get_cgi_var('south'))/$step); 
    $east  = $step*ceil(floatval(get_cgi_var('east'))/$step); 
    $west  = $step*floor(floatval(get_cgi_var('west'))/$step); 

    //Checking values
    if (($north < $south) || ($north > 90.) || ($south < -90.)) {
      invalid_values("bad latitude requested : (north : $north, south : $south)");
    } 

    $east = fmod($east, 360.);
    if ($east < -180.) {
      $east += 360.;
    } else if ($east > 180.) {
      $east -= 360.;
    }

    $west = fmod($west, 360);
    if ($west < -180) {
      $west += 360;
    } else if ($west > 180) {
      $west -= 360;
    }

    if ($east == $west) {
      invalid_values("bad longitude requested : (west : $west, esat : $east)");
    } 

    $now = $timerequest;
    $windgrid = array();

    if ($west < $east) {
        $nb_points = (($north - $south) / $step) * (($east - $west) / $step);
        if ($nb_points > MAX_WIND_POINTS) {
            invalid_values("max wind points reached : ( > ".MAX_WIND_POINTS.")");
        }
        // at least we can do work!
        for ($lat = $south; $lat <= $north; $lat += $step) {
            for ($lon = $west; $lon <= $east; $lon += $step) {
                $twa = get_wind_info_deg($lat, $lon, $now);
                array_push($windgrid, array("lat" => $lat, "lon" => $lon,
                    "wspd" => $twa['speed'], 
                    "whdg" => $twa['heading']));
            }
        }
    } else {
        $nb_points = (($north - $south) / $step) * ((360. - ($west - $east)) / $step);
        if ($nb_points > MAX_WIND_POINTS) {
            invalid_values("max wind points reached : ( > ".MAX_WIND_POINTS.")");
        } 
        for ($lat = $south; $lat <= $north; $lat += $step) {
            for ($lon = $west; $lon <= 180.; $lon += $step) {
                $twa = get_wind_info_deg($lat, $lon, $now);
                array_push($windgrid, array("lat" => $lat, "lon" => $lon,
                    "wspd" => $twa['speed'], 
                    "whdg" => $twa['heading']));
          }
        } 
        $min_lon = -180.+$step-fmod((180.-$west), $step);
        for ($lat = $south; $lat <= $north; $lat += $step) {
            for ($lon = $min_lon; $lon <= $east; $lon += $step) {
                $twa = get_wind_info_deg($lat, $lon, $now);
                array_push($windgrid, array("lat" => $lat, "lon" => $lon,
                    "wspd" => $twa['speed'], 
                    "whdg" => $twa['heading']));
            }
        } 
    }
    send_json_header();
    echo json_encode($windgrid);
?>
