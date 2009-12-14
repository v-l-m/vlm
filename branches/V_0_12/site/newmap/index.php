<html>
<head>
<title>Si tu tournes la molette, tu zoomes... Joyeux Noel Hugues (et cliques un peu sur les chiffres... !)</title>
<script src="simpleajax.js"></script>
<script src="mousewheel.js"></script>

</head>
<body>

<?php

    define(DEFAULT_MAPWIDTH, 900);
    define(DEFAULT_MAPHEIGHT, 600);
    define(DEFAULT_MAPLAT, 47.75);
    define(DEFAULT_MAPLON, -3.75);
    define(DEFAULT_MAPZOOM, 7);

    $zoomlevels=array(
      0  => 0.05,
      1  => 0.1,
      2  => 0.2,
      3  => 0.4,
      4  => 0.8,
      5  => 1.5,
      6  => 3,
      7  => 6,
      8  => 12,
      9  => 24,
      10 => 35
                );

    $lat=$_REQUEST["lat"];
    $lon=$_REQUEST["long"];

    if ( ! isset($_REQUEST["zoom"]) ) {
           $zoom=DEFAULT_MAPZOOM;
    } else {
           $zoom=abs(round($_REQUEST["zoom"]));
    }
    if ( $zoom > 20 ) $zoom=20;

    $maparea=$zoomlevels[$zoom];
    $maparea=$zoom;

    $x=$_REQUEST["x"];
    $y=$_REQUEST["y"];
    if ( empty($x) ) $x=DEFAULT_MAPWIDTH;
    if ( empty($y) ) $y=DEFAULT_MAPHEIGHT;
 
    $lat=$_REQUEST["lat"];
    $lon=$_REQUEST["lon"];
    if ( empty($lat) ) $lat=DEFAULT_MAPLAT;
    if ( empty($lon) ) $lon=DEFAULT_MAPLON;

    echo "<script type=\"text/javascript\">
            var zoom = " . $zoom . "
            var x = " . $x . "
            var y = " . $y . "
            var lat = " . $lat . "
            var lon = " . $lon . "
          </script>";

    $proj=$_REQUEST["proj"];

    $tracks=$_REQUEST["tracks"];
    $idraces=$_REQUEST["idraces"];
    $boat=$_REQUEST["boat"];
    $age=$_REQUEST["age"];
    $text=$_REQUEST["text"];

    $maptype=$_REQUEST["maptype"];
    $wp=$_REQUEST["wp"];

    $baseurl="/mercator.img.php?lat=".$lat."&long=".$lon."&text=right&x=".$x."&y=".$y."&proj=mercator";
    $url=$baseurl . "&idraces=20071111&tracks=on&maptype=compas&list=myboat&drawwind=no&boat=45&age=0";

    // Le DIV contentant le fond de carte (Grille + trait de cote MAIS PAS LE VENT (drawwind=-1))
    echo " <div id=map  style=\" position:absolute; 
         top:20px; 
         left:30px; 
         width:".$x."px; 
         height:".$y."px; 
         background-repeat:no-repeat; 
         background-image:url(".$url."&maparea=".$maparea."&drawwind=-1)
         \">
          </div>";

    // Le DIV contenant le vent
    echo " <div id=wind style=\" position:absolute; 
         top:20px; 
         left:30px; 
         width:".$x."px; 
         height:".$y."px; 
         background-repeat:no-repeat;
         \">&nbsp;</div>";

    /*
    echo "<script language=\"javascript\">";
    echo "     var path_png = DisplayPngByBrowser(navigator.appName, ' " . $URL_TS . "', " . $x . ", " . $y.");";
    echo "</script>";
    */

    // Le DIV de controle 
    echo " <div id=control style=\" position:absolute; 
         top:20px; left:0px;\">";
    echo "<table>";
    echo "H+<BR>";
    for ( $i=0 ; $i<=32; $i+=1) {
        $windurl=$baseurl . "&drawwind=".$i."&windonly=true&maille=1&seacolor=transparent&maparea=".$maparea;

  //echo "<A onclick=\"setBackgroundImage('wind', '".$windurl."', '.$x.' , '.$y.' )\"><tr bgcolor=#A0A0E0><td>".$i."</td></tr></A>\n";
  echo "<A onclick=\"setBackgroundImage('wind', '".$windurl."', '.$x.' , '.$y.' )\">" .  sprintf("%02d",$i) . "</a><BR>";
    }
    echo "</table>";

    // Ce div contient aussi le niveau de zoom souhaité
    echo "<form name=parameters><input type=hidden name=zoom value=".$zoom."></form>\n";
    echo "
   <form name=\"Show\">
   <input type=button name=\"MouseX\" value=\"0\" size=\"4\"><br>
   <input type=button name=\"MouseY\" value=\"0\" size=\"4\">
   </form>
     <script src=\"mousemove.js\"></script>";
    echo " </div>";
     

?>
</body>
</html>


