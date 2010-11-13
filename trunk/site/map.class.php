<?php
// Création carte MERCATOR CONFORME
// x = long
// y = ln(tan(PI/4 + lat/2))

class map
{
  var $fullRacesObj,
    $gridListObj,
    $north, $south, $east, $west, //limits of the map in millidegrees
    $LatNorthRad, $LatSouthRad, $LongWestRad, $LongEastRad, //limits of the map in radians
    $mapImage, //image resource
    $xSize,
    $ySize,
    //$length = 8, //size of the arrow, in pixel 
    $xMax,
    $y1,
    $y2,
    $yMax,
    $Xo,
    $Yo,
    $K,
    $colorSea, $colorLines, $color1Lines, $color5Lines, $colorContinent, $colorText,//color definitions
    $startSize = 5 ,//size of the starting square 
    $list = array(),//list of boats to display
    $proj,
    $windtext,
    $text, $tracks,
    $flag_E_W,
    $am_on_map,
    $wp_only,
    $drawtextwp,
    $maille;
  var $arrayFuncProj, $arrayFuncProjLong;
  
  //constructor that set all constants and values
  function map($origRace = NULL, $boatlist, $proj, $text, $tracks, 
               $north = 50000, $south = 40000, $east = 0, $west = -35000 , $idraces=0 , 
               $x = 800, $y = 800 , $windtext = "default" , $maille=1, $drawwind=0, $timings=false, $drawtextwp='on', $defaultgridcolor='yes')
  {
    //echo $north." ".$south."  ".$west." ".$east."\n";

    if ($origRace != null) {
      $this->fullRacesObj = &$origRace;
    } else {
      $this->fullRacesObj = new fullRaces($idraces);
    }
    // Limits of the geographic zone to draw

    $this->north = $north;
    $this->south = $south;

    if ( abs($east)>180000 or abs($west)>180000 ) {
      $this->am_on_map = true ;
    } else { 
      $this->am_on_map = false;
    }

    $this->flag_E_W = false;

    if ( $west < -180000 ) {
      $this->west  = $west + 360000;
      $this->flag_E_W = true;
    } else {
      $this->west  = $west;
    }

    if ( $east > 180000 ) {
      $this->east  = $east - 360000;
      $this->flag_E_W = true;
    } else {
      $this->east  = $east;
    }

    //printf ("West : %f, East = %f\n", $this->west, $this->east);

    $this->xSize = $x;
    $this->ySize = $y;


    $this->LatNorthRad  = deg2rad($north/1000);
    $this->LatSouthRad  = deg2rad($south/1000);
    $this->LongWestRad  = deg2rad($west/1000);
    $this->LongEastRad  = deg2rad($east/1000);
      
    // define size of the associated map
    $this->xMax = abs($this->LongEastRad - $this->LongWestRad);
    $this->y1 = log(tan($this->LatSouthRad/2 + M_PI_4));
    $this->y2 = log(tan($this->LatNorthRad/2 + M_PI_4));
    $this->yMax = abs($this->y2 - $this->y1);
      
    // adapt to display size
    $k1 = $this->xSize/$this->xMax;   // xSize (900) : width max of the map
    $k2 = $this->ySize/$this->yMax;   // ySize (600) : height max of the map

    $this->K=round(min($k1, $k2));
    //printf ("K=%d\n",$this->K);
    $this->xMax = round($this->xMax * $this->K);
    $this->yMax = round($this->yMax * $this->K);
      
    // referentiel change : geographic ==> image
    //  Xmap = Xgeo - Xo   and   Ymap = Yo - Ygeo
    $this->Xo = $this->LongWestRad;
    $this->Yo = $this->y2;
            
    // Create map and defines colors
    // Must be in true colors because we have lot of colors with wind arrows
    $this->mapImage = ImageCreatetruecolor($this->xSize, $this->ySize);

    $seacolor = get_cgi_var('seacolor', DEFAULT_SEA_COLOR);
    if ( $seacolor == "transparent" ) {
        $this->colorSea = $this->fromhex(TRANSPARENT_SEA_COLOR);
        imagecolortransparent($this->mapImage, $this->colorSea);
    } else {
        $this->colorSea = $this->fromhex($seacolor);
    }

    $this->colorContinent= ImageColorAllocate($this->mapImage, 185, 100, 50);
    $this->colorCoastCross= ImageColorAllocate($this->mapImage, 250, 20, 20);
    // Meridiens et Parallèles (10, 5, 1)
    $colgrid = split(",", $defaultgridcolor);

    if ($defaultgridcolor != 'yes' and count($colgrid) == 3) {
        $this->colorLines = $this->fromhex($colgrid[0]);
        $this->color1Lines = $this->fromhex($colgrid[1]);
        $this->color5Lines = $this->fromhex($colgrid[2]);
    } else {
        $this->colorLines = ImageColorAllocate($this->mapImage, 255, 255, 255);
        $this->color1Lines = ImageColorAllocate($this->mapImage, 200, 200, 210);
        $this->color5Lines = ImageColorAllocate($this->mapImage, 180, 180, 230);
    }
    
    $this->colorText = ImageColorAllocate($this->mapImage, 0, 0, 0);
    $this->colorTextOrtho = ImageColorAllocate($this->mapImage, 0, 0, 127);
    $this->colorWarning = ImageColorAllocate($this->mapImage, 230, 80, 80);
    $this->colorBlack = ImageColorAllocate($this->mapImage, 0, 0, 0);
    $this->colorWaypoints = ImageColorAllocate($this->mapImage, 230, 80, 0);
    $this->colorWaypointsArea = ImageColorAllocate($this->mapImage, 230, 120, 40);
    $this->colorWaypointsIceGate = ImageColorAllocate($this->mapImage, 0, 51, 204);
    $this->colorWaypointsIndication = imagecolorallocatealpha($this->mapImage, 0, 184, 46, 64);
    $this->colorBuoy = ImageColorAllocate($this->mapImage, 250, 150, 150);
    $this->colorWind = ImageColorAllocate($this->mapImage, 110, 130, 150);
    $this->colorCC = ImageColorAllocate($this->mapImage, 250, 50, 50);
    
    $this->styleCrossOnceWP = array( $this->colorWaypoints, $this->colorWaypoints, $this->colorWaypoints, $this->colorWaypoints,
                                     $this->colorBlack, $this->colorBlack, $this->colorBlack, $this->colorBlack);
    $this->styleCrossOnceWPLong = array( $this->colorWaypoints, $this->colorWaypoints, $this->colorSea, $this->colorSea,
                                         $this->colorBlack, $this->colorBlack, $this->colorSea, $this->colorSea);

    // FIXME add a style with an arrow in alpha channel.

    //print_r($this);
    $this->list = $boatlist;

    $this->proj = $proj;
    $this->text = $text;
    $this->tracks = $tracks;

    //read wind data from db 
    //printf ("Avant new GridList\n");

    if ( $drawwind >= 0 ) {
      $time_start = time();
      $this->gridListObj = new gridList($this->north, $this->south, $this->west, $this->east, $maille, $drawwind);
      $time_stop = time();
    }
    //printf ("Après new GridList\n");

    $this->windtext = $windtext;
      
    // remplissage du fond de la carte
    imagefill ( $this->mapImage, 0, 0, $this->colorSea );
    if ( $timings == "true" ) imagestring($this->mapImage, 2, 150, 20, "Time gridlist= " . ($time_stop - $time_start) . "s", $this->colorText);
    $this->drawtextwp = ($drawtextwp != 'no');
    //imagefill ( $this->mapImage, 0, 0, $this->colorContinent );
    
    $this->setFuncProjLat($this->proj."Lat2y");
    $this->setFuncProjLat($this->proj."Long2x");

  }

  // function drawLine
  // =================
  // == Draws a line (calls imageline), but before, checks and adapts x coordinates against ante-meridien
  // It transforms geographic coordinates (x1,y1) and (x2,y2) into graphical (pixel) coordinates
  // drawline (image, x1, y1, x2, y2, Color);
  // ========================================
  //   $image : the image to draw into : used as-is
  //   $Color : color (or style) used as-is
  // 
  //   $x1,$y1 : longitude/latitude of first point
  //   $x2,$y2 : longitude/latitude of second point

  function drawLine($image, $x1, $y1, $x2, $y2, $Color)
  {
    // we have to deal with x coordinates : they are a problem around ante-meridian
    // we can use $this->west and $this->east instead of $this->flag_on_map and $this->flag_E_W, and so on...

    imageline($image, $pixelx1, $pixely1, $pixelx2, $pixely2, $Color);

  }

  // (-7 0) -> (-2 -5) -> (-2 -1) -> (7 -1) => (7 1) -> (-2 1) -> (-2 5) (-7 0)

