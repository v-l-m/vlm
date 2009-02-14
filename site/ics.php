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
            echo "<div class=\"ic\">";
            echo nl2br($ic['instructions']);
            echo "</div>";
        }
    }

}

/*
 output de la racemap
 */

function outputRaceMap($fullRacesObj, $alttemplate) {

    $href = "images/racemaps/regate".$fullRacesObj->races->idraces.".jpg";
    if ( file_exists($href) ) {
        echo "<img src=\"$ref\" alt=\"" .$alttemplate. "\" />";
    }
}

function outputRaceTitle($fullRacesObj, $titletemplate = "%s / %s") {
    printf("<h3>".$titletemplate."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));     
}

if ($idraces != 0) {

    $fullRacesObj = new fullRaces($idraces);
    echo "<div id=\"raceheader\">";
        outputRaceTitle($fullRacesObj, $strings[$lang]["racestarted"]);
    echo "</div>";

    // Carte de la course
    echo "<div id=\"racemap\">";
        outputRaceMap($fullRacesObj, $strings[$lang]["racemap"]);
    echo "</div>";

    echo "<div id=\"ic\">";
        outputIC($fullRacesObj);
    echo "</div>";    
}

include_once("includes/footer.inc");
?>
