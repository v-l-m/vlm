<?php
    session_start();
    include_once("config.php");
    include_once("functions.php");
       
    if (!isLoggedIn() or !isAdminLogged() ) {
        include ("unallowed.html");
        die();
    } 

    include_once("../externals/php-kml/kml.php");
    include_once('positions.class.php');


    $idu = intval(get_cgi_var('idu', 0));
    $users = getUserObject($idu);
    if (is_null($users)) die("ERROR in IDU");
    $idr = intval(get_cgi_var('idr', $users->engaged));
    if ($idr <= 0) {
        die("ERROR users not engaged or Bad IDR");
    }
    
    if (!raceExists($idr)) die("ERROR in IDR (don't exist)");
    $races = new races($idr);
    $starttime = $races->deptime;
    $endtime = time();
    
    $f = new kml_Folder();
    $f->set_name("Export for #$idu in ~$idr");

    $pi = new fullPositionsIterator($users->idusers, $races->idraces, $starttime, $endtime, $races->vacfreq*60);
    $nbtracks = count($pi->records);

    $points = Array();
    foreach ($pi->records as $t) {
        $h = intval(strftime("%H", $t[0]));
        $points[] = Array($t[1]/1000., $t[2]/1000., 0);
    }

    $p = new kml_Placemark();
    $p->set_name("TRACKS");
    $p->set_Geometry( new kml_LineString($points) );
    $f->add_Feature($p);

    //Dessine les WP
    
    $res = wrapper_mysql_db_query_reader("SELECT rw.idwaypoint AS idwaypoint, wpformat, wporder, laisser_au, wptype, latitude1, longitude1, latitude2, longitude2, libelle, maparea FROM races_waypoints AS rw LEFT JOIN waypoints AS w ON (w.idwaypoint = rw.idwaypoint) WHERE rw.idraces  = ".$idr);
    while ($wp = mysqli_fetch_assoc($res)) {
        switch ($wp["wpformat"] & 0xF) {
            case WP_ONE_BUOY:
                $p = new kml_Placemark();
                $p->set_name(sprintf("WP %s - %s", $wp['idwaypoint'], $wp['libelle']));
                $p->set_Geometry( new kml_Point($wp['longitude1']/1000., $wp['latitude1']/1000.) );
                $f->add_Feature($p);
                break;
            case WP_TWO_BUOYS:
                $p = new kml_Placemark();
                $p->set_name(sprintf("WP %s - %s", $wp['idwaypoint'], $wp['libelle']));
                $p->set_Geometry( new kml_LineString(Array(Array($wp['longitude1']/1000., $wp['latitude1']/1000., 0), Array($wp['longitude2']/1000., $wp['latitude2']/1000., 0) )) );
                $f->add_Feature($p);
                $p = new kml_Placemark();
                $p->set_name(sprintf("WP %s /1", $wp['idwaypoint']));
                $p->set_Geometry( new kml_Point($wp['longitude1']/1000., $wp['latitude1']/1000.) );
                $f->add_Feature($p);
                $p = new kml_Placemark();
                $p->set_name(sprintf("WP %s /2", $wp['idwaypoint']));                
                $p->set_Geometry( new kml_Point($wp['longitude2']/1000., $wp['latitude2']/1000.) );
                $f->add_Feature($p);
                
            default:
        }
    }

    header("Content-type: application/vnd.google-earth.kml+xml");
    header('Content-Disposition: attachment; filename="'."vlm_${idr}_${idu}.kml".'"');
   
    $f->dump();

?>
