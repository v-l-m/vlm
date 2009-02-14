<?php
include_once("includes/header.inc");
include_once("config.php");
//include_once("includes/strings.inc");

/*
 output des IC
 */
function outputIC($fullRacesObj) {
    foreach($fullRacesObj->races->ics as $ic) {
        if ($ic['flag'] & IC_FLAG_VISIBLE) {
            echo "<div class=\"icbox\">\n";
            echo nl2br($ic['instructions']);
            echo "\n</div>\n";
        }
    }

}

/*
 output de la racemap
 */

function outputRaceMap($fullRacesObj, $alttemplate) {

    $href = "images/racemaps/regate".$fullRacesObj->races->idraces.".jpg";
    if ( file_exists($href) ) {
        echo "<img src=\"$href\" alt=\"" .$alttemplate. "\" />\n";
    }
}

/* output du titre */

function outputRaceTitle($fullRacesObj, $titletemplate = "%s / %s") {
    printf("<h3>".$titletemplate."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));     
}

/* output du tableau de wp */

function outputWayPoints($fullRacesObj) {

    echo "<table class=\"waypoints\">\n";
    foreach ($fullRacesObj->races->waypoints as $wp) {
        echo "<tr>\n";
        foreach ($wp as $dat) {
            echo "<td>$dat</td>\n";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
}

if ($idraces != 0) {

    $fullRacesObj = new fullRaces($idraces);
    echo "<div id=\"raceheader\">\n";
        outputRaceTitle($fullRacesObj, $strings[$lang]["racestarted"]);
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
        outputWaypoints($fullRacesObj);
    echo "</div>\n";    

}

include_once("includes/footer.inc");
?>
