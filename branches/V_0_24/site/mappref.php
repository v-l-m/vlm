<?php
    include_once("includes/header.inc");
    include_once("config.php");
?>
<div>
    <h2><?php echo getLocalizedString("chooseopp"); ?></h2>
</div>
<?php 
    $fullUsersObj = new fullUsers(getLoginId());
    $action = htmlentities(get_cgi_var('action', 'none'));
    if ($fullUsersObj->users->engaged != 0) {  
        //echo "PO=".$prefOpponents;
        $fullRacesObj = new fullRaces ($fullUsersObj->users->engaged, $fullUsersObj->races);
        //$bounds = $fullRacesObj->getRacesBoundaries();
        // Sauvegarde des préférences
        // Check si liste vide, dans ce cas, on précoche l'utilisateur demandeur (bug implode signalé par Phille le 27/06/07)
        if ( $action == "valider") {
            $list= get_cgi_var('list', "");
            //print_r($list);
            //echo implode(",",$list);
            if ( $list == "" || count($list) == 0 ) $list = array($fullUsersObj->users->idusers) ;
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $list)   );
        } else if ( $action == "tous") {
            $oppList=array();
            foreach ( $fullRacesObj->opponents as $opp) array_push($oppList, $opp->idusers);
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );   
        } else if ( $action == "top20") {
            $oppList=array();
            $num_opp=0;
            foreach ( $fullRacesObj->opponents as $opp) {
                $num_opp++;
                array_push($oppList, $opp->idusers);
                if ( $num_opp == 20 ) break;
            }
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );
        } else if ( $action == "top10") {
            $oppList=array();
            $num_opp=0;
            foreach ( $fullRacesObj->opponents as $opp) {
                $num_opp++;
                array_push($oppList, $opp->idusers);
                if ( $num_opp == 10 ) break;
            }
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );    
        } else if ( $action == "aucun") {
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , ""  );
        } 
        $prefOpponents=getUserPref($fullUsersObj->users->idusers,"mapPrefOpponents");
  ?>



    <form id="mercator" action="mappref.php" method="post">
      <input type="hidden" name="idraces" value="<?php echo $fullUsersObj->users->engaged; ?>" />

      <!-- Table pour remonter un peu toute la page -->
      <table border="0" width="100%">
        <tr>
          <td class="map" align="left" valign="top"></td>
          <!-- //Colonne 2 : les trajectoires et les noms des bateaux -->
          <td class="map" align="left" valign="top">
            <?php
            //FIXME : we are not sure of the prefOpponents format
            $prefOpponentsList = explode(",", $prefOpponents);
            if (strlen($prefOpponents) == 0) $prefOpponentsList = Array();
            $nbo = count($prefOpponentsList);

            echo "<h3";
            if ($nbo > MAX_BOATS_ON_MAPS) echo " class=\"warnmessage\"";
            echo ">".getLocalizedString("Number of boats selected (checked below)")."&nbsp;:&nbsp;".$nbo.".</h3>";
            echo "<h3>".getLocalizedString("Popularity of your boat")."&nbsp;:&nbsp;".getBoatPopularity($fullUsersObj->users->idusers, $fullUsersObj->users->engaged);
            echo ".</h3>";
            ?>
<?
        //List of players, check boxes
        $fullRacesObj->dispHtmlForm($prefOpponentsList);
?>
          </td>
        </tr>
      </table>
      <button type="submit" name="action" value="valider"><? echo getLocalizedString("valider") ?></button>
      <button type="submit" name="action" value="tous"><? echo getLocalizedString("tous") ?></button>
      <button type="submit" name="action" value="top20"><? echo getLocalizedString("top20") ?></button>
      <button type="submit" name="action" value="top10"><? echo getLocalizedString("top10") ?></button>
      <button type="submit" name="action" value="aucun"><? echo getLocalizedString("aucun") ?></button>
    </form>
<?php
    } else {
        echo  getLocalizedString("mustbeengaged");
    }
    //TODO : write into cookie at submission
    include_once("includes/footer.inc");
?>
