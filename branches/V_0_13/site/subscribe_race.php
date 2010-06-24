<?php
    include_once("includes/header.inc");
    include_once("config.php");
    
    $fullUsersObj = new fullUsers(getLoginId());

    //select NOT started races list and available for this boat
    $races=availableRaces($fullUsersObj->users->idusers);

    $select_list="";
    foreach($races as $race) {
        $racesObj=new races($race);
        $select_list = $select_list . "<option value=\"". $racesObj->idraces ."\">". $racesObj->idraces . " - " . $racesObj->racename."</option>";
    }
  
    if ( $select_list != "" ) {
        echo "<h1>".getLocalizedString("sub_race")."</h1>";
?>
    <form action="myboat.php">
        <select name="idraces">
<?php
        echo $select_list;
?>
        </select>
        <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
        <input type="hidden" name="type" value="subscribe"/>
        <input type="hidden" name="lang" value="<?php echo $lang?>"/>
        <input type="submit" value="<?php echo getLocalizedString("subscribe"); ?>" />
        </form>

<?php 
    } else { 
        echo getLocalizedString("norace");
    } 
?>