  function drawArrow($image, $x1, $y1, $angle, $color) {
    $deg = round(fmod($angle, 360.0)); 
    switch (intval($angle)) {
    case 0:
      $poly = array( $x1-7, $y1, $x1-2, $y1-5, $x1-2, $y1-1, $x1+7, $y1-1, $x1+7, $y1+1, $x1-2, $y1+1, $x1-2, $y1+5);
      break;
    case 90:
      $poly = array( $x1, $y1-7, $x1-5, $y1-2, $x1-1, $y1-2, $x1-1, $y1+7, $x1+1, $y1+7, $x1+1, $y1-2, $x1+5, $y1-2);
      break;
    case 180:
      $poly = array( $x1+7, $y1, $x1+2, $y1-5, $x1+2, $y1-1, $x1-7, $y1-1, $x1-7, $y1+1, $x1+2, $y1+1, $x1+2, $y1+5);
      break;
    case 270:
      $poly = array( $x1, $y1+7, $x1-5, $y1+2, $x1-1, $y1+2, $x1-1, $y1-7, $x1+1, $y1-7, $x1+1, $y1+2, $x1+5, $y1+2);
      break;
    default:
      $poly = array();
      // (-7 0) -> (-2 -5) -> (-2 -1) -> (7 -1) => (7 1) -> (-2 1) -> (-2 5) (-7 0)
      array_push($poly, $x1+7*cos((180-$deg)*M_PI/180.0), $y1-7*sin((180-$deg)*M_PI/180.0));
      array_push($poly, $x1+5.385164807134504*cos((111.8014094863518-$deg)*M_PI/180.0), $y1-5.385164807134504*sin((111.8014094863518-$deg)*M_PI/180.0));
      array_push($poly, $x1+2.23606797749979*cos((153.434948822922-$deg)*M_PI/180.0), $y1-2.23606797749979*sin((153.434948822922-$deg)*M_PI/180.0));
      array_push($poly, $x1+7.071067811865476*cos((8.130102354156051-$deg)*M_PI/180.0), $y1-7.071067811865476*sin((8.130102354156051-$deg)*M_PI/180.0));
      array_push($poly, $x1+7.071067811865476*cos((-8.130102354156051-$deg)*M_PI/180.0), $y1-7.071067811865476*sin((-8.130102354156051-$deg)*M_PI/180.0));
      array_push($poly, $x1+2.23606797749979*cos((-153.434948822922-$deg)*M_PI/180.0), $y1-2.23606797749979*sin((-153.434948822922-$deg)*M_PI/180.0));
      array_push($poly, $x1+5.385164807134504*cos((-111.8014094863518-$deg)*M_PI/180.0), $y1-5.385164807134504*sin((-111.8014094863518-$deg)*M_PI/180.0));
      break;
    }
    imagefilledpolygon( $image, $poly, 7, $color);
  }
  
  //function draw Grid
  function drawGrid($projCallbackLong, $projCallbackLat) {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  

    //FIXME : hardcoded ?
    $font = 2;

    // draw parallels
    $min_latp = -90000;
    $nb_paralleles=0;
    for ($latp=-89000; $latp<=89000; $latp+=1000) 
      {
        if ( $latp > $this->south && $latp < $this->north ) {
          //special trick : use a callbak function to draw
          $yp = $this->projLat($latp);
          $nb_paralleles++;

          // Coloration des méridiens 1, 5, 10
          if ( $latp % 10000 == 0 ) { 
            $medColor=$this->colorLines;
            //imagestring ( $this->mapImage, $font-1, 3, $yp-6 , $latp /1000, $this->colorBlack);
          } else if ( $latp % 5000 == 0 ) {
            $medColor=$this->color5Lines;
          } else {
            $medColor=$this->color1Lines;
          }

          imageline($this->mapImage, 0, $yp, $this->xSize, $yp, $medColor);
          imagestring ( $this->mapImage, $font-1, 2, $yp-6 , $latp /1000, $this->colorBlack);
          imagestring ( $this->mapImage, $font-1, $this->xSize - 15, $yp-6 , $latp /1000, $this->colorBlack);

          if ( $min_latp == 0 ) $min_latp = $latp;
        }
      }

    // draw meridians
    for ($longm=-360000; $longm<=360000; $longm+=1000) 
      {
        if ( $this->flag_E_W != true && $longm > $this->west && $longm < $this->east 
             || $this->flag_E_W == true && $longm > $this->west && $longm < $this->east + 360000 ) {
          $xm = $this->projLong($longm);

          // Coloration des parallèles 1, 5, 10
          if ( $longm % 10000 == 0 ) { 
            $parColor=$this->colorLines;
            //imagestring( $this->mapImage, $font-1, $xm -4 , 1 , $longm /1000, $this->colorBlack);
          } else if ( $longm % 5000 == 0 ) {
            $parColor=$this->color5Lines;
          } else {
            $parColor=$this->color1Lines;
          }

          imageLine($this->mapImage, $xm, 0, $xm, $this->ySize, $parColor);

          $longm_label = $longm / 1000;
          if ( $longm_label < -180 ) {
            $longm_label +=360 ;
          } else if ( $longm_label > 180 ) {
            $longm_label -=360 ;
          }
          imagestring( $this->mapImage, $font-1, $xm -3 , 1 , $longm_label, $this->colorBlack);
          imagestring( $this->mapImage, $font-1, $xm -3 , $this->ySize -10  , $longm_label , $this->colorBlack);

        }
      }



    //====================================================================

    // Les bordures, pour éviter de colorier la mer :-)
    // 1 - bord gauche  (max avec la bordure West du datafile)
    imagelinethick($this->mapImage, 0, 0, 0, $this->ySize, 
                   $this->colorContinent, 2);

    // 2 - bord haut  
    imagelinethick($this->mapImage, 0, 0, $this->xSize, 0, 
                   $this->colorContinent, 2);

    // 3 - bord droite 
    imagelinethick($this->mapImage, $this->xSize-1, 0, $this->xSize-1, $this->ySize, 
                   $this->colorContinent, 2);

    // 4 - bord bas 
    imagelinethick($this->mapImage, 0, $this->ySize-1, $this->xSize, $this->ySize-1, 
                   $this->colorContinent, 2);
  }


  //draw shoreline
  function drawOneCoast($projCallbackLong, $projCallbackLat, $points , $fullres = MAP_FULLRES_MODE , $coasts = "" ) {
      $numpoints = count($points);

      if ( $numpoints > 0 ) {
          if ( $fullres == MAP_POLYLINE_FULL_MODE  && $numpoints > 4  ) {
              imagefilledpolygon( $this->mapImage, $points, $numpoints/2, $this->colorContinent);
          } else if ( $fullres == MAP_POLYLINE_MODE  && $numpoints > 4  ) {
              imagepolygon( $this->mapImage, $points, $numpoints/2, $this->colorContinent);
          } else {
              // drawCoastline from this x and y arrays
              $last_longitude = 0;
              for ($j=1; $j<$numpoints; $j++) {
                  imagelinethick($this->mapImage, $points[$j-1][1], $points[$j-1][2], $points[$j][1], $points[$j][2], $this->colorContinent, 1);
                  $label_x=$points[$j][1];
                  $label_y=$points[$j][2];
              }

              // numero de cote  : idcoast , coastid
              if ( $coasts != "" ) {
                  imagestring( $this->mapImage, $font, $label_x , $label_y , $points[0], $this->colorBlack);
              }
          }
      }
  }

        
  function addFakeMapPoints($coastarray, $fullres, $prev_x, $prev_y, $curr_x, $curr_y, $coastid, $c = false) {
    $new_x = -1;
    $new_y = -1;
    $new2_x = -1;
    $new2_y = -1;
    // we might have first and last points on different image edges
    // if so, let's add a fake point.
    if (($prev_x <= 1) || ($curr_x <= 1)) {
      $new_x = 0;
    } 
    if (($prev_x >= $this->xMax-1) || ($curr_x >= $this->xMax-1)) {
      if ($new_x == 0) {
	// we need to add two points and reorder...
	if ($prev_x >= $this->xMax-1) {
	  $new2_x = 0;
	  $new_x = $this->xMax;
	} else {
	  $new_x = 0;
	  $new2_x = $this->xMax;
	}
	// now fill the y, and this addresses all cases
	if (($prev_y+$curr_y) < $this->yMax) {
	  $new_y = 0;
	  $new2_y = 0;
	} else {
	  $new_y = $this->yMax;
	  $new2_y = $this->yMax;
	}
      } else {
	$new_x = $this->xMax;
      }
    }
    // check y only if we got one point
    if ($new2_y == -1) {
      if (($prev_y <= 1) || ($curr_y <= 1)) {
	$new_y = 0;
	if ($new2_x != -1) {
	  $new2_y = 0;
	}
      } 
      if (($prev_y >= $this->yMax-1) || ($curr_y >= $this->yMax-1)) {
	if ($new_y == 0) {
	  // in trouble again ;)
	  if ($prev_y >= $this->yMax-1) {
	    $new2_y = 0;
	    $new_y = $this->yMax;
	  } else {
	    $new_y = 0;
	    $new2_y = $this->yMax;
	  }
	  // now fill the y, and this addresses all cases
	  if (($prev_x+$curr_x) < $this->xMax) {
	    $new_x = 0;
	    $new2_x = 0;
	  } else {
	    $new_x = $this->xMax;
	    $new2_x = $this->xMax;
	  }
	} else {
	  $new_y = $this->yMax;
	  if ($new2_x != -1) {
	    $new2_y = $this->yMax;
	  }
	}
      } 
    }
    if ( $fullres == MAP_POLYLINE_FULL_MODE || $fullres == MAP_POLYLINE_MODE) {
      if ($c) {
	if ( ($new2_x >=0) && ($new2_y >=0) ) {
	  array_push ($coastarray, $new2_x, $new2_y);
	} 
	if ( ($new_x >=0) && ($new_y >=0) ) {
	  array_push ($coastarray, $new_x, $new_y);
	} 
      } else {
	if ( ($new_x >=0) && ($new_y >=0) ) {
	  array_push ($coastarray, $new_x, $new_y);
	} 
	if ( ($new2_x >=0) && ($new2_y >=0) ) {
	  array_push ($coastarray, $new2_x, $new2_y);
	} 
      }
    } else {
      if ($c) {
	if ( ($new2_x >=0) && ($new2_y >=0) ) {
	  array_push ($coastarray, array($coastid+.5,$new2_x,$new2_y));
	}
	if ( ($new_x >=0) && ($new_y >=0) ) {
	  array_push ($coastarray, array($coastid+.5,$new_x,$new_y));
	}
      } else {
	if ( ($new_x >=0) && ($new_y >=0) ) {
	  array_push ($coastarray, array($coastid+.5,$new_x,$new_y));
	}
	if ( ($new2_x >=0) && ($new2_y >=0) ) {
	  array_push ($coastarray, array($coastid+.5,$new2_x,$new2_y));
	}
      }
    }
  }
  
