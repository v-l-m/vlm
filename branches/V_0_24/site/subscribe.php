<?php
    include_once("includes/header.inc");
    include_once("config.php");

    if (!isPlayerLoggedIn()) die("Not logged");

    $fullUsersObj = new fullUsers(getLoginId());

    // If engaged on a race, display Warning
    if ($fullUsersObj->users->engaged  != 0) {
        $fullUsersObj->displayAbandonDiv();
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
        echo "<h1>".getLocalizedString("sub_race")."</h1>";
?>
  <form action="myboat.php">
    <select name="idraces">
        <?php echo $select_list; ?>
    </select>
    <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
    <input type="hidden" name="type" value="subscribe"/>
    <input type="submit" value="<?php echo getLocalizedString("subscribe")?>" />
  </form>

<?php 
    } else { 
        echo getLocalizedString("norace");
    } 
}

include_once("includes/footer.inc");

?>
