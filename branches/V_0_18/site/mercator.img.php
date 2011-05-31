<?php
    session_start();
    include_once("config.php");
    include_once("mapfunctions.php");
    include_once("map.class.php");
    include_once("functions.php");
    

    //================================================================
    // MAIN
    //================================================================

    $maparea=round(htmlentities(get_cgi_var('maparea', 10)),1); //Ici, maparea est 0.1 à 20.
    // avoid trouble with bad clients
    if ($maparea > 20.) $maparea = 20.;
    if ($maparea < 0.01) $maparea = 0.01;

    $maille=round(htmlentities(get_cgi_var('maille', 1)),1);
    
    // ajustement du niveau de zoom (0.. 20/21)
    $maparea=max(exp($maparea/2.5)/10, MAPAREA_MIN);
    if ($maparea > MAPAREA_MAX ) $maparea=MAPAREA_MAX;
    
    // ajustement de la maille
    if ( $maille <= 0 || !isset($maille) ) $maille=MAILLE_MIN;
    //$maille=max(round(sqrt($maille)*sqrt($maparea-MAPAREA_MIN),1),MAILLE_MIN);
    $maille=max(round(sqrt(($maille)/20)+sqrt($maparea/20),1),MAILLE_MIN);
    
    if ( $maille > MAILLE_MAX ) $maille=MAILLE_MAX;
    //echo "MAILLE=".$maille; exit;
    
    
    $age=max(0,htmlentities(get_cgi_var('age', 0)));
    $estime=max(0,htmlentities(get_cgi_var('estime', 0)));
    $maptype = htmlentities(get_cgi_var('maptype', 'compas'));
    
    $coasts=trim(htmlentities(get_cgi_var('coasts')));
    
    // Taille de la carte
    $x=max(100,htmlentities(get_cgi_var('x', 100)));
    $y=max(100,htmlentities(get_cgi_var('y', 100)));
    // Limitation de la taille de la carte pour pas péter le serveur 
    $boat = htmlentities(get_cgi_var('boat'));
    if (!isAdminLogged()) {
      if ( $x > MAX_MAP_X ) {
	$x=MAX_MAP_X;
      }
      if ( $y > MAX_MAP_Y ) { 
	$y=MAX_MAP_Y;
      }
    }
    
    // On reçoit maintenant un point de coordonnées du centre de la carte
    $lat=htmlentities(get_cgi_var('lat', 0));
    $long=htmlentities(get_cgi_var('long', 0));
    
    // On limite les bornes de la carte (pour les problèmes de coloriage...
    $mapCoords=coordCarte($lat,$long,$maparea,$y,$x);
    
    $north=$mapCoords[0];
    $south=$mapCoords[1];
    $west=$mapCoords[2];
    $east=$mapCoords[3];
    
    while ( $west > 360 ) {
        $west -=360;
    }
    while ( $east > 360 ) {
        $east -=360;
    }
    
    
    ///printf ("N=%f, S=%f, W=%f, E=%f\n", $north, $south, $west, $east);
    // Gestion des cartes autour de Day Changing Line
    
    //$libmap=sprintf ("Map Center : lat=%4.3f,long=%4.3f, Map Borders : N=%4.3f,S=%4.3f,W=%4.3f,E=%4.3f", $lat,$long,$north,$south,$west,$east);
    $libmap=sprintf ("N=%4.3f, S=%4.3f, W=%4.3f, E=%4.3f", $north,$south,$west,$east);
    // Maintenant que le libellé est prêt, on va convertir les cordonnées en cas d'antemeridien
    if ( $west > 0 && $east <0 ) {
        $east +=360;
    }
    
    $proj=htmlentities(get_cgi_var('proj', "mercator"));
    
    $idraces=round(htmlentities(get_cgi_var('idraces')));
    
    $list = get_cgi_var('list');
   
    if ( is_numeric($list) ) {
        $list=array($list);
    }
    $text=htmlentities(get_cgi_var('text', 'right'));
    $save=htmlentities(get_cgi_var('save', 'off'));
    $tracks=htmlentities(get_cgi_var('tracks'));
    $raceover=htmlentities(get_cgi_var('raceover'));
    $windtext=htmlentities(get_cgi_var('windtext'));
    $drawrace=htmlentities(get_cgi_var('drawrace'));
    $drawgrid=htmlentities(get_cgi_var('drawgrid'));
    $drawmap=htmlentities(get_cgi_var('drawmap'));
    $drawwind=htmlentities(get_cgi_var('drawwind'));
    $windonly=htmlentities(get_cgi_var('windonly'));
    $drawlogos=htmlentities(get_cgi_var('drawlogos'));
    $drawscale=htmlentities(get_cgi_var('drawscale'));
    $drawpositions=htmlentities(get_cgi_var('drawpositions'));
    $drawortho=htmlentities(get_cgi_var('drawortho'));
    $drawlibelle=htmlentities(get_cgi_var('drawlibelle'));
    $drawrealboats=htmlentities(get_cgi_var('drawrealboats'));
    $fullres=htmlentities(get_cgi_var('fullres'));
    $drawtextwp=htmlentities(get_cgi_var('drawtextwp'));
    $defaultgridcolor=htmlentities(get_cgi_var('defaultgridcolor'));

    /*
    if ( $maparea > 5 ) {
         $fullres="poly";
    }
    */
    
    $timings=htmlentities(get_cgi_var('timings'));
    $wpnum=floor(htmlentities(get_cgi_var('wp')));
    
    // 2 segments + 1 point : pour visualisation des points de croisement de cote
    $seg1=htmlentities(get_cgi_var('seg1'));
    $seg2=htmlentities(get_cgi_var('seg2'));
    $ec=htmlentities(get_cgi_var('ec'));
    
    //warning
    //when transferring an array by GET or POST
    //if it is empty, the resulting var is NULL and it break everthing
    if ($list == "" || $list == "myboat" ) {
        $list = array();
        array_push($list, $boat);
    }
    if ( $list == "my5opps" ) {
        $list = array();
        $list = findNearestOpponents($idraces,$boat,5);
    }
    
    if ( $list == "my10opps" ) {
        $list = array();
        $list = findNearestOpponents($idraces,$boat,10);
    }
    
    if ( $list == "meandtop10" ) {
        $list = array();
        $list = findTopUsers($idraces,10);
        array_push ($list, $boat);
    }
    
    if ( $list == "mylist" ) {
      $list = explode ("," , getUserPref($boat,"mapPrefOpponents") ); 
      //print_r($list);
    }
    
    
    // Le pas de temps du vent
    if ( $drawwind == "no" ) {
        $drawwind=-1;
    }
    if ( $drawwind >= 0 ) {
        $drawwind=min($drawwind,96)*3600;
    }
    
    
    
    // Age (si list = all alors max age = 1 jour)
    if ( $age == 0 || $list == "all" ) {
        $age = min(24*3600,$age*3600);
    } else {
        $age = $age*3600;
    }

    $fullRacesObj = NULL;
    if ( $list=="all" ) {
        $list = array();
        $fullRacesObj = new fullRaces($idraces); 
        if ( $raceover == "true") {
            foreach ($fullRacesObj->excluded as $excl) {
                array_push($list, $excl->idusers);
            }
        } else {
            foreach ($fullRacesObj->opponents as $opp) {
                array_push($list, $opp->idusers);
            }
        }
        //print_r($list);
    }
    
    
    //COOKIES
    //all the values submitted are stored in a cookie
    /*
    if ($save == "on")
    {
      setcookie("north", $north, time()+3600*24*365); //expire in one year
      setcookie("south", $south, time()+3600*24*365);
      setcookie("east", $east, time()+3600*24*365);
      setcookie("west", $west, time()+3600*24*365);
      setcookie("x", $x, time()+3600*24*365);
      setcookie("y", $y, time()+3600*24*365);
      //setcookie("list", implode(",", $list), time()+3600*24*365);
      //setcookie("list", implode(",", $list), time()+3600*24*365);
      setcookie("proj", $proj, time()+3600*24*365);
      setcookie("text", $text ,time()+3600*24*365);
      setcookie("tracks", $tracks, time()+3600*24*365);
    }
    */
    
    $north*=1000;
    $south*=1000;
    $east*=1000;
    $west*=1000;
    
    
    $time_start = time();
    $mapObj = new map($fullRacesObj, $list, $proj, $text, $tracks, $north, $south, $east, $west, 
		      $idraces, $x, $y, $windtext, $maille, $drawwind, $timings, $drawtextwp, $defaultgridcolor);
    $time_stop = time();
    
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 20, "Time new map = " . ($time_stop - $time_start) . "s", $mapObj->colorText);
    
    if ( $drawrace != "no" && $windonly != "true" ) {
        if ( $wpnum != 0 ) {
          $mapObj->wp_only = $wpnum;
        }
        $time_start = time();
        $mapObj->drawRaces($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 70, "Time drawRaces = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
    }
    
    if ( $drawgrid != "no" && $windonly != "true" ) {
        $time_start = time();
        $mapObj->drawGrid($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 40, "Time drawGrid = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
    }
    
    if ( $drawmap != "no" && $windonly != "true" ) {
        $time_start = time();
        //NB: ici maparea est 0.1 à 300
        $mapObj->drawMap($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coasts, $fullres);
        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 30, "Time drawMap = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
    }
    
    if ( $drawwind >= 0 ) {
        $time_start = time();
        $mapObj->drawWind($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $drawwind);

        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 50, "Time drawWind = ". ($time_stop - $time_start) . "s", $mapObj->colorText);

    }
    
    
    if ( $drawscale != "no" && $windonly != "true" ) {
        $time_start = time();
        $mapObj->drawScale($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 60, "Time drawScale = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
    }
    
    
    if ( $drawpositions != "no" && $windonly != "true" ) {
        $time_start = time();
        if ($drawrealboats != "no") {
          $mapObj->drawRealBoatPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
        }

        if ( $raceover == "true") {
          $mapObj->drawExcludedPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $idraces, $boat, $age, $estime);
        } else {
          $mapObj->drawPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $age, $estime, $maptype);
          if ( $drawortho == "yes" ) {
              $mapObj->drawOrtho($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $estime);
          }
        }
        $time_stop = time();
        if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 80, "Time Positions = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
    }
    
    //echo "OK" ; exit;
    
    if ( $drawlogos != "no" && $windonly != "true" ) {
        // Quelques pubs.
        if ( $idraces == 55 ) {
            $logo = @imagecreatefromgif("images/site/banniere_hi5.gif");
            imagecopymerge ( $mapObj->mapImage, $logo, 1 , $mapObj->ySize-60-1, 35, 0, 240, 60, 60 );
        }
    
        // Logo VLM en haut à droite des cartes.
        $logo = @imagecreatefrompng("images/logos/logovlmnew.png");
        imagecopy ( $mapObj->mapImage, $logo, $mapObj->xSize-65 , 5, 0, 0, 64, 75);

        if ( $idraces == 20090317 or $idraces == 20090318 ) {
            $logo = @imagecreatefromjpeg("images/logos/logobateaux.jpg");
            imagecopymerge ( $mapObj->mapImage, $logo, $mapObj->xSize-360-1 , $mapObj->ySize-85-1, 0, 0, 348, 75, 100 );
        }
    
    }
    
    if ( $drawlibelle != "no" && $windonly != "true" ) {
        imagestring($mapObj->mapImage, 5, 10, $y-20, "Positions : " . gmdate("Y/m/d H:i:s",time()) . " GMT", $mapObj->colorText);

        imagestring($mapObj->mapImage, 3, $x-200 , 15  ,  "Map Borders" ,$mapObj->colorText);
        imagestring($mapObj->mapImage, 3, $x-300 , 25  ,  $libmap ,$mapObj->colorText);
    }
    
    // Dessin d'une croix pour "seg1"
    if ( preg_match ("/^.*,.*:.*,.*$/",$seg1) ) {
        $coords_seg1=preg_split('/[,:]/',$seg1);
        $mapObj->drawSegment($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coords_seg1, $mapObj->colorCC, true);
    }
    
    if ( preg_match ("/^.*,.*:.*,.*$/",$seg2) ) {
        $coords_seg2=preg_split('/[,:]/',$seg2);
        $mapObj->drawSegment($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coords_seg2, $mapObj->colorBlack, false);
    }
    
    // Display
    if (!isset($_GET['noHeader']) || htmlentities($_GET['noHeader']) !=1) {
        header("Content-type: image/png");
      	header("Cache-Control: max-age=0");
    }
    
    $mapObj->display();
?>
 