  //draw shoreline (search for coasts to draw and call drawOneCoast)
  function drawMap($projCallbackLong, $projCallbackLat, $coasts , $fullres, $print_gshhs=True ) {
      $this->setFuncProjLat($projCallbackLat);
      $this->setFuncProjLong($projCallbackLong);  
      //FIXME : HARDCODED
      $font = 2;

      // =========================================================
      // draw Coastline from database
      // =========================================================
      // get the list of coasts and num points for this map area ($north, $south, $east, $west)
      // Optimisation conseillée par MGA : pas de *1000 dans les reqûetes pour index sur long/lat
      $north_x1000=$this->north/1000 +1;
      $south_x1000=$this->south/1000 -1 ;
      $west_x1000=$this->west/1000 -1;
      $east_x1000=$this->east/1000 +1;

      // 
      // Calcul $num_points
      if ( $this->flag_E_W == true ) {
          $num_points = abs(($north_x1000 - $south_x1000)*($west_x1000-360-$east_x1000));
      } else {
          $num_points = abs(($north_x1000 - $south_x1000)*($east_x1000-$west_x1000));
      }
      //imagestring($this->mapImage, 2, 200 ,50  ,  "N=" . $north_x1000 . ", S=" . $south_x1000 . ", 
      //             W=".$west_x1000.", E=".$east_x1000.", NP=".$num_points ,$this->colorText);
        
      if ( $num_points < 40 ) {
          $coastline_table="coastline_f";
          //$min_coast_points = floor( $num_points / 50 ) ;
      } else if (  $num_points < 200  ) {
          $coastline_table="coastline_h";
          //$min_coast_points = floor( $num_points / 20 ) ;
      } else if (  $num_points < 400  ) {
          $coastline_table="coastline_i";
          //$min_coast_points = floor( $num_points / 10 ) ;
      } else {
          $coastline_table="coastline_l";
          //$min_coast_points = 5;
      }
      if ($print_gshhs) imagestring( $this->mapImage, $font+2, 40 , 10 , "GSHHS_" . substr($coastline_table,10) , $this->colorBlack);

      // Détermination de la zone à cartographier
      // Si W > 180, W = W-360
      //if ($west_x1000 >= 180 ) $west_x1000 -=360;
      // Si E > 180, E = E-360
      //if ($east_x1000 >= 180 ) $east_x1000 -=360;

      // Filtre sur latitude
      $filtre_latitude = "latitude BETWEEN " . $south_x1000 . " AND " . $north_x1000 ; 
      //$filtre_latitude = "";
       
      // Du coup, Si W > E, il faut prendre :
      // W à 180 + -180 à E
      if ( $this->flag_E_W == true ) {
          $filtre_longitude = " AND ((longitude >" . $west_x1000 . " ) OR (longitude < " . $east_x1000 . "))";
      } else {
          $filtre_longitude = " AND longitude BETWEEN " . $west_x1000 . " AND " . $east_x1000 ; 
      }

      //printf ("Coasts=%s\n", $coasts);
      if ( $coasts == "" ) {
          $filtre_onecoast=" " ;
      } else {
          $filtre_onecoast=" AND idcoast= " . $coasts;
      }
      //printf ("West=%f, East=%f, %s\n", $west_x1000, $east_x1000, $filtre_longitude);
        
      // recherche des points de cote du coin

      $query_coast="SELECT idcoast, idpoint, latitude, longitude FROM " . $coastline_table .
          " WHERE " .
          $filtre_latitude .
          $filtre_longitude . 
          $filtre_onecoast .
          " ORDER BY idpoint ;"  ;

      $result_coast = wrapper_mysql_map_db_query_reader($query_coast) or die("Query [$query_coast] failed \n");
      // for each coast, draw the corresponding coastline

      $idcoast = -1;
      $idpoint = -1;

      $polymode = ($fullres == MAP_POLYLINE_FULL_MODE || $fullres = MAP_POLYLINE_MODE);
      while ( $point = mysql_fetch_array($result_coast, MYSQL_NUM) ) {

      // On parcourt tous les points résultant de la requête (ils sont classé par  idcoast, idpoint
      //  - Chaque fois qu'on trouve un point : 

          //    1/ C'est un point d'un trait de cote différent de celui mentionné par "$idcoast"
          //       ==> Il faut tracer le trait de côte terminé (fonction drawOneCoast)
          //       ==> Il faut initialiser le tableau (nettoyage + creation)
          //       +   ==> on ajoute ce point au tableau 

          //    2/ C'est un point du meme trait de cote que le repère "$idcoast", 
          //       +   ==> on ajoute ce point au tableau 

          if ( $point[0] != $idcoast ) {            
	    if ($idcoast != -1 ) {
	      if ( $polymode ) {
		// only needed to close the polygon, not in multiline mode
		$this->addFakeMapPoints(&$coastpoints_array, $fullres, $first_x, $first_y, 
					$x, $y, $idcoast, true);
	      }
	      $this->drawOneCoast($projCallbackLong, $projCallbackLat, $coastpoints_array, $fullres, $coasts);
	    }
	    unset($coastpoints_array);  // Utile ou pas ? vidage mémoire ?
	    $points_count = 0;
	    $coastpoints_array = array(); 
	    $idcoast = $point[0];      
	    $idpoint = -1;
          }
          // + ajout au tableau (dans tous les cas, donc factorisé)

          // latitude
          $y = $this->projLat($point[2]*1000);
          // longitude
          if ( $this->flag_E_W == true && $point[3] < 0 ) {
              $x = $this->projLong(360000+$point[3]*1000);
              //printf ("longitude négative : %f, x=%d\n" , $point[2], $x[$i]);
          } else {
              $x = $this->projLong($point[3]*1000);
          }

	  if ($idpoint != -1) {
	    if ($point[1]-$idpoint != 1) {
	      $this->addFakeMapPoints(&$coastpoints_array, $fullres, $prev_x, $prev_y, $x, $y, $idcoast);
	    }
	  } else {
	    $first_x = $x;
	    $first_y = $y;
	  }
	  $idpoint = $point[1];
	  $prev_x = $x;
	  $prev_y = $y;
          // On prépare un tableau que la fonction drawOneCoast mangera. 
          // Pour le dessin en polygones fermés pleins, on prépare directement la bonne structure
          if ( $polymode ) {
	    array_push ($coastpoints_array, $x, $y);
          } else {
	    // Pour le dessin en polygones fermés vides, on conserve la possibilité d'afficher 
	    // les numéros de cote
	    array_push ($coastpoints_array, array($point[0],$x,$y));
          }
      }
      
      // En fin de parcours, on appelle la fonction de tracage, qui trace si idcoast != -1 
      if ($idcoast != -1 ) {
	if ( $polymode ) {
	  $this->addFakeMapPoints(&$coastpoints_array, $fullres, $first_x, $first_y, $x, $y, $idcoast, true);
	}
	$this->drawOneCoast($projCallbackLong, $projCallbackLat, $coastpoints_array, $fullres, $coasts);
      }
      
      // Puis on supprime le tableau
      unset($coastpoints_array);  // Utile ou pas ? vidage mémoire ?
  }
        



