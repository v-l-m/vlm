<?php
include_once("header.inc");
include_once("config.php");

$fullUsersObj = new fullUsers(getLoginId());

// If engaged on a race, display Warning
if ($fullUsersObj->users->engaged  != 0)
{
    echo "<h2>". $strings[$lang]["warning"]."</h2>";

    $racesListObj = new racesList();
    foreach($racesListObj->records as $racesObj) {
        if (  $racesObj->races->idraces == $fullUsersObj->users->engaged ) {
            echo "<p>Engaged on Race : <b>" .     $fullUsersObj->users->engaged .  " (" .
                 $racesObj->races->racename    .  ") " .
                 "</b></p>";
        }
    }

    // The user may want to unsubscribe from this race
?>
  <form action="subscribe_race.php">
     <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
     <input type="hidden" name="type" value="unsubscribe" />
     <input type="hidden" name="lang" value="<?php echo $lang?>" />
     <input type="submit" value="<?php echo $strings[$lang]["unsubscribe"]?>" />
     <p>
     <?php echo $strings[$lang]["wanttosubscribe"]?>
     </p>
  </form>

<?php

// Else display list of available races
} else {

    //select races not started or permanent and available for this boat
    $races=availableRaces($fullUsersObj->users->idusers);
    //print_r($races);
    $select_list="";
    foreach($races as $race) {
        $racesObj=new races($race);
        //printf("R=%d\n",$race);
        $select_list = $select_list . "<option value=\"". $racesObj->idraces . "\">". $racesObj->idraces . " - " . $racesObj->racename."</option>";
    }

    if ( $select_list != "" ) {
        echo "<h1>".$strings[$lang]["sub_race"]."</h1>";
?>
  <form action="myboat.php">
    <select name="idraces">
        <?php echo $select_list; ?>
    </select>
    <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
    <input type="hidden" name="type" value="subscribe"/>
    <input type="hidden" name="lang" value="<?php echo $lang?>"/>
    <input type="submit" value="<?php echo $strings[$lang]["subscribe"]?>" />
  </form>

<?php 
    } else { 
        echo $strings[$lang]["norace"];
    } 
}

include_once("footer.inc");

?>
