<?php
include_once("includes/header.inc");
include_once("config.php");
//include_once("includes/strings.inc");

/*
 output des IC
 */
function outputIC($fullRacesObj, $lang="fr") {
    foreach($fullRacesObj->races->ics as $ic) {
        if ($ic['flag'] & IC_FLAG_VISIBLE) {
            echo "<div class="ic">";
            echo nl2br($ic['instructions']);
            echo "</div>";
        }
    }

/*
 output de la racemap
 */

function outputRaceMap($fullRacesObj, $lang="fr") {

    $href = "images/racemaps/regate".$fullRacesObj->races->idraces.".jpg";
    if ( file_exists($href) ) {
        echo "<img src=\"$ref\" alt=\"" .$strings[$lang]["racemap"]. "\" />";
    }
}

function outputRaceTitle($fullRacesObj, $lang="fr") {
    printf("<h3>".$strings[$lang]["racestarted"]."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));     
}

if ($idraces != 0) {

    $fullRacesObj = new fullRaces($idraces);
    echo "<div id=\"raceheader\">";
        outputRaceTitle($fullRacesObj, $lang);
    echo "</div>";

    // Carte de la course
    echo "<div id=\"racemap\">";
        outputRaceMap($fullRacesObj, $lang);
    echo "</div>";

    echo "<div id=\"ic\">";
        outputIC($fullRacesObj, $lang);
    echo "</div>";    
}

include_once("includes/footer.inc");
?>