  function drawScale($projCallbackLong, $projCallbackLat)
  {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  


    // Draw scale : scale size depends on map size
    // taille_carte = nord - sud (en millidegrés) ==> /1000 = en milles
    $font = 3;
    $map_size = $this->north - $this->south;
    if ( $map_size > 10000 ) {
      $map_size = round($map_size/10000)*5000;
    } else {
      if ( $map_size > 1000 ) {
        $map_size = round($map_size/1000 )*500;
      } else if ( $map_size > 100 ) {
        $map_size = round($map_size/100  )*50;
      } else {
        $map_size = round($map_size/10   )*5;
      }
    }
    $ortholength = ortho($this->south, $this->west, $this->south + $map_size, $this->west);

    //imagestring ($this->mapImage, $font, 100,10, $map_size, $this->colorBlack);

    $map_scale_length =  abs($this->projLat($this->north - $map_size ));
    //imagestring ($this->mapImage, $font, 100,30, $map_scale_length, $this->colorBlack);

    //verticale
    imageLine($this->mapImage, 20, 20,  20, 20 + $map_scale_length , $this->colorBlack);
    imageLine($this->mapImage, 20, 20,  25, 20 , $this->colorBlack);
    imageLine($this->mapImage, 20, 20 + $map_scale_length,  25, 20 + $map_scale_length, $this->colorBlack);

    // indication distance = ortho (0,taille_barre_echelle)
    imagestring( $this->mapImage, $font, 25, 20 + $map_scale_length  , round($ortholength,3) . "nm" , $this->colorBlack);

  }

  function drawRaces($projCallbackLong, $projCallbackLat) {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  
  
    //draw starting point
    imagerectangle ( $this->mapImage, 
                     $this->projLong($this->fullRacesObj->races->startlong) -$this->startSize,
                     $this->projLat($this->fullRacesObj->races->startlat) -$this->startSize,
                     $this->projLong($this->fullRacesObj->races->startlong) +$this->startSize, 
                     $this->projLat($this->fullRacesObj->races->startlat) +$this->startSize,
                     $this->fromhex("ff0000"));
    if ($this->drawtextwp) {
        imagestring($this->mapImage,
                1,
                $this->projLong($this->fullRacesObj->races->startlong) + 2 * $this->startSize,
                $this->projLat($this->fullRacesObj->races->startlat) -$this->startSize,
                "Start (" . giveDegMinSec('img', $this->fullRacesObj->races->startlat/1000, 
                                          $this->fullRacesObj->races->startlong/1000) . ")" ,
                $this->fromhex("000000"));
    }

    // Boat , to know about the newt wp ?
    $user=htmlentities(get_cgi_var('boat', 0));
    if ( round($user) != 0 ) {
      if (array_key_exists($user, $this->fullRacesObj->opponents)) {
	$usersObj = &$this->fullRacesObj->opponents[$user];
      } else {
	$usersObj = getUserObject($user);
      }
      $nwp = &$usersObj->nwp;
    } else { 
      $nwp=0;
    }
    // draw each waypoint
    $waypoint_num=0;
    foreach( $this->fullRacesObj->races->getWPs() as $waypoint) {
      
      $waypoint_num++;
      //print_r($waypoint);
      /*
        34 Array
        35 (
        36     [0] => -4022
        37     [1] => 47647
        38     [2] => -4034.79803183
        39     [3] => 47614.8024725
        40     [4] => 2
        41     [5] => classement
        42     [6] => Cardinale Sud Jument de Glenan
        43     [7] => 15
        44 )
      */

      // La ligne de l'antemeridien...
      //echo "L1:".$waypoint[1]."\nL2:".$waypoint[3]."\nXo:".$this->Xo."\n";
      //echo "W:".$this->west."\nE:".$this->east."\n";
      if ($this->west > $this->east ) {
          if ( $waypoint['longitude1'] <0 ) $waypoint['longitude1']+=360000;
          if ( $waypoint['longitude2'] <0 ) $waypoint['longitude2']+=360000;
      } else {
      /* le cas connu ou :
       * - antemeridien non visible (east > west)
       * - on veut tracer un wp sur l'antemeridien... (i.e. abs(long1-long2) > 180)
       * - c'est une gate
       */
        if ( ( $waypoint['longitude1'] <0 ) and ($waypoint['longitude2'] >0 ) and ( ($waypoint['wpformat'] & 0xF) == WP_TWO_BUOYS ) and abs($waypoint['longitude1']-$waypoint['longitude2']) > 180000) {
            $waypoint['longitude2']-=360000;
        } else if ( ( $waypoint['longitude2'] <0 ) and ($waypoint['longitude1'] >0 ) and ( ($waypoint['wpformat'] & 0xF) == WP_TWO_BUOYS ) and abs($waypoint['longitude1']-$waypoint['longitude2']) > 180000) {
            $waypoint['longitude1']-=360000;
        }  
      }
          
      $wp1ProjLong = $this->projLong($waypoint['longitude1']);
      $wp1ProjLat  = $this->projLat($waypoint['latitude1']);
      $wp2ProjLong = $this->projLong($waypoint['longitude2']);
      $wp2ProjLat  = $this->projLat($waypoint['latitude2']);

      // bouée sur point 1
      imagefilledellipse($this->mapImage, $wp1ProjLong, $wp1ProjLat,
                         WP_BUOY_SIZE+4, WP_BUOY_SIZE+4, $this->colorBuoy);


      // Coordonnées bouée 1
      if ( $this->drawtextwp && ($this->wp_only == $waypoint_num  || $nwp == $waypoint_num )) {
        imagestring($this->mapImage,
                    3, $wp1ProjLong, $wp1ProjLat,
                    "WP" . $waypoint_num . "(" .giveDegMinSec('img',$waypoint['latitude1']/1000, $waypoint['longitude1']/1000) . ")",
                    $this->colorBlack);
      }

      // bouée sur point 2 (seulement si PORTE, pas si WP)
      if ( ($waypoint['wpformat'] & 0xF) == WP_TWO_BUOYS ) {
        imagefilledellipse($this->mapImage, $wp2ProjLong, $wp2ProjLat,
                           WP_BUOY_SIZE+4, WP_BUOY_SIZE+4, $this->colorBuoy);

        if ( $this->drawtextwp && ($this->wp_only == $waypoint_num || $nwp == $waypoint_num )) {
          imagestring($this->mapImage,
                      3,  $wp2ProjLong, $wp2ProjLat,
                      "WP" . $waypoint_num . "(" .giveDegMinSec('img',$waypoint['latitude2']/1000, $waypoint['longitude2']/1000) . ")",
                      $this->colorBlack);
        }
      }

      if ( $this->wp_only == $waypoint_num  || $nwp == $waypoint_num ) {
        imagesetthickness ( $this->mapImage, WP_THICKNESS);

        if ( ($waypoint['wpformat'] & 0xF) == WP_TWO_BUOYS ) {
	  if ($waypoint['wpformat'] & WP_CROSS_ONCE) {
	    imagesetstyle($this->mapImage, $this->styleCrossOnceWP);
	    imageline ( $this->mapImage, 
			$wp1ProjLong, $wp1ProjLat,
			$wp2ProjLong, $wp2ProjLat,
			IMG_COLOR_STYLED);
	  } else {
	  // NOTE: icegates are only of kind 'WP_TWO_BUOYS'
	    $wpcolor = (($waypoint['wpformat'] & (WP_ICE_GATE_N|WP_ICE_GATE_S)) ? $this->colorWaypointsIceGate : $this->colorWaypoints);
	    imageline ( $this->mapImage, 
			$wp1ProjLong, $wp1ProjLat,
			$wp2ProjLong, $wp2ProjLat,
			$wpcolor);
	  }
	  if ($waypoint['wpformat'] & WP_CROSS_CLOCKWISE) {
	    $this->drawArrow($this->mapImage, ($wp1ProjLong+$wp2ProjLong)/2.0, 
			     ($wp1ProjLat+$wp2ProjLat)/2.0,
			     270+rad2deg(atan2(($wp2ProjLat-$wp1ProjLat),($wp2ProjLong-$wp1ProjLong))), $this->colorWaypointsIndication);
	  } else if ($waypoint['wpformat'] & WP_CROSS_ANTI_CLOCKWISE) {
	    $this->drawArrow($this->mapImage, ($wp1ProjLong+$wp2ProjLong)/2.0, 
			     ($wp1ProjLat+$wp2ProjLat)/2.0,
			     90+rad2deg(atan2(($wp2ProjLat-$wp1ProjLat),($wp2ProjLong-$wp1ProjLong))), $this->colorWaypointsIndication);
	  }
	} else {

          // On va tracer un arc de cercle sur les 200 premiers milles, tous les 10 milles
          // giveEndPointCoordinates(  $latitude, $longitude, $distance, $heading  )
          //$style = array ($this->colorWaypoints, $this->colorSea);
          //imagesetstyle ($this->mapImage, $style);
          $poly_coords=array();
          array_push ($poly_coords, $wp1ProjLong, $wp1ProjLat);
	  
          $wpheading=($waypoint['laisser_au']+180)%360;
          $distEP=10  ; $EP_coords=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
	  
          array_push($poly_coords, $this->projLong($EP_coords['longitude']), $this->projLat($EP_coords['latitude']));
          $distEP=500 ; $EP_coords1=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
          array_push($poly_coords, $this->projLong($EP_coords1['longitude']),
                     $this->projLat($EP_coords1['latitude']));

          $distEP=2000; $EP_coords2=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
          array_push($poly_coords, $this->projLong($EP_coords2['longitude']),
                     $this->projLat($EP_coords2['latitude']));

          imagefilledpolygon( $this->mapImage, $poly_coords, 4, $this->colorBuoy );

	  if ($waypoint['wpformat'] & WP_CROSS_ONCE) {
	    imagesetstyle($this->mapImage, $this->styleCrossOnceWP);
	    imageline ( $this->mapImage, 
			$wp1ProjLong, $wp1ProjLat,
			$this->projLong($EP_coords['longitude']),      
			$this->projLat($EP_coords['latitude']),
			IMG_COLOR_STYLED);
	  } else {
	    imageline ( $this->mapImage, 
			$wp1ProjLong, $wp1ProjLat,
			$this->projLong($EP_coords['longitude']),      
			$this->projLat($EP_coords['latitude']),
			$this->colorBuoy);
	  }
	  // FIXME more arrows ?
	  if ($waypoint['wpformat'] & WP_CROSS_CLOCKWISE) {
	    $this->drawArrow($this->mapImage, $wp1ProjLong-20*cos(deg2rad(90-$waypoint['laisser_au'])), 
			     $wp1ProjLat+20*sin(deg2rad(90-$waypoint['laisser_au'])), 
			     $waypoint['laisser_au'], $this->colorWaypointsIndication);
	  } else if ($waypoint['wpformat'] & WP_CROSS_ANTI_CLOCKWISE) {
	    $this->drawArrow($this->mapImage, $wp1ProjLong-20*cos(deg2rad(90-$waypoint['laisser_au'])), 
			     $wp1ProjLat+20*sin(deg2rad(90-$waypoint['laisser_au'])), 
			     $waypoint['laisser_au']+180, $this->colorWaypointsIndication);
	  }

          $style = array ($this->colorWaypoints, $this->colorSea);
	  if ($waypoint['wpformat'] & WP_CROSS_ONCE) {
	    imagesetstyle($this->mapImage, $this->styleCrossOnceWPLong);
	  } else {
	    imagesetstyle ($this->mapImage, $style);
	  }
          imageline ( $this->mapImage, 
                      $this->projLong($EP_coords['longitude']),
                      $this->projLat($EP_coords['latitude']),
                      $this->projLong($EP_coords2['longitude']),
                      $this->projLat($EP_coords2['latitude']),
                      IMG_COLOR_STYLED);

          /*
          // pointillé sur les WP après 1% de leur longueur.
          // Début du pointillé
          $long_inter=$waypoint[0] + ($waypoint[2]-$waypoint[0]);
          $lat_inter=$waypoint[1] + ($waypoint[3]-$waypoint[1]);

          // Extrémités dessinées
          $long_extrem=$waypoint[0] + ($waypoint[2]-$waypoint[0])*10;
          $lat_extrem=$waypoint[1] + ($waypoint[3]-$waypoint[1])*10;

          imageline ( $this->mapImage, 
          call_user_func_array( array(&$this, $projCallbackLong), $waypoint[0]),
          call_user_func_array( array(&$this, $projCallbackLat),  $waypoint[1]),
          call_user_func_array( array(&$this, $projCallbackLong), $long_inter),      
          call_user_func_array( array(&$this, $projCallbackLat),  $lat_inter),
          $this->colorWaypoints);

          $style = array ($this->colorWaypoints, $this->colorSea);
          imagesetstyle ($this->mapImage, $style);
          imageline ( $this->mapImage, 
          call_user_func_array( array(&$this, $projCallbackLong), $long_inter),
          call_user_func_array( array(&$this, $projCallbackLat),  $lat_inter),
          call_user_func_array( array(&$this, $projCallbackLong), $long_extrem),      
          call_user_func_array( array(&$this, $projCallbackLat),  $lat_extrem),
          IMG_COLOR_STYLED);
          */
        }
        imagesetthickness ( $this->mapImage, 1);

      }
      

      // Numero de WP
      //imagestring($this->mapImage,
      //    2,
      //    call_user_func_array( array(&$this, $projCallbackLong), ($waypoint[0] + $waypoint[2])/2) ,
      //    call_user_func_array( array(&$this, $projCallbackLat),  ($waypoint[1] + $waypoint[3])/2) ,
      //    "WP" . $waypoint_num ,
      //    $this->colorBlack);
    }
  }


