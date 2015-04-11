<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once('positions.class.php');

    header("content-type: text/plain; charset=UTF-8");

    // NOT AUTHENTIFIED

    $ws = new WSBase();

    $step  = intval(get_cgi_var('step', 15));
    if ($step != 5 && $step != 15) $ws->reply_with_error("GRB01");

    //La zone, arrondie, au step le plus proche
    $north = $step*ceil(floatval(get_cgi_var('north'))/$step);
    $south = $step*floor(floatval(get_cgi_var('south'))/$step);
    if ($south >= $north) $ws->reply_with_error("GRB02");
    $east  = $step*ceil(floatval(get_cgi_var('east'))/$step); 
    $west  = $step*floor(floatval(get_cgi_var('west'))/$step);
    if ($west >= $east) $west -= 360;

    $grib_date = intval(get_cgi_var("date", date("Ymd")."00"));
    $gribfile = sprintf("%s/gfs_NOAA-%s.grb", GRIB_DIRECTORY, $grib_date);
    if (! file_exists($gribfile)) $ws->reply_with_error("GRB03");
    
    //Préremplissagge
    $ws->answer['request'] = Array('time_request' => $ws->now, 'north' => $north, 'south' => $south, 'east' => $east, 'west' => $west, 'step' => $step, 'date' => $grib_date);
    $ws->answer['gribs_url'] = Array();
        
    $count = 0;
    while ($south < $north) {
        $twest = $west;
        while ($twest < $east) {
            $originaldir = sprintf("%d/%d", $south, $twest);
            $original = sprintf("%s/%d.%02d.grb", $originaldir, $grib_date, $step);
            $ws->answer['gribs_url'][] = $original;
            $count += 1;
            $twest += $step;
        }
        $south += $step;
    }
    $ws->answer['count_url'] = $count;

    $ws->reply_with_success();
?>
