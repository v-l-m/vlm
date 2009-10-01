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

  
  //constructor that set all constants and values
  function map($origRace = NULL, $boatlist, $proj, $text, $tracks, 
               $north = 50000, $south = 40000, $east = 0, $west = -35000 , $idraces=0 , 
               $x = 800, $y = 800 , $windtext = "default" , $maille=1, $drawwind=0, $timings=false, $drawtextwp='on')
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

    $seacolor=$_GET['seacolor'];
    if ( $seacolor != "transparent" ) {
      if ( isset($seacolor)) {
        $this->colorSea=$this->fromhex($seacolor);
      } else {
        //$this->colorSea=ImageColorAllocate($this->mapImage, 220, 235, 245);
        $this->colorSea=$this->fromhex(DEFAULT_SEA_COLOR);
      }
    } else {
      // Set the sea as the transparency color (to be really transparent)
      $this->colorSea=$this->fromhex(TRANSPARENT_SEA_COLOR);
      imagecolortransparent($this->mapImage, $this->colorSea);
    }

    $this->colorContinent= ImageColorAllocate($this->mapImage, 185, 100, 50);
    $this->colorCoastCross= ImageColorAllocate($this->mapImage, 250, 20, 20);
    // Meridiens et Parallèles (10, 5, 1)
    $this->colorLines = ImageColorAllocate($this->mapImage, 255, 255, 255);
    $this->color1Lines = ImageColorAllocate($this->mapImage, 200, 200, 210);
    $this->color5Lines = ImageColorAllocate($this->mapImage, 180, 180, 230);
    $this->colorText = ImageColorAllocate($this->mapImage, 0, 0, 0);
    $this->colorTextOrtho = ImageColorAllocate($this->mapImage, 0, 0, 127);
    $this->colorWarning = ImageColorAllocate($this->mapImage, 230, 80, 80);
    $this->colorBlack = ImageColorAllocate($this->mapImage, 0, 0, 0);
    $this->colorWaypoints = ImageColorAllocate($this->mapImage, 230, 80, 0);
    $this->colorWaypointsArea = ImageColorAllocate($this->mapImage, 230, 120, 40);
    $this->colorBuoy = ImageColorAllocate($this->mapImage, 250, 150, 150);
    $this->colorWind = ImageColorAllocate($this->mapImage, 110, 130, 150);
    $this->colorCC = ImageColorAllocate($this->mapImage, 250, 50, 50);

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



  //function draw Grid
  function drawGrid($projCallbackLong, $projCallbackLat)
  {
    $font = 2;

    // draw parallels
    $min_latp = -90000;
    $nb_paralleles=0;
    for ($latp=-89000; $latp<=89000; $latp+=1000) 
      {
        if ( $latp > $this->south && $latp < $this->north ) {
          //special trick : use a callbak function to draw
          $yp = call_user_func_array( array(&$this, $projCallbackLat), $latp );
          $nb_paralleles++;

          // Coloration des méridiens 1, 5, 10
          if ( $latp % 10000 == 0 ) { 
            $medColor=$this->colorLines;
            //imagestring ( $this->mapImage, $font-1, 3, $yp-6 , $latp /1000, $colorBlack);
          } else if ( $latp % 5000 == 0 ) {
            $medColor=$this->color5Lines;
          } else {
            $medColor=$this->color1Lines;
          }

          imageline($this->mapImage, 0, $yp, $this->xSize, $yp, $medColor);
          imagestring ( $this->mapImage, $font-1, 2, $yp-6 , $latp /1000, $colorBlack);
          imagestring ( $this->mapImage, $font-1, $this->xSize - 15, $yp-6 , $latp /1000, $colorBlack);

          if ( $min_latp == 0 ) $min_latp = $latp;
        }
      }

    // draw meridians
    for ($longm=-360000; $longm<=360000; $longm+=1000) 
      {
        if ( $this->flag_E_W != true && $longm > $this->west && $longm < $this->east 
             || $this->flag_E_W == true && $longm > $this->west && $longm < $this->east + 360000 ) {
          $xm = call_user_func_array( array(&$this, $projCallbackLong), $longm );

          // Coloration des parallèles 1, 5, 10
          if ( $longm % 10000 == 0 ) { 
            $parColor=$this->colorLines;
            //imagestring( $this->mapImage, $font-1, $xm -4 , 1 , $longm /1000, $colorBlack);
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
          imagestring( $this->mapImage, $font-1, $xm -3 , 1 , $longm_label, $colorBlack);
          imagestring( $this->mapImage, $font-1, $xm -3 , $this->ySize -10  , $longm_label , $colorBlack);

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
  function drawOneCoast($projCallbackLong, $projCallbackLat, $points , $fullres = "nopoly" , $coasts = "" )
  {

            $numpoints = count($points);

            if ( $numpoints > 0 ) {

                if ( $fullres == "poly"  && $numpoints > 2  ) {
                  imagefilledpolygon( $this->mapImage, $points, $numpoints, $this->colorContinent);

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
                            imagestring( $this->mapImage, $font, $label_x , $label_y , $points[0], $colorBlack);
                      }
                }

            }
  }

        

  //draw shoreline (search for coasts to draw and call drawOneCoast)
  function drawMap($projCallbackLong, $projCallbackLat, $coasts , $fullres, $maparea=2 )
  {
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
    imagestring( $this->mapImage, $font+2, 40 , 10 , "GSHHS_" . substr($coastline_table,10) , $colorBlack);

    // Détermination de la zone à cartographier
    // Si W > 180, W = W-360
    //if ($west_x1000 >= 180 ) $west_x1000 -=360;
    // Si E > 180, E = E-360
    //if ($east_x1000 >= 180 ) $east_x1000 -=360;

    // Filtre sur latitude
    $filtre_latitude = " and latitude between " . $south_x1000 . " and " . $north_x1000 ; 
    //$filtre_latitude = "";
     
    // Du coup, Si W > E, il faut prendre :
    // W à 180 + -180 à E
    if ( $this->flag_E_W == true ) {
      $filtre_longitude = " and ( longitude between " . $west_x1000 . " and 181 OR longitude between -181 and " . $east_x1000 . ")";
    } else {
      $filtre_longitude = " and longitude between " . $west_x1000 . " and " . $east_x1000 ; 
    }

    //printf ("Coasts=%s\n", $coasts);
    if ( $coasts == "" ) {
      $filtre_onecoast=" " ;
    } else {
      $filtre_onecoast=" and idcoast= " . $coasts;
    }
    //printf ("West=%f, East=%f, %s\n", $west_x1000, $east_x1000, $filtre_longitude);
      
    // recherche des points de cote du coin

    $query_coast="select idcoast, latitude, longitude from " . $coastline_table .
      " where idcoast >= 0 " .
      $filtre_latitude .
      $filtre_longitude . 
      $filtre_onecoast .
      " order by idcoast,idpoint ;"  ;
    //printf ("DEBUG Requete exécutée : %s \n", $query_coast);

    $result_coast = mysql_query($query_coast) or die("Query [$query_coast] failed \n");
    // for each coast, draw the corresponding coastline

    $idcoast=-1;
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
          
            $this->drawOneCoast($projCallbackLong, $projCallbackLat, $coastpoints_array, $fullres, $coasts);

            unset($coastpoints_array);  // Utile ou pas ? vidage mémoire ?
            $points_count=0;
            $coastpoints_array=array(); 
            $idcoast= $point[0];         
        }
        // + ajout au tableau (dans tous les cas, donc factorisé)

        // latitude
        $y = call_user_func_array( array(&$this, $projCallbackLat), $point[1]*1000 );
        // longitude
        if ( $this->flag_E_W == true && $point[2] < 0 ) {
                      $x = call_user_func_array( array(&$this, $projCallbackLong),360000+$point[2]*1000);
                      //printf ("longitude négative : %f, x=%d\n" , $point[2], $x[$i]);
        } else {
                      $x = call_user_func_array( array(&$this, $projCallbackLong),$point[2]*1000);
        }

        // On prépare un tableau que la fonction drawOneCoast mangera. 
        // Pour le dessin en polygones fermés pleins, on prépare directement la bonne structure
        if ( $fulres == "poly" ) {
             array_push ($coastpoints_array, $x, $y);
        } else {
        // Pour le dessin en polygones fermés vides, on conserve la possibilité d'afficher les numéros de cote
             array_push ($coastpoints_array, array($point[0],$x,$y));
        }

    }

    // En fin de parcours, on appelle la fonction de tracage, qui trace si idcoast != -1 
    $this->drawOneCoast($projCallbackLong, $projCallbackLat, $coastpoints_array, $fullres, $coasts);

    // Puis on supprime le tableau
    unset($coastpoints_array);  // Utile ou pas ? vidage mémoire ?
  }
        



  function drawScale($projCallbackLong, $projCallbackLat)
  {
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

    $map_scale_length =  abs(call_user_func_array( array(&$this, $projCallbackLat), $this->north - $map_size ));
    //imagestring ($this->mapImage, $font, 100,30, $map_scale_length, $this->colorBlack);

    //verticale
    imageLine($this->mapImage, 20, 20,  20, 20 + $map_scale_length , $colorBlack);
    imageLine($this->mapImage, 20, 20,  25, 20 , $colorBlack);
    imageLine($this->mapImage, 20, 20 + $map_scale_length,  25, 20 + $map_scale_length, $colorBlack);

    // indication distance = ortho (0,taille_barre_echelle)
    imagestring( $this->mapImage, $font, 25, 20 + $map_scale_length  , round($ortholength,3) . "nm" , $this->colorBlack);

  }

  function drawRaces($projCallbackLong, $projCallbackLat)
  {
    //draw starting point
    imagerectangle ( $this->mapImage, 
                     call_user_func_array( array(&$this, $projCallbackLong), $this->fullRacesObj->races->startlong) -$this->startSize,
                     call_user_func_array( array(&$this, $projCallbackLat), $this->fullRacesObj->races->startlat) -$this->startSize,
                     call_user_func_array( array(&$this, $projCallbackLong), $this->fullRacesObj->races->startlong) +$this->startSize, 
                     call_user_func_array( array(&$this, $projCallbackLat), $this->fullRacesObj->races->startlat ) +$this->startSize,
                     $this->fromhex("ff0000"));
    if ($this->drawtextwp) {
        imagestring($this->mapImage,
                1,
                call_user_func_array( array(&$this, $projCallbackLong), $this->fullRacesObj->races->startlong) + 2 * $this->startSize,
                call_user_func_array( array(&$this, $projCallbackLat), $this->fullRacesObj->races->startlat) -$this->startSize,
                "Start (" . giveDegMinSec('img', $this->fullRacesObj->races->startlat/1000, 
                                          $this->fullRacesObj->races->startlong/1000) . ")" ,
                $this->fromhex("000000"));
    }

    // Boat , to know about the newt wp ?
    $user=htmlentities($_GET['boat']);
    if ( round($user) != 0 ) {
      if (array_key_exists($user, $this->fullRacesObj->opponents)) {
	$usersObj = &$this->fullRacesObj->opponents[$user];
      } else {
	$usersObj = new users($user);
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
        if ( $waypoint['longitude1'] <0 ) $waypoint[1]+=360000;
        if ( $waypoint['longitude2'] <0 ) $waypoint[3]+=360000;
      } else {
      /* le cas connu ou :
       * - antemeridien non visible (east > west)
       * - on veut tracer un wp sur l'antemeridien...
       * - c'est une gate
       */
        if ( ( $waypoint['longitude1'] <0 ) and ($waypoint['longitude2'] >0 ) and ( $waypoint['wptype'] == WPTYPE_PORTE ) ) {
            $waypoint['longitude2']-=360000;
        } else if ( ( $waypoint['longitude2'] <0 ) and ($waypoint['longitude1'] >0 ) and ( $waypoint['wptype'] == WPTYPE_PORTE )) {
            $waypoint['longitude1']-=360000;
        }  
      }
          
      // bouée sur point 1
      imagefilledellipse($this->mapImage, call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']),
                         call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']),
                         WP_BUOY_SIZE+4, WP_BUOY_SIZE+4, $this->colorBuoy);


      // Coordonnées bouée 1
      if ( $this->drawtextwp && ($this->wp_only == $waypoint_num  || $nwp == $waypoint_num )) {
        imagestring($this->mapImage,
                    3,
                    call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']) ,
                    call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']) ,
                    "WP" . $waypoint_num . "(" .giveDegMinSec('img',$waypoint['latitude1']/1000, $waypoint['longitude1']/1000) . ")",
                    $this->colorBlack);
      }

      // bouée sur point 2 (seulement si PORTE, pas si WP)
      if ( $waypoint['wptype'] == WPTYPE_PORTE ) {
        imagefilledellipse($this->mapImage, call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude2']),
                           call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude2']),
                           WP_BUOY_SIZE+4, WP_BUOY_SIZE+4, $this->colorBuoy);

        if ( $this->drawtextwp && ($this->wp_only == $waypoint_num || $nwp == $waypoint_num )) {
          imagestring($this->mapImage,
                      3,
                      call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude2']) ,
                      call_user_func_array( array(&$this, $projCallbackLat), $waypoint['latitude2']) ,
                      "WP" . $waypoint_num . "(" .giveDegMinSec('img',$waypoint['latitude2']/1000, $waypoint['longitude2']/1000) . ")",
                      $this->colorBlack);
        }
      }

      if ( $this->wp_only == $waypoint_num  || $nwp == $waypoint_num ) {
        if ( $waypoint['wptype'] == WPTYPE_PORTE ) {
          imagesetthickness ( $this->mapImage, WP_THICKNESS);
          imageline ( $this->mapImage, 
                      call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']),
                      call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']),
                      call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude2']),      
                      call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude2']),
                      $this->colorWaypoints);
          imagesetthickness ( $this->mapImage, 1);
        } else {

          // On va tracer un arc de cercle sur les 200 premiers milles, tous les 10 milles
          // giveEndPointCoordinates(  $latitude, $longitude, $distance, $heading  )
          //$style = array ($this->colorWaypoints, $this->colorSea);
          //imagesetstyle ($this->mapImage, $style);
          $poly_coords=array();
          array_push ($poly_coords, call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']),
                      call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']));

          $wpheading=($waypoint['laisser_au']+180)%360;
          $distEP=10  ; $EP_coords=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
	  
          array_push($poly_coords, call_user_func_array( array(&$this, $projCallbackLong), $EP_coords['longitude']),
                     call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords['latitude']));
	  
          imageline ( $this->mapImage, 
                      call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']),
                      call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']),
                      call_user_func_array( array(&$this, $projCallbackLong), $EP_coords['longitude']),      
                      call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords['latitude']),
                      $this->colorBuoy);

          $distEP=500 ; $EP_coords1=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
          array_push($poly_coords, call_user_func_array( array(&$this, $projCallbackLong), $EP_coords1['longitude']),
                     call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords1['latitude']));

          $distEP=2000; $EP_coords2=giveEndPointCoordinates( $waypoint['latitude1'], $waypoint['longitude1'], $distEP, $wpheading );
          array_push($poly_coords, call_user_func_array( array(&$this, $projCallbackLong), $EP_coords2['longitude']),
                     call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords2['latitude']));

          $style = array ($this->colorWaypoints, $this->colorSea);
          imagesetstyle ($this->mapImage, $style);
          imageline ( $this->mapImage, 
                      call_user_func_array( array(&$this, $projCallbackLong), $EP_coords['longitude']),
                      call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords['latitude']),
                      call_user_func_array( array(&$this, $projCallbackLong), $EP_coords2['longitude']),      
                      call_user_func_array( array(&$this, $projCallbackLat),  $EP_coords2['latitude']),
                      IMG_COLOR_STYLED);

          array_push ($poly_coords, call_user_func_array( array(&$this, $projCallbackLong), $waypoint['longitude1']),
                      call_user_func_array( array(&$this, $projCallbackLat),  $waypoint['latitude1']));
	  
	  
          imagefilledpolygon( $this->mapImage, $poly_coords, 5, $this->colorBuoy );

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
          //FIXME: WARNING is untranslated
          //FIXME what is this $font var ? (I guess it's zero, this the font size is 15)
          imagestring ( $this->mapImage, $font+15, $this->xSize/2 -150, 10 , "WARNING : POSITION BLACKOUT IN ACTION", $this->colorWarning);
          imagestring ( $this->mapImage, $font+15, $this->xSize/2 -149, 9 , "WARNING : POSITION BLACKOUT IN ACTION", $this->colorBlack);
      }
      
  } //drawBlackoutWarning      


  /////////////////////Draw boat positions and tracks
  function drawPositions($projCallbackLong, $projCallbackLat, $age, $estime)
  {
 
      if ( $this->list == "" ) { 
          return (0);
      }

      $now=time();
    
      $this->drawBlackoutWarning();

      $mapTools=getUserPref(htmlentities($_GET['boat']),"mapTools") ;
      $num_boats_to_draw=0;

    // Si plus de trop de bateaux... on rend la main tout de suite.
    if ( !idusersIsAdmin(htmlentities($_GET['boat'])) ) {
      if ( count($this->list) > MAX_BOATS_ON_MAPS ) {
        imagestring ( $this->mapImage, $font+5, $this->xSize/2 - 290, $this->ySize/2 , "No boat drawn. Please select no more than " . MAX_BOATS_ON_MAPS . " boats on maps please !" , $this->colorWarning);
        imagestring ( $this->mapImage, $font+5, $this->xSize/2 - 289, $this->ySize/2 , "No boat drawn. Please select no more than " . MAX_BOATS_ON_MAPS . " boats on maps please !" , $this->colorBlack);
        return (0);
      }
    }
    $t_userid = 0;
    if ( isset ($_GET['boat']) ) {
      $t_userid = htmlentities($_GET['boat']);
      if (array_key_exists($t_userid, $this->fullRacesObj->opponents)) {
	$fullUsersObj = new fullUsers($t_userid, $this->fullRacesObj->opponents[$t_userid], $this->fullRacesObj, $this->north, $this->south, $this->west, $this->east, $age);
      } else {
	$fullUsersObj = new fullUsers($t_userid, NULL, $this->fullRacesObj, $this->north, $this->south, $this->west, $this->east, $age);
      }
      // DRAW MyWP
      if ( $fullUsersObj->users->targetlong != 0 && $fullUsersObj->users->targetlat != 0 ) {
        imagefilledellipse($this->mapImage, call_user_func_array( array(&$this, $projCallbackLong), 
                                                                  $fullUsersObj->users->targetlong*1000),
                           call_user_func_array( array(&$this, $projCallbackLat),  
                                                 $fullUsersObj->users->targetlat*1000),
                           WP_BUOY_SIZE, WP_BUOY_SIZE,  $this->fromhex($fullUsersObj->users->color));
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
      } else {
	$usersObj = new users($opponnent);
      }
      // Si le pixel se cache, on passe au suivant
      if ( $usersObj->hidepos > 0 ) continue;

      // Si la couleur est précédée d'un signe "-", on cache la trace (si c'est pas le demandeur, bien sur)
      if ( substr($usersObj->color,0,1) == "-" ) {
        $hidetrack="yes";
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
      //print_r($positions);

      // =======================
      // Tracé de la trajectoire
      // =======================
      $num_segments=0;
      foreach ($positions->records as $posObj) {

        if ( $this->flag_E_W == true && $posObj->long < 0 ) {
          $x=call_user_func_array( array(&$this, $projCallbackLong),$posObj->long + 360000);
        } else {
          $x=call_user_func_array( array(&$this, $projCallbackLong),$posObj->long);
        }
        $y=call_user_func_array( array(&$this, $projCallbackLat), $posObj->lat );
        $positionPx = array( $x, $y );
          
        // draw segment ==> ONLY IF NOT CROSSING DAY CHANGING LINE
        if (   ( $last_longitude != 0 && $posObj->long > 0 ) 
               || ( $last_longitude != 0 && $posObj->long < 0 )  ) {

          if ( $_GET['boat'] == $opponnent || $hidetrack == "no" ) {
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
        ## if ( $_GET['boat'] == $opponnent && $num_segments%12==0 ) {
        if ( $num_segments%12 == 0 && $num_segments >= 12 ) {
          $H = array ( 
                      call_user_func_array( array(&$this, $projCallbackLong), $posObj->long),
                      call_user_func_array( array(&$this, $projCallbackLat), $posObj->lat)
                       );
          $ellipseSz=5;
        } else if ( $num_segments%6 == 0 && $num_segments >= 6 ) {
          $H = array ( 
                      call_user_func_array( array(&$this, $projCallbackLong), $posObj->long),
                      call_user_func_array( array(&$this, $projCallbackLat), $posObj->lat)
                       );
          $ellipseSz=3;
        }          
        //if ( $_GET['boat'] == $opponnent || $hidetrack == "no" ) {
        if ( $_GET['boat'] == $opponnent ) {
          imagefilledellipse($this->mapImage, $H[0], $H[1], 
                             $ellipseSz, $ellipseSz, $this->fromhex( $usersObj->color)  );
        }

        $num_segments++;
              
      } // foreach positions
      // ## Si le tri dans positionsList est DESC
      //$current_long = $positions->records[0]->long;
      //$current_lat  = $positions->records[0]->lat;
      // ## Si le tri dans positionsList est ASC
      $current_long = $posObj->long;
      $current_lat  = $posObj->lat;


      /*
        if ( $_GET['boat'] == $fullUsersObj->users->idusers ) {
        imagestring($this->mapImage, 2, 200 ,60  ,  "LON=" . $fullUsersObj->lastPositions->long . ", LAT=" . $fullUsersObj->lastPositions->lat ,$this->colorText);
        imagestring($this->mapImage, 2, 200 ,90  ,  "PO LON=" . $current_long . ", LAT=" . $current_lat  ,$this->colorText);
        }
      */

      if ( ( $this->flag_E_W !=true  
             and $current_long > $this->west 
             and $current_long < $this->east ) 
           || ( $this->flag_E_W ==true
                and $current_long > $this->west - 360000
                and $current_long < $this->east + 360000 ) 
           ) 
        {
          $num_boats_to_draw++;

          //print_r($fullUsersObj->lastPositions);
          if ($this->west > $this->east ) {
            if ( $current_long < -180000 ) {
              $current_long +=360000;
            } 
            if ( $current_long  > 180000 ) {
              $current_long -=360000;
            } 
          }

          //================================================
          // POSITION Set a black cross un the boat position
          //================================================
          if ( !strcmp($usersObj->color, DEFAULT_SEA_COLOR) ) {
            $usersObj->color = ALTERNATE_SEA_COLOR;
          }
          $A = array ( 
                      call_user_func_array( array(&$this, $projCallbackLong), $current_long),
                      call_user_func_array( array(&$this, $projCallbackLat), $current_lat)
                       );
          // Affichage d'une ellipse en plus de la croix
          imagefilledellipse($this->mapImage, $A[0], $A[1], 
                             POSITIONSIZE/3, POSITIONSIZE/3, $this-> fromhex( $usersObj->color)  );

          imageline ( $this->mapImage, $A[0]-POSITIONSIZE/2, $A[1], $A[0]+POSITIONSIZE/2, $A[1], $this->colorBlack);
          imageline ( $this->mapImage, $A[0], $A[1]-POSITIONSIZE/2, $A[0], $A[1]+POSITIONSIZE/2, $this->colorBlack);
          //imageline ( $this->mapImage, $x-POSITIONSIZE/2, $y, $x+POSITIONSIZE/2, $y, $this->colorBlack);

          // A +360°..
          $B = array ( 
                      call_user_func_array( array(&$this, $projCallbackLong), $current_long+360000),
                      call_user_func_array( array(&$this, $projCallbackLat), $current_lat)
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

          $width  = ImageFontWidth($font) * strlen($usersObj->boatname) + POSITIONSIZE/2 + 2;
          if ($this->text == "left") {
            /* imagestring ( $this->mapImage, $font, $A[0] - $width, $A[1] - ImageFontHeight($font)/2, $fullUsersObj->users->idusers . "(" .$fullUsersObj->users->boatname . ")", $this->colorText); */
            // Label à la position du bateau
            imagestring ( $this->mapImage, $font, $A[0] - $width, $A[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
            imagestring ( $this->mapImage, $font, $B[0] - $width, $B[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
          } else if ($this->text == "right") {
            imagestring ( $this->mapImage, $font, $A[0] + POSITIONWIDTH, $A[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
            imagestring ( $this->mapImage, $font, $B[0] + POSITIONWIDTH, $B[1] - ImageFontHeight($font)/2, "".$usersObj->idusers , $this->colorText);
          }

          // DRAW "MyWP", and merge "Compass" on the map
          if ( $opponnent == htmlentities($_GET['boat']) ) {

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
          if ( $estime != 0 && $usersObj->idusers == htmlentities($_GET['boat']) ) {

            /*
              for ($nvac=0;    
              621               imagefilledellipse($this->mapImage, call_user_func_array( array(&$this, $projCallbackLong),
              622                                                                         $fullUsersObj->users->targetlong*1000),
              623                                                   call_user_func_array( array(&$this, $projCallbackLat),
              624                                                                         $fullUsersObj->users->targetlat*1000),
              625                                   WP_BUOY_SIZE, WP_BUOY_SIZE,  $this->fromhex($fullUsersObj->users->color));
            */

            /* 2008/01/27 : expression de l'estime en temps plutot qu'en distance  */
            $Estime=giveEndPointCoordinates(  $current_lat,
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
            $E = array ( 
                        call_user_func_array( array(&$this, $projCallbackLong), $Estime['longitude']),
                        call_user_func_array( array(&$this, $projCallbackLat), $Estime['latitude'])
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
  function drawRealBoatPositions($projCallbackLong, $projCallbackLat)
  {
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
           $xlogo = call_user_func_array(array(&$this, $projCallbackLong), $boat[2] + 360000 ) ;
        } else {
           $xlogo = call_user_func_array(array(&$this, $projCallbackLong), $boat[2]) ;
        }
        $ylogo = call_user_func_array(array(&$this, $projCallbackLat), $boat[1]) ;
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

        } elseif ( $boat[0] >= -11 ) {
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
          // Pas de logo disponible, on dessine des bulles
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

          //if ( $boat[0] == -3 ) {
          //    $boat[0] = "G3";
          //} else {
          //    $boat[0] = -$boat[0];
          //}
        }
        imagestring ( $this->mapImage, $font+1, $xlogo-4, $ylogo-3, $boatname , $numcolor);
      }

    } // foreach opponent
  }


  /////////////////////Draw boat positions and tracks

  /////////////////////Draw boat positions and tracks
  function drawOrtho($projCallbackLong, $projCallbackLat, $estime )
  {
    $opponent=$_GET['boat'];
    if ( intval($opponent)  == 0 ) return(0);

    //opponent is a userid, get a fulluser
    if (array_key_exists($opponent, $this->fullRacesObj->opponents)) {
      $fullUsersObj = new fullUsers($opponent, $this->fullRacesObj->opponents[$opponent], $this->fullRacesObj, $this->north, $this->south, $this->west, $this->east, $age);
    } else {
      $fullUsersObj = new fullUsers($opponent, NULL, $this->fullRacesObj, $this->north, $this->south, $this->west, $this->east, $age);
    }
    // Dessin de la trajectoire correspondant aux premiers pas de temps de la route ortho (morceaux de 10 milles)
    if ( $fullUsersObj->users->idusers == htmlentities($_GET['boat']) ) {
      // Des traces de ORTHOSTEP milles pour l'ortho
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


        $style = array ($this-> fromhex( $fullUsersObj->users->color), 
                        $this->colorSea);
        imagesetstyle ($this->mapImage, $style);
        $A = array ( 
                    call_user_func_array( array(&$this, $projCallbackLong), $fullUsersObj->lastPositions->long),
                    call_user_func_array( array(&$this, $projCallbackLat), $fullUsersObj->lastPositions->lat)
                     );
        $B = array ( 
                    call_user_func_array( array(&$this, $projCallbackLong), $fullUsersObj->lastPositions->long+360000),
                    call_user_func_array( array(&$this, $projCallbackLat), $fullUsersObj->lastPositions->lat)
                     );
        if ( $this->am_on_map == true ) {
          if ( $fullUsersObj->lastPositions->long <0 )  {
            $DepOrtho=$B;
          } else { 
            $DepOrtho=$A;
          }
        } else {
          $DepOrtho=$A;
        }

        $E = array ( 
                    call_user_func_array( array(&$this, $projCallbackLong), $Estime['longitude']),
                    call_user_func_array( array(&$this, $projCallbackLat), $Estime['latitude'])
                     );
        imageline ( $this->mapImage, $DepOrtho[0], $DepOrtho[1], $E[0], $E[1] , IMG_COLOR_STYLED);
        //imageline ( $this->mapImage, $A[0], $A[1], $E[0], $E[1] , $this->colorTextOrtho);

        $np++;
      }

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
  function drawExcludedPositions($projCallbackLong, $projCallbackLat, $idraces, $idusers, $age)
  {
    //get a user, get a fulluser
    $excludedUsersObj = new excludedUsers($idusers, $idraces);
    $A = array ( 
                call_user_func_array( array(&$this, $projCallbackLong), $excludedUsersObj->lastPositions->long),
                call_user_func_array( array(&$this, $projCallbackLat), $excludedUsersObj->lastPositions->lat)
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
  function drawWind($projCallbackLong, $projCallbackLat, $drawwind = 0)
  {

    $now = time();
    imagestring($this->mapImage, 5, 350, $this->ySize-20, "Wind : ".gmdate("Y/m/d H:i", $now + $drawwind) . " GMT", $this->colorText);
    foreach( $this->gridListObj->records as $fullGridObj)
      {

        // Si pas de vent, pas de vecteur à représenter.
        // On ne dessine le vecteur que si vent > 0.1 kts.
        if ( $fullGridObj->wspeed > 1 ) {

          // ===
          
          if ( $this->flag_E_W == true && $fullGridObj->Long < 0 ) {
            $x = call_user_func_array( array(&$this, $projCallbackLong),360000 + $fullGridObj->Long);
          } else {
            $x = call_user_func_array( array(&$this, $projCallbackLong), $fullGridObj->Long);
          }
          $y = call_user_func_array( array(&$this, $projCallbackLat), $fullGridObj->Lat);
          $Tbl = array ( $x, $y );
        
          //get Wind heading and draw a triangle
          $coordi = windtrianglecoordinates($Tbl, $fullGridObj->wheading, 
                                            $fullGridObj->wspeed);
          $col = windspeedtocolorbeaufort($fullGridObj->wspeed, $this->mapImage);
          imagefilledpolygon( $this->mapImage, 
                              $coordi,
                              4, $col);
    
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
  function drawSegment($projCallbackLong, $projCallbackLat, $segCoords, $segColor, $segPoint = true)
  {

    $y1 = call_user_func_array( array(&$this, $projCallbackLat),  $segCoords[0]*1000);
    $x1 = call_user_func_array( array(&$this, $projCallbackLong), $segCoords[1]*1000);

    $y2 = call_user_func_array( array(&$this, $projCallbackLat),  $segCoords[2]*1000);
    $x2 = call_user_func_array( array(&$this, $projCallbackLong), $segCoords[3]*1000);
        
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
