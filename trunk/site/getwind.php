<?php
  include_once("config.php");
  date_default_timezone_set('UTC');
  $lat=htmlentities(quote_smart($_REQUEST['lat']))*1000;
  $lon=htmlentities(quote_smart($_REQUEST['lon']))*1000;
  $time=htmlentities(quote_smart($_REQUEST['time']));
  $period=htmlentities(quote_smart($_REQUEST['period']));

  if ( round($time) < 3600 ) {
       echo "Usage : http://vlm.../getwind.php?lat=latitude&lon=longitude&time=howlong_in_seconds&period=how_often_in_seconds<BR>";
       echo "Exemple : http://vlm.../getwind.php?lat=47.7216&lon=-3.369579&time=3600&period=600<BR>";
       exit;
  }
  $now=time();

  printf ('<h4>Prevision pour la position Lat=%f, Long=%f ( %s )</h4>' , 
               $lat/1000, $lon/1000 , giveDegMinSec ('html', $lat/1000, $lon/1000)) ;
  printf ("Time is UTC, Wind Speed unit : kts, Wind Dir is where the wind comes from<br>");

  // OLD Version
  //echo "la future ancienne version de windAtPosition() donne ceci pour maintenant<BR>";
  $wind=windAtPosition($lat,$lon,$ti,'OLD');
  echo "<H1>Fonction actuelle, table wind</H1>";
  echo "<code>";
  printf ('%s;Speed=%4.1f;Dir=%003d<BR>' , 
  	   gmdate("Y/m/d H:i:s", $now+$ti), $wind[0], ($wind[1]+180)%360 ) ;
  echo "</code>";


exit;

 
  //echo "la nouvelle version de windAtPosition() pour ... les bientot nouvelles cartes ... donne ceci<BR>";
  echo "<H1>Future/nouvelle fonction, table winds</H1>";
  echo "<code>";
  for ( $ti=0-24*$period ; $ti<=$time ; $ti+=$period) {
        $wind=windAtPosition($lat,$lon,$ti,'NEW');
        //printf ('Lat=%f, Long=%f, Time=%s    TWS=%5.2f;TWD=%5.2f<BR>' , 
	//             $lat/1000, $lon/1000, gmdate("Y/m/d H:i:s", $now+$ti), $wind[0], ($wind[1]+180)%300 ) ;
        printf ('%s;Speed=%04.1f;Dir=%003d<BR>' , 
	            gmdate("Y/m/d H:i:s", $now+$ti), $wind[0], ($wind[1]+180)%360 ) ;
  }
  echo "</code>";
 
//  $wind=OLDwindAtPosition($lat,$lon,$time);
//  printf ('<BR>OLD = TWS=%5.2f;TWD=%5.2f<BR>' , $wind[0], $wind[1] ) ;
?>
  