  //DrawblackoutWarning (used by drawPositions)
  function drawBlackoutWarning()
  {
      $now=time();

      // Test blackout ou pas
      if ( $now > $this->fullRacesObj->races->bobegin && $now < $this->fullRacesObj->races->boend ) {
          //FIXME what is this $font var ? (I guess it's zero, this the font size is 15)
          imagestring ( $this->mapImage, $font+15, 10, $this->ySize - 40 ,
                        getLocalizedString("blackout").gmdate(getLocalizedString("dateClassificationFormat"),
                          $this->fullRacesObj->races->boend),
                        $this->colorWarning);
          imagestring ( $this->mapImage, $font+15, 9, $this->ySize - 39 ,
                        getLocalizedString("blackout").gmdate(getLocalizedString("dateClassificationFormat"),
                          $this->fullRacesObj->races->boend),
                        $this->colorBlack);
      }
      
  } //drawBlackoutWarning      


  /////////////////////Draw boat positions and tracks
  function drawPositions($projCallbackLong, $projCallbackLat, $age, $estime, $mapTools) {
      $this->setFuncProjLat($projCallbackLat);
      $this->setFuncProjLong($projCallbackLong);  
      $font = 5; //FIXME HARDCODED
      $boat = get_cgi_var('boat');
      if ( $this->list == "" ) return (0);

      $now=time();
    
      $this->drawBlackoutWarning();

      $num_boats_to_draw=0;

      // Si plus de trop de bateaux... on rend la main tout de suite.
      if ( !isAdminLogged() ) {
          if ( count($this->list) > MAX_BOATS_ON_MAPS ) {
              $msg = "No boat drawn. Please select no more than " . MAX_BOATS_ON_MAPS . " boats on maps please ! (selected : ".count($this->list).")";
              imagestring ( $this->mapImage, $font, $this->xSize/2 - 290, $this->ySize/2 ,
                            $msg , $this->colorWarning);
              imagestring ( $this->mapImage, $font, $this->xSize/2 - 289, $this->ySize/2 ,
                            $msg , $this->colorBlack);
              return (0);
          }
      }
      $t_userid = 0;
      if ( !is_null($boat) ) {
          $t_userid = htmlentities($boat);
          if (array_key_exists($t_userid, $this->fullRacesObj->opponents)) {
            	$fullUsersObj = new fullUsers($t_userid, $this->fullRacesObj->opponents[$t_userid], $this->fullRacesObj,
            	                              $this->north, $this->south, $this->west, $this->east, $age);
          } else {
            	$fullUsersObj = new fullUsers($t_userid, NULL, $this->fullRacesObj,
            	                              $this->north, $this->south, $this->west, $this->east, $age);
          }

          // DRAW MyWP
          if ( $fullUsersObj->users->targetlong != 0 && $fullUsersObj->users->targetlat != 0 ) {
              if ($this->west > $this->east && $fullUsersObj->users->targetlong < 0) {
                  //case with wp east of AM and AM is visible
                  $mywpoffset = 360000;
              } else {
                  $mywpoffset = 0;
              }

              imagefilledellipse( $this->mapImage,
                                  $this->projLong($fullUsersObj->users->targetlong*1000+$mywpoffset),
                                  $this->projLat($fullUsersObj->users->targetlat*1000),
                                  WP_BUOY_SIZE, WP_BUOY_SIZE,  $this->fromhex($fullUsersObj->users->color)
                                );
          }
      }

      //FMFM:modif_batafieu_12/06/2007
      // if (in_array($opp->idusers, $this->list ))      //if inside the list

      // Dans tous les cas :  position la plus ancienne = $age
      $maxage=$now - $age ;
      foreach ( $this->list as $opponnent ) {

          // Le pixel demandeur n'est pas sujet au blackout...
          if ( $t_userid == $opponnent ) {
              $minage=0;
          } else {
              // la trajectoire "des autres" :
              // Si on est en cours de blackout (races->bogegin < time() && races->boend > time())
              if ( $this->fullRacesObj->races->bobegin < $now && $this->fullRacesObj->races->boend > $now ) {
                  // ==> position la plus récente pour les concurrents = celle de début du blackout
                  $minage=$this->fullRacesObj->races->bobegin;
              } else {
                  // Sinon = Position la plus récente : maintenant
                  $minage=0;
              }
          }

          //opponent is a userid, get a fulluser is not required, users is sufficient
          // $fullUsersObj = new fullUsers($opponnent, $this->north, $this->south, $this->west, $this->east, $maxage, $minage);
          if (array_key_exists($opponnent, $this->fullRacesObj->opponents)) {
	    $usersObj = &$this->fullRacesObj->opponents[$opponnent];
          } else if (array_key_exists($opponnent, $this->fullRacesObj->excluded)) {
	    $usersObj = &$this->fullRacesObj->excluded[$opponnent];
          } else {
	    continue;
	  }
          // Si le pixel se cache, on passe au suivant
          if ( $usersObj->hidepos > 0 ) continue;

          // Si la couleur est précédée d'un signe "-", on cache la trace (si c'est pas le demandeur, bien sur)
          if ( $usersObj->hasTrackHidden() ) {
              $hidetrack="yes";
              //FIXME : ce n'est pas prudent de réécrire temporairement dans la classe.
              $usersObj->color=substr($usersObj->color,1);
          } else {
            $hidetrack="no";
          }

          // Init last_longitude to 0 : Greenwich. 
          // used to compare to next position if boat crosses day changing line
          $last_longitude = 0;

          // Get the positions from the database            idraces                        first    last
          $positions = new positionsList($opponnent, $this->fullRacesObj->races->idraces, $maxage, $minage);

      
          // If the race is not started, add one position at startpoint (for maps)
          if ( $this->fullRacesObj->races->started == 0 ) {
              $pos = new positions();
              // positions : array(time, long, lat, idusers, race)
              $pos->time    = $now ; 
              $pos->long    = $this->fullRacesObj->races->startlong ;
              $pos->lat     = $this->fullRacesObj->races->startlat  ;
              $pos->idusers = $usersObj->idusers ;
              $pos->idraces = $this->fullRacesObj->races->idraces ;
              array_push ($positions->records, $pos);
          }

          // =======================
          // Tracé de la trajectoire
          // =======================
          $num_segments=0;
          foreach ($positions->records as $posObj) {
              if ( $this->flag_E_W == true && $posObj->long < 0 ) {
                  $x=$this->projLong($posObj->long + 360000);
              } else {
                  $x=$this->projLong($posObj->long);
              }
              $y = $this->projLat($posObj->lat);
              $positionPx = array( $x, $y );
              
              // draw segment ==> ONLY IF NOT CROSSING DAY CHANGING LINE
              if ( ( $last_longitude != 0 && $posObj->long > 0 ) || ( $last_longitude != 0 && $posObj->long < 0 ) ) {
                  if ( $boat == $opponnent || $hidetrack == "no" ) {
                      imageline ( $this->mapImage, 
                                  $positionPx[0],
                                  $positionPx[1],
                                  $nextPosition[0],
                                  $nextPosition[1],
                                  $this->fromhex( $usersObj->color) );
                  }
              }

              //printf("draw line ($positionPx[0], $positionPx[1]) to ($nextPosition[0],$nextPosition[1])\n ");
              $nextPosition = $positionPx;

              // Save it to compare to next position if boat crosses day changing line
              $last_longitude = $posObj->long ;

              // La position heure par heure
              if ( $num_segments%12 == 0 && $num_segments >= 12 ) {
                  $H = array ( 
                              $this->projLong($posObj->long),
                              $this->projLat($posObj->lat)
                             );
                  $ellipseSz=5;
              } else if ( $num_segments%6 == 0 && $num_segments >= 6 ) {
                  $H = array ( 
                              $this->projLong($posObj->long),
                              $this->projLat($posObj->lat)
                             );
                  $ellipseSz=3;
              }          
              if ( $boat == $opponnent && $num_segments%6 == 0 && $num_segments >= 6) {
                  imagefilledellipse($this->mapImage, $H[0], $H[1], 
                                     $ellipseSz, $ellipseSz, $this->fromhex( $usersObj->color)  );
              }

              $num_segments++;
              
          } // foreach positions

          //si pas de position, on passe au suivant !
          if ($num_segments == 0) continue;

          // ## Si le tri dans positionsList est DESC
          //$current_long = $positions->records[0]->long;
          //$current_lat  = $positions->records[0]->lat;
          // ## Si le tri dans positionsList est ASC
          $current_long = $posObj->long;
          $current_lat  = $posObj->lat;

          if ( ( $this->flag_E_W !=true && $current_long > $this->west && $current_long < $this->east ) 
              || ( $this->flag_E_W ==true && $current_long > $this->west - 360000 && $current_long < $this->east + 360000 ) 
              ) {
              $num_boats_to_draw++;

              //print_r($fullUsersObj->lastPositions);
              if ($this->west > $this->east ) {
                  if ( $current_long < -180000 ) $current_long +=360000;
                  if ( $current_long  > 180000 ) $current_long -=360000;
              }

              //================================================
              // POSITION Set a black cross un the boat position
              //================================================
              if ( !strcmp($usersObj->color, DEFAULT_SEA_COLOR) ) $usersObj->color = ALTERNATE_SEA_COLOR;
                  $A = array ( 
                           $this->projLong($current_long),
                           $this->projLat($current_lat)
                       );
                  // Affichage d'une ellipse en plus de la croix
                  imagefilledellipse($this->mapImage, $A[0], $A[1], 
                                     POSITIONSIZE/3, POSITIONSIZE/3, $this-> fromhex( $usersObj->color)  );
                  imageline ( $this->mapImage, $A[0]-POSITIONSIZE/2, $A[1], $A[0]+POSITIONSIZE/2, $A[1], $this->colorBlack);
                  imageline ( $this->mapImage, $A[0], $A[1]-POSITIONSIZE/2, $A[0], $A[1]+POSITIONSIZE/2, $this->colorBlack);
                  //imageline ( $this->mapImage, $x-POSITIONSIZE/2, $y, $x+POSITIONSIZE/2, $y, $this->colorBlack);

                  // A +360°..
                  $B = array ( 
                          $this->projLong($current_long+360000),
                          $this->projLat($current_lat)
                       );
                  // Affichage d'une ellipse en plus de la croix
                  imagefilledellipse($this->mapImage, $B[0], $B[1], 
                                     POSITIONSIZE/3, POSITIONSIZE/3, $this-> fromhex( $usersObj->color)  );
                  imageline ( $this->mapImage, $B[0]-POSITIONSIZE/2, $B[1], $B[0]+POSITIONSIZE/2, $B[1], $this->colorBlack);
                  imageline ( $this->mapImage, $B[0], $B[1]-POSITIONSIZE/2, $B[0], $B[1]+POSITIONSIZE/2, $this->colorBlack);
                  //imageline ( $this->mapImage, $x, $y-POSITIONSIZE/2, $x, $y+POSITIONSIZE/2, $this->colorBlack);

                  //===============================
                  // Affichage des numéros de pixel
                  //===============================
                  $font = 2; //FIXME HARDCODED

                  $width  = ImageFontWidth($font) * strlen($usersObj->boatname) + POSITIONSIZE/2 + 2;
                  if ($this->text == "left") {
                      // Label à la position du bateau
                      imagestring ( $this->mapImage, $font, $A[0] - $width, $A[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
                      imagestring ( $this->mapImage, $font, $B[0] - $width, $B[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
                  } else if ($this->text == "right") {
                      imagestring ( $this->mapImage, $font, $A[0] + POSITIONWIDTH, $A[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
                      imagestring ( $this->mapImage, $font, $B[0] + POSITIONWIDTH, $B[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
                  }

                  // DRAW "MyWP", and merge "Compass" on the map
                  if ( $opponnent == $boat ) {
                      if ( ( $mapTools == "compas" || $mapTools == "bothcompass") ) {
                          $compass_image = imagecreatefromgif ("images/site/compas-transparent.gif");
                          if ( $this->am_on_map == true ) {
                              if ( $current_long <0 )  {
                                  imagecopymerge ( $this->mapImage, $compass_image, $B[0]-174, $A[1]-169, 0 ,0, 350, 341, 100 );
                              } else { 
                                  imagecopymerge ( $this->mapImage, $compass_image, $A[0]-174, $A[1]-169, 0 ,0, 350, 341, 100 );
                              }
                          } else {
                              imagecopymerge ( $this->mapImage, $compass_image, $A[0]-174, $A[1]-169, 0 ,0, 350, 341, 100 );
                          }
                      }
                  }
              $font = 1;

              // Dessin de la trajectoire correspondant à l'estime si != 0
              if ( $estime != 0 && $usersObj->idusers == htmlentities($boat) ) {

                  /* 2008/01/27 : expression de l'estime en temps plutot qu'en distance  */
                  $Estime=giveEndPointCoordinates($current_lat,
                                                  $current_long,
                                                  $estime,
                                                  $usersObj->boatheading  );

                  // Controle sur l'antemeridien
                  if ( $this->am_on_map == true ) {
                      //imagestring ( $this->mapImage, $font, 50, 50 , "AM ON MAP" , $this->colorText);
                      if ( $current_long <0 )  {
                          //imagestring ( $this->mapImage, $font, 50, 60 , "BOAT on west" , $this->colorText);
                          $DepEstime=$B;
                      } else {
                          //imagestring ( $this->mapImage, $font, 50, 60 , "BOAT on east" , $this->colorText);
                          $DepEstime=$A;
                      }
                      if ( $Estime['longitude'] < 0) {
                          $Estime['longitude']+=360000;
                      }
                      //imagestring ( $this->mapImage, $font, 50, 70 ,"EstimeEndAfter ="  .$Estime[1] , $this->colorText);
                  } else {
                      // Si les longitudes de départ et d'arrivée n'ont pas le même signe
                      //         if ( $current_long * $Estime[1] < 0) {
                      // cas du franchissement de Greenwich
                      //            $Estime[1]+=360000;
                      // Dans le sens Amerique-europe
                      // $current_long <0 && $Estime[1] >0

                      // Dans le sens Europe-Amerique
                      // $current_long >0 && $Estime[1] <0

                      // Cas du franchissement de DCL
                      // Dans le sens NZ-Amerique
                      // $current_long >0 && $Estime[1] <0
                      // Dans le sens Amerique-NZ
                      // $current_long <0 && $Estime[1] >0
                      //             }
                      $DepEstime=$A;
                      //imagestring ( $this->mapImage, $font, 50, 50 , "AM NOT ON MAP" , $this->colorText);
                  }

                  $style = array ($this->fromhex( $usersObj->color), $this->colorSea);
                  imagesetstyle ($this->mapImage, $style);
                  $E = array( 
                           $this->projLong($Estime['longitude']),
                           $this->projLat($Estime['latitude'])
                           );
                  imageline ( $this->mapImage, $DepEstime[0], $DepEstime[1], $E[0], $E[1] , IMG_COLOR_STYLED);

                  // Label pour l'estime
                  imagestring ( $this->mapImage, $font, $E[0], $E[1]-5 , "+" .$estime ."nm" , $this->colorText);
              }
          } // if player's position is inside this map
          //FMFM:modif_batafieu_12/06/2007  } // if inside the list
      } // foreach opponent
      //  imagestring ( $this->mapImage, $font, 100,100 , "NB = " . $num_boats_to_draw, $this->colorText);
  }

  /////////////////////Draw boat positions and tracks
  function drawRealBoatPositions($projCallbackLong, $projCallbackLat) {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  

    $boats=getRealBoats();
    $drawn=array();

    foreach ( $boats as $boat ) {

      if ( ! in_array($boat[0], $drawn) ) {

        array_push($drawn , $boat[0]);
        $drawn[$boat[0]] = 15;
        //print_r($drawn);

      } else {

        $drawn[$boat[0]]*=0.95;

      }

      if ( $drawn[$boat[0]] > 14 ) {
        // Sur la Volvo, les pixels reels sont numérotés -700 à -708
        if ( $this->am_on_map == true && $boat[2] < 0 ) {
           $xlogo = $this->projLong($boat[2] + 360000);
        } else {
           $xlogo = $this->projLong($boat[2]);
        }
        $ylogo = $this->projLat($boat[1]);
        if ( $boat[0] > -709 and $boat[0] < -700 ) {
          $Volvologo= imagecreatefrompng("images/bateaux_reels/V70_" . $boat[4] . ".png");
          imagecopymerge  ( $this->mapImage  , $Volvologo  , $xlogo, $ylogo,
                            0 ,0  , 20  , 20  , 70  );
          // boatname : pour le libellé
          $boat[0] = -$boat[0];
          $boatname= $boat[0];
          if ( $boatname >=700 and $boatname <=799 ) $boatname-=700;

        } elseif ( $boat[0] >= -899 and $boat[0] < -800 ) {
          $logo_name= "images/bateaux_reels/VDG" . ( abs($boat[0]) - 800 ) . ".png";
          $VDGLogo= imagecreatefrompng($logo_name);
          imagecopy  ( $this->mapImage  , $VDGLogo  , 
                       $xlogo, $ylogo,
                       0 ,0  , 34  , 55  );
          imageline ( $this->mapImage, $xlogo-POSITIONSIZE/2, $ylogo, $xlogo+POSITIONSIZE/2, $ylogo, $this->colorBlack);
          imageline ( $this->mapImage, $xlogo, $ylogo-POSITIONSIZE/2, $xlogo, $ylogo+POSITIONSIZE/2, $this->colorBlack);

          $boatname = $boat[4];
          // Maj de Ylogo pour descendre un peu le libellé
          $xlogo +=8;
          $ylogo +=8;

        } elseif ( $boat[0] == -11 ) {
          $logo_name= "images/bateaux_reels/SODEBO.png";
          $VDGLogo= imagecreatefrompng($logo_name);
          imagecopy  ( $this->mapImage  , $VDGLogo  , 
                       $xlogo, $ylogo,
                       0 ,0  , 76  , 82  );
          imageline ( $this->mapImage, $xlogo-POSITIONSIZE/2, $ylogo, $xlogo+POSITIONSIZE/2, $ylogo, $this->colorBlack);
          imageline ( $this->mapImage, $xlogo, $ylogo-POSITIONSIZE/2, $xlogo, $ylogo+POSITIONSIZE/2, $this->colorBlack);

          $boatname = $boat[4];
          // Maj de Ylogo pour descendre un peu le libellé
          $xlogo +=8;
          $ylogo +=8;

        } else { 
          // Pas de logo disponible, on dessine des bulles avec numero
          imagefilledellipse($this->mapImage, $xlogo, $ylogo,
                             $drawn[$boat[0]], $drawn[$boat[0]],  $this->fromhex($boat[3])
                             );
          $score=0;
          if ( hexdec(substr($boat[3],0,2)) > 192 ) $score++;
          if ( hexdec(substr($boat[3],2,2)) > 128 ) $score++;
          if ( hexdec(substr($boat[3],4,2)) > 128 ) $score++;

          if ( $score >= 2 ) {
            $numcolor=$this->colorBlack;
          } else {
            $numcolor=$this->colorSea;
          }

          if ( $boat[0] == -3 ) {
              $boatname = "G3";
          } elseif ( $boat[0] == -5 ) {    
              $boatname = "BP";
          } else {
              $boatname = -$boat[0];
          }
        }
        imagestring ( $this->mapImage, $font+1, $xlogo-4, $ylogo-3, $boatname , $numcolor);
      }

    } // foreach opponent
  }


  /////////////////////Draw boat positions and tracks

  /////////////////////Draw boat positions and tracks
  function drawOrtho($projCallbackLong, $projCallbackLat, $estime) {
      $this->setFuncProjLat($projCallbackLat);
      $this->setFuncProjLong($projCallbackLong);
      $font = 2; //FIXME HARDCODED

      $opponent = get_cgi_var('boat', 0);
      if ( intval($opponent)  == 0 ) return(0);

      //opponent is a userid, get a fulluser
      if (array_key_exists($opponent, $this->fullRacesObj->opponents)) {
          $fullUsersObj = new fullUsers($opponent, $this->fullRacesObj->opponents[$opponent], $this->fullRacesObj,
                                        $this->north, $this->south, $this->west, $this->east,
                                        0); //FIXME: why 0 ?(was $age)
      } else {
          $fullUsersObj = new fullUsers($opponent, NULL, $this->fullRacesObj,
                                        $this->north, $this->south, $this->west, $this->east,
                                        0); //FIXME: why 0 ?(was $age)
      }
      // Dessin de la trajectoire correspondant aux premiers pas de temps de la route ortho (morceaux de ORTHOSTEP miles)
      if ( $fullUsersObj->users->idusers == htmlentities($opponent) ) {
          // Des traces de ORTHOSTEP miles pour l'ortho
          //$nbpos=floor($fullUsersObj->distancefromend/ORTHOMAX);
          $nbpos=floor(($this->xSize+$this->ySize)/2);
          $np=0;
          while ( $np <= $nbpos && $np <= ORTHOMAX ) {
              $_lastlong=$fullUsersObj->lastPositions->long;
              $_lastlat=$fullUsersObj->lastPositions->lat;
              //addDistance2Positions
              $Estime=giveEndPointCoordinates( $fullUsersObj->lastPositions->lat,
                              					       $fullUsersObj->lastPositions->long,
					                                     ORTHOSTEP, 
					                                     $fullUsersObj->orthodromicHeading());
	
              $fullUsersObj->lastPositions->addDistance2Positions(ORTHOSTEP,$fullUsersObj->orthodromicHeading());

              // Controle sur l'antemeridien
              if ( $fullUsersObj->lastPositions->long <0 and $Estime['longitude'] > 0) {
                  $Estime['longitude']-=360000;
              }
              if ( $fullUsersObj->lastPositions->long >0 and $Estime['longitude'] < 0) {
                  $Estime['longitude']+=360000;
              }
              if ( $Estime['longitude'] < $this->west || $Estime['longitude'] > $this->east ) {
                  //echo "EAST=$this->east, WEST=$this->west, ESTIME1=".$Estime[1]."\n";
                  break;
              }
              if ( $Estime['latitude'] < $this->south || $Estime['latitude'] > $this->north ) {
                  //echo "NORTH=$this->north, SOUTH=$this->south, ESTIME1=".$Estime[1]."\n";
                  break;
              }

              $style = array($this-> fromhex( $fullUsersObj->users->color), $this->colorSea);
              imagesetstyle ($this->mapImage, $style);

              $A = array($this->projLong($fullUsersObj->lastPositions->long), $this->projLat($fullUsersObj->lastPositions->lat));
              $B = array($this->projLong($fullUsersObj->lastPositions->long+360000), $this->projLat($fullUsersObj->lastPositions->lat));
              if ( $this->am_on_map == true ) {
                  if ( $fullUsersObj->lastPositions->long < 0 )  {
                      $DepOrtho=$B;
                  } else { 
                      $DepOrtho=$A;
                  }
              } else {
                  $DepOrtho=$A;
              }

              $E = array($this->projLong($Estime['longitude']), $this->projLat($Estime['latitude']));
              imageline ( $this->mapImage, $DepOrtho[0], $DepOrtho[1], $E[0], $E[1] , IMG_COLOR_STYLED);
              //imageline ( $this->mapImage, $A[0], $A[1], $E[0], $E[1] , $this->colorTextOrtho);

              $np++;
          }
          
          if ($np == 0) return;
          
          // Label pour l'estime
          $ortho_string="Ortho ";
          if ( $fullUsersObj->users->targetlong != 0 && $fullUsersObj->users->targetlat != 0 ) {
              $ortho_string .= "to your WP";
          }
          imagestring ( $this->mapImage, $font, $E[0], $E[1]-5 , $ortho_string , $this->colorTextOrtho);
      }
  }


  // =====================================================================
  /////////////////////Draw boat positions and tracks (FOR EXCLUDED BOATS)
  // =====================================================================
  function drawExcludedPositions($projCallbackLong, $projCallbackLat, $idraces, $idusers, $age) {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  

    //get a user, get a fulluser
    $excludedUsersObj = new excludedUsers($idusers, $idraces);
    $A = array ( 
                $this->projLong($excludedUsersObj->lastPositions->long),
                $this->projLat($excludedUsersObj->lastPositions->lat)
                 );
        
    // Set a black cross un the boat position
    // Horiz
    imageline ( $this->mapImage, $A[0]-POSITIONSIZE/2, $A[1], $A[0]+POSITIONSIZE/2, $A[1], $this->colorBlack);
    // Vert
    imageline ( $this->mapImage, $A[0], $A[1]-POSITIONSIZE/2, $A[0], $A[1]+POSITIONSIZE/2, $this->colorBlack);

    // No boat heading for excluded boats : assume tyer face North
    //$coordi = trianglecoordinates($A, 0);
    //imagefilledpolygon( $this->mapImage, $coordi, 4, $this->fromhex( $excludedUsersObj->users->color));

    //TODO draw a label
    $font = 1;
    $width  = ImageFontWidth($font) * strlen($excludedUsersObj->users->boatname) + POSITIONSIZE/2 + 2;
    if ($this->text == "left") {
      imagestring ( $this->mapImage, $font, $A[0] - $width, $A[1] - ImageFontHeight($font)/2, $excludedUsersObj->users->idusers . "(" .$excludedUsersObj->users->boatname . ")", $this->colorText);
    } else if ($this->text == "right") {
      imagestring ( $this->mapImage, $font, $A[0] + POSITIONWIDTH, $A[1] - ImageFontHeight($font)/2, $excludedUsersObj->users->idusers . "(" .$excludedUsersObj->users->boatname . ")" , $this->colorText);
    }

  }

  /////////////////////////draw wind arrows
  function drawWind($projCallbackLong, $projCallbackLat, $drawwind = 0) {
    $this->setFuncProjLat($projCallbackLat);
    $this->setFuncProjLong($projCallbackLong);  

    $now = time();
    imagestring($this->mapImage, 5, 350, $this->ySize-20, "Wind : ".gmdate("Y/m/d H:i", $now + $drawwind) . " GMT", $this->colorText);
    foreach( $this->gridListObj->records as $fullGridObj)
      {

        // Si pas de vent, pas de vecteur à représenter.
        // On ne dessine le vecteur que si vent > 0.1 kts.
        if ( $fullGridObj->wspeed > 1 ) {

          // ===
          
          if ( $this->flag_E_W == true && $fullGridObj->Long < 0 ) {
            $x = $this->projLong(360000 + $fullGridObj->Long);
          } else {
            $x = $this->projLong($fullGridObj->Long);
          }
          $y = $this->projLat($fullGridObj->Lat);
          $Tbl = array ( $x, $y );
        
          //get Wind heading and draw a triangle
          $coordi = windtrianglecoordinates($Tbl, $fullGridObj->wheading, 
                                            $fullGridObj->wspeed);
          $col = windspeedtocolorbeaufort($fullGridObj->wspeed, $this->mapImage);
          imagefilledpolygon( $this->mapImage, $coordi, 3, $col);
    
          if ( $this->windtext != "off" ) {
            // Write wind strength (knots)
            // Compute wind-text position with "from wind direction"
            $wind_direction =(int)(($fullGridObj->wheading +180)%360); 
  
            //$text_x=call_user_func_array( array(&$this, $projCallbackLong), $fullGridObj->Long);
            //$text_y=call_user_func_array( array(&$this, $projCallbackLat), $fullGridObj->Lat);
            $text_x = $x;
            $text_y = $y;
  
            if ( $wind_direction < 360 and $wind_direction > 180 )  {
              // Vent de secteur ouest, affichage à gauche
              $text_x-=10;
            } else {
              // Vent de secteur Est, affichage à droite
              $text_x+=5;
            }
  
            // Vent de secteur Ouest ou Est
            //        if ( abs($wind_direction - 270) <=45 or abs($wind_direction - 90) <=45  )  {
            //      // Affichage au meme niveau
            //          $text_y-=5;
            //        } else 
            if ( $wind_direction < 270 and $wind_direction > 90 )  {
              // Vent de secteur sud, affichage en bas
              $text_y+=1;
            } else {
              // Vent de secteur nord, affichage en haut
              $text_y-=5;
            }
  
            // Couleur des libellés
            if ( $this->windtext == "default" ) {
              imagestring($this->mapImage, 1, $text_x, $text_y,
                          round($fullGridObj->wspeed) . "/" . $wind_direction . "°",
                          $this->colorWind);
            } else {
              imagestring($this->mapImage, 1, $text_x, $text_y,
                          round($fullGridObj->wspeed) . "/" . $wind_direction . "°",
                          $col);
            }

          }
        }
      } 
      
  }
  
  // Draw Segment 
  function drawSegment($projCallbackLong, $projCallbackLat, $segCoords, $segColor, $segPoint = true) {
      $this->setFuncProjLat($projCallbackLat);
      $this->setFuncProjLong($projCallbackLong);  

      $y1 = $this->projLat($segCoords[0]*1000);
      $x1 = $this->projLong($segCoords[1]*1000);

      $y2 = $this->projLat($segCoords[2]*1000);
      $x2 = $this->projLong($segCoords[3]*1000);
          
      imageline($this->mapImage, $x1, $y1, $x2, $y2, $segColor);
      //imagestring($this->mapImage, 5, 100, 30, $y1 . "/" . $x1 . ":" . $y2 . "/" . $x2, $this->colorText);
      if ( $segPoint == true ) {
          imagefilledellipse($this->mapImage, $x2, $y2 ,
                             WP_BUOY_SIZE, WP_BUOY_SIZE, $segColor);
      }
  }

  function fromhex($string)
  {
    sscanf($string, "%2x%2x%2x", $red, $green, $blue);
    return ImageColorAllocate($this->mapImage,$red,$green,$blue);
  }   

  function display()
  {
    //   imagestring($this->mapImage, 2, 10, 10, "Vous pouvez déplacer le compas avec la souris", $this->colorText);
    //    imagestring($this->mapImage, 2, 10, 20, "You can drag'n move the compass...", $this->colorText);
    //imagetruecolortopalette($this->mapImage, true, 4096*256);
    Imagepng($this->mapImage);
    ImageDestroy($this->mapImage);
  }



 
  //TODO : add more functions for more projections 
  
  function setFuncProjLat($projnameCallback) {
      $this->arrayFuncProjLat = array(&$this, $projnameCallback);
  }
  
  function setFuncProjLong($projnameCallback) {
      $this->arrayFuncProjLong = array(&$this, $projnameCallback);
  }

  function projLat($lat) {
      return call_user_func($this->arrayFuncProjLat, $lat);
  }

  function projLong($long) {
      return call_user_func($this->arrayFuncProjLong, $long);
  }
  
  // Fonctions de tranposition des latitudes, longitudes en x et y
  // lat in millidegrees
  function mercatorLong2x($Long)
  {
    return (deg2rad($Long/1000) - $this->Xo) * $this->K;
  }

  function mercatorLat2y($Lat)
  {
    $tangente = tan(deg2rad($Lat/1000)/2 + M_PI_4);
    return ($this->Yo - log($tangente)) * $this->K;
  }
  

  function lambertLong2x($Long)
  {
    return (deg2rad($Long/1000) - $this->Xo) * $this->K;
  }

  function lambertLat2y($Lat)
  {
    return ($this->Yo - sin(deg2rad($Lat/1000))) * $this->K;
  }

  function carreLong2x($Long)
  {
    return (deg2rad($Long/1000) - $this->Xo) * $this->K;
  }

  function carreLat2y($Lat)
  {
    return ($this->Yo - deg2rad($Lat/1000)) * $this->K;
  }

}

?>
