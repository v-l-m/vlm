<?php
  include_once("config.php");
  date_default_timezone_set('UTC');
  $lat=htmlentities(quote_smart($_REQUEST['lat']))*1000;
  $lon=htmlentities(quote_smart($_REQUEST['lon']))*1000;
  $time=htmlentities(quote_smart($_REQUEST['time']));
  $now=time();

  printf ('<h4>Prevision pour la position Lat=%f, Long=%f ( %s )</h4>' , 
               $lat/1000, $lon/1000 , giveDegMinSec ('html', $lat/1000, $lon/1000)) ;
  printf ("Time is UTC (%d), Wind Speed unit : kts, Wind Dir is where the wind comes from<br>", $now+$time);

  $wind=windAtPosition($lat,$lon,$time,'OLD');
  echo "<H1>Fonction actuelle, table wind</H1>";
  echo "<code>";
  printf ('%s;Speed=%4.1f;Dir=%4.1f<BR>' , 
       gmdate("Y/m/d H:i:s", $now+$time), $wind['speed'], fmod($wind['windangle']+180., 360.) ) ;
  echo "</code>";

?>
  
