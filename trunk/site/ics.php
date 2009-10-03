<?php
include_once("includes/header.inc");
include_once("config.php");
//include_once("includes/strings.inc");

/*
  output des IC
*/
function outputIC($fullRacesObj) {
  foreach($fullRacesObj->races->getICS() as $ic) {
    if ($ic['flag'] & IC_FLAG_VISIBLE) {
      echo "<div class=\"icbox\">\n";
      if ($ic['flag'] & IC_FLAG_LINKFORUM) {
	printf ("<a href=\"".$ic['instructions']."\" target=_ic><b>INSTRUCTIONS DE COURSE SUR LE FORUM / RACE INSTRUCTIONS ON THE FORUM</b></a>\n");
      } else {
	echo nl2br($ic['instructions']);
      }
      echo "\n</div>\n";
    }
  }
}

/*
  output de la racemap
*/

function outputRaceMap($fullRacesObj, $alttemplate) {

  echo htmlTinymap($fullRacesObj->races->idraces, $alttemplate);

}

/* output du titre */

function outputRaceTitle($fullRacesObj, $titletemplate = "%s / %s") {
  printf("<h3>".$titletemplate."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));     
}

/* output de la polaire */

function outputRacePolar($fullRacesObj, $title) {
  printf("<h3>" . $title . "&nbsp;:&nbsp;<a href=\"speedchart.php?boattype=" . $fullRacesObj->races->boattype . "\" target=\"_speedchart\" rel=\"nofollow\">" . substr($fullRacesObj->races->boattype,5) . "</a></h3>");     
}

/* output du tableau de wp */

function outputWayPoints($fullRacesObj, $startstring) {

  echo "<table class=\"waypoints\">\n";
  //echo "<tr><th>#</th><th>Lat1</th><th>Lon1</th><th>Lat2</th><th>Lon2</th><th>Hdg</th><th>Type</th><th>Name</th></tr>";
  echo "<tr><th>#</th><th>Lat1</th><th>Lon1</th><th>Lat2</th><th>Lon2</th><th>Type</th><th>Name</th></tr>";

  echo "<tr>\n";
  echo "<td>WP0</td>"; 
  //printf("<td>%.3f</td><td>%.3f</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>%s</td><td>&nbsp;</td>", $fullRacesObj->races->startlat/1000., $fullRacesObj->races->startlong/1000., $startstring);
  printf("<td>%.3f</td><td>%.3f</td><td>&nbsp;</td><td>&nbsp;</td><td>%s</td><td>&nbsp;</td>", $fullRacesObj->races->startlat/1000., $fullRacesObj->races->startlong/1000., $startstring);
  echo "</tr>\n";

  foreach ($fullRacesObj->races->getWPs() as $num => $wp) {
    echo "<tr>\n";
    echo "<td>WP".$num."</td>";
    //printf("<td>%.3f</td><td>%.3f</td><td>%.3f</td><td>%.3f</td><td>%.1f</td><td>%s</td><td>%s</td>", $wp[0]/1000., $wp[1]/1000., $wp[2]/1000., $wp[3]/1000., $wp[4], $wp[5], $wp[6]);
    printf("<td>%.3f</td><td>%.3f</td><td>%.3f</td><td>%.3f</td><td>%s</td><td>%s</td>", 
	   $wp['latitude1']/1000., $wp['longitude1']/1000., 
	   $wp['latitude2']/1000., $wp['longitude2']/1000.,  $wp['wptypelabel'], htmlentities($wp['libelle']));
    echo "</tr>\n";
  }
  echo "</table>\n";
}

if ($idraces != 0) {

  $fullRacesObj = new fullRaces($idraces);
  echo "<div id=\"raceheader\">\n";
  outputRaceTitle($fullRacesObj, $strings[$lang]["racestarted"]);
  outputRacePolar($fullRacesObj, $strings[$lang]["boattype"]);
  echo "<h3><a href=\"races.php?type=racing&lang=".$lang."&idraces=".$idraces."\">".$strings[$lang]["ranking"]."</a></h3>";
  echo "</div>\n";

  // Carte de la course
  echo "<div id=\"racemap\">\n";
  outputRaceMap($fullRacesObj, $strings[$lang]["racemap"]);
  echo "</div>\n";

  echo "<div id=\"ic\">\n";
  echo "<h3>".$strings[$lang]["ic"]."</h3>\n";
  outputIC($fullRacesObj);
  echo "</div>\n";    

  echo "<div id=\"waypoints\">\n";
  echo "<h3>Waypoints</h3>\n";
  outputWaypoints($fullRacesObj, $strings[$lang]["startmap"]);
  echo "</div>\n";    

}

include_once("includes/footer.inc");
?>
