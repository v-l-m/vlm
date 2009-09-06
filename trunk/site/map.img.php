<?php
    session_start();
    include_once("config.php");
    include_once("functions.php");
    include_once("mapfunctions.php");

    // Test si connecté ou pas.
    $idusers = getLoginId() ;
    if ( empty($idusers) || $idusers != htmlentities($_GET['boat']) ) {
        // Récupération des dimensions (x et y) : valeurs mini par défaut = 250
        $x=500;
        $y=250;
    
        $im = @imagecreate($x, $y)
              or die("Cannot Initialize new GD image stream");
        $blanc = imagecolorallocate($im, 255, 255, 255);
        $noir = imagecolorallocate($im, 0, 0, 0);
    
        // Affichage d'un "-X-" au milieu de l'image
    
        imagestring($im, 5, 20, $y/2,  "You should not do that...your IP : " . $_SERVER["REMOTE_ADDR"] , $noir);
        imagestring($im, 5, 20, $y/2+20,  "Connected : ".$idusers ." is not BOAT=(".$_GET['boat'].")" , $noir);
        imagestring($im, 3, 20, $y/2+40,  "Asking a map for a boat= that is not yours changes the user's prefs" , $noir);
        imagestring($im, 3, 20, $y/2+60,  "SRV = " . SERVER_NAME , $noir);
    
        // Affichage de l'image
        header("Content-type: image/png");
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    $maptype= htmlentities($_GET['maptype']);

    $list= htmlentities($_GET['list']) ;
        
    $maparea= htmlentities($_GET['maparea']);
    if ( $maparea == "" ) {
        $maparea=round(MAPAREA_MAX/2);
    } else {
        if ($maparea <MAPAREA_MIN ) $maparea=MAPAREA_MIN;
    }
    if ($maparea >MAPAREA_MAX ) $maparea=MAPAREA_MAX; {
        setUserPref(htmlentities($_GET['boat']), "maparea" , $maparea);
    }
    $maille= htmlentities($_GET['maille']);
    if ( $maille == "" ) {
        $maille=round(MAILLE_MAX/2);
    } else {
        if ($maille <MAILLE_MIN ) $maille=MAILLE_MIN;
    }
    if ($maille >MAILLE_MAX ) $maille=MAILLE_MAX;
    setUserPref(htmlentities($_GET['boat']), "mapMaille" , $maille);

    $idraces= htmlentities($_GET['idraces']) ;
    //if ( $idraces == 20081109 ) $list = "myboat";

    $boat= htmlentities($_GET['boat']) ;
    $save= htmlentities($_GET['save']) ;
    $tracks= htmlentities($_GET['tracks']) ;
    if ( $tracks == "" ) $tracks = "on";

    $x= htmlentities($_GET['x']) ;
    if ( $x == "" ) $x = 800;

    $y= htmlentities($_GET['y']) ;
    if ( $y == "" ) $y = 600;

    // Limitation de la taille de la carte pour pas péter le serveur
    if ( $x > MAX_MAP_X ) $x=MAX_MAP_X;
    setUserPref(htmlentities($_GET['boat']), "mapX" , $x);
  
    if ( $y > MAX_MAP_X ) $y=MAX_MAP_X;
    setUserPref(htmlentities($_GET['boat']), "mapY" , $y);
  
    $age= htmlentities($_GET['age']) ;
    if ( $age == "" ) $age = 2;
    setUserPref(htmlentities($_GET['boat']), "mapAge" , $age);
  
    $estime= htmlentities($_GET['estime']) ;
    if ( $estime == "" ) $estime = 30;
    setUserPref(htmlentities($_GET['boat']), "mapEstime" , $estime);
  
    $proj= htmlentities($_GET['proj']) ;
    //  $proj="carre"; 
  
    $text= htmlentities($_GET['text']) ;
    if ( $text == "" ) $text = "right";
  
    $windtext= htmlentities($_GET['windtext']) ;
    if ( $windtext == "" ) $windtext = "on";
  
    // Guess real map coordinates

?>
<html>
  <head>
    <title>VLM Map (<?php echo $idusers; ?>)</title>
    <link rel="stylesheet" type="text/css" href="style/<?php echo getTheme(); ?>/style.css" />
    <script>
        clicEnCours = false;
        position_x = 250 ; 
        position_y = 150 ;
        netscape = false;
        if (navigator.appName.substring(0,8) == "Netscape") {
            netscape = true;
        }
        msiesix = /MSIE 6/i.test(navigator.userAgent);
  
        function DisplayPngByBrowser ( browser, img_path, width, height )
        {
            var png_path;
            if (msiesix) {
                document.write('<img id="dynimg" src="images/site/blank.gif" style="width:'+width+'px; height:'+height+'px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''+img_path+'\', sizingMethod=\'scale\');" >');
            } else if (netscape) {
                document.write('<img id="dynimg" src="'+img_path+'" />');
            } else {
                document.write('<img id="dynimg" src="'+img_path+'" />');
            }
        }

        function boutonPresse()
        {
            origine_x = x - position_x;
            origine_y = y - position_y;
            clicEnCours = true;
        }
  
        function boutonRelache()
        {
            clicEnCours = false;
        }
  
        function deplacementSouris(e)
        {
            x = (netscape) ? e.pageX : event.x + document.body.scrollLeft;
            y = (netscape) ? e.pageY : event.y + document.body.scrollTop;
          
            if (clicEnCours && document.getElementById) {
                position_x = x - origine_x;
                position_y = y - origine_y;
                document.getElementById("deplacable").style.left = position_x ;
                document.getElementById("deplacable").style.top = position_y ;
            }
        }
  
        //FIXME : Useless ?
        function previousTimestamp()
        {
            vts=document.control.vts.value;
            if ( vts >0 ) vts--;
            document.control.vts.value=vts;
            //showvts();
            for (ts=0; ts<=24 ; ts++) {
                vt=eval("ts" . ts);
                document.getElementById(vt).style.display = 'none' ;
            }
            vt=eval("ts" . document.control.vts.value);
            document.getElementById(vt).style.display = '' ;
        }

        function nextTimestamp()
        {
            vts=document.control.vts.value;
            if ( vts <24 ) vts++;
            document.control.vts.value=vts;
            showvts();
        }

        if (netscape) {
            document.captureEvents(Event.MOUSEMOVE);
        }
  
        document.onmousemove = deplacementSouris;
    </script>
  </head>
  <body id="mapbody">

    <?php
      // Sauvegarde des préférences
      setUserPref(htmlentities($_GET['boat']), "mapOpponents" , htmlentities($_GET['list']));

      $maplayers=htmlentities($_GET['maplayers']);
      if ( $maplayers == "" ) $maplayers = "merged";
      setUserPref(htmlentities($_GET['boat']), "mapLayers" , $maplayers);

      $mapcenter=htmlentities($_GET['mapcenter']);
      if ( $mapcenter == "" ) $mapcenter = "myboat";
      setUserPref(htmlentities($_GET['boat']), "mapCenter" , $mapcenter);

      // Centrage de la carte
      // Coordonnées bateau
      $long= htmlentities($_GET['long']);
      $lat= htmlentities($_GET['lat']);

      // Coordonnées WP
      $longwp=htmlentities($_GET['longwp']);
      $latwp= htmlentities($_GET['latwp']);

      // Si centrage sur la route, on moyenne long/longwp et lat/latwp
      // Sinon rien, long/lat sont le centre de la carte
      if ( $mapcenter == "roadtowp" ) {
          $long = ($long + $longwp)/2;
          $lat = ($lat + $latwp)/2;
      } else if ( $mapcenter == "mywp" ) {
             $long = $longwp;
             $lat  = $latwp;
      } // else lat/long = ceux qu'on a déjà (position du bateau)

      $query_string_base="lat=". $lat . "&" . 
          "long=". $long . "&" . 
          "x=". $x . "&" . 
          "y=". $y . "&" . 
          "maparea=". $maparea . "&" .
          "maille=". $maille . "&" .
                "idraces=". $idraces . "&" . 
          "proj=". $proj  ; 
        $query_string = $query_string_base . "&" . 
          "seacolor=e0e0f0". "&" . 
          "tracks=". $tracks . "&" . 
          "age=". $age . "&" . 
          "estime=". $estime . "&" . 
          "list=". $list . "&" . 
          "boat=". $boat . "&" . 
          "text=". $text ;

    // **** And now, draw the map **** 
          
      if ( $maplayers == "merged" ) {
          $URL_MAP=MAP_SERVER_URL . "/mercator.img.php?drawortho=yes&drawwind=0&" . $query_string  ;
          echo "<img src=\"$URL_MAP\">";
      } else {
          $URL_MAP=MAP_SERVER_URL . "/mercator.img.php?drawortho=yes&drawwind=-1&" . $query_string  ;

          // **** DRAW  WIND MAPS **** 
          $timestamp=0;

          $URL_TS=MAP_SERVER_URL . "/mercator.img.php?" ;
          $URL_TS.="drawgrid=no&drawmap=no&drawrace=no&drawscale=no";
          $URL_TS.="&drawpositions=no&drawlogos=no&drawlibelle=no&drawortho=no";
          $URL_TS.="&seacolor=transparent";
          $URL_TS.="&". $query_string_base ;
          $URL_TS_BASE = $URL_TS;
          $URL_TS.="&drawwind=".$timestamp;

          echo "<div id=ts".$timestamp." style=\"top:10; left:10; position:absolute; background-image:url(".$URL_MAP.");\">";
          echo "  <script language=\"javascript\">";
          echo "      var path_png = DisplayPngByBrowser(navigator.appName, ' " . $URL_TS . "', " . $x . ", " . $y.");";
          echo "  </script>";
          echo "</div>";
      ?>

      <div id="windcontrollayer" class="boxonmap">

        <script language="javascript">
            <?php echo "    var url_ts_base = '".$URL_TS_BASE."';"; ?>
            function enterOffset(event)
            {
                if (event && event.keyCode == 13) {  
                    updateOffset();
                }
            }

            function updateOffset()
            {
                var path = url_ts_base+'&drawwind='+document.getElementById('griboffset').value;
                document.getElementById('dynimg').src = path;
            }

            function nextOffset()
            {
                document.getElementById('griboffset').value++;
                updateOffset();
            }

            function prevOffset()
            {
                document.getElementById('griboffset').value--;
                updateOffset();
            }

        </script>
        <input id="prevgriboffset" size="2" value="-" class="controlonmap" type="button" onClick="javascript:prevOffset();" />
        <input id="griboffset"  class="controlonmap" type="text" maxlength="2" size="2" value="0" onKeyPress="javascript:enterOffset(event);"/>h
        <input id="nextgriboffset" size="2" value="+" class="controlonmap" type="button" onClick="javascript:nextOffset();" />

   <?php
      }

      // ****  Le compas deplacable en dernier, sinon il est dessous.. *** 
      // Que met t'on sur la carte ?
      if ( $maptype == "floatingcompas" || $maptype == "bothcompass" ) {
          setUserPref(htmlentities($_GET['boat']), "mapTools" , $maptype);
          echo "<div id=\"deplacable\" onMouseDown=\"boutonPresse()\" onMouseUp=\"boutonRelache()\"><img src=\"images/site/compas-transparent.gif\"></div>";
      } else if ( $maptype == "compas" ) {
          setUserPref(htmlentities($_GET['boat']), "mapTools" , "compas");
      } else {
          setUserPref(htmlentities($_GET['boat']), "mapTools" , "none");
      }
    ?>


  </body>
</html>
