<?php
    include_once("includes/header.inc");
    include_once("config.php");
?>
<div>
    <h2><?php echo getLocalizedString("mymappref"); ?></h2>
</div>
<?php 
    $fullUsersObj = new fullUsers(getLoginId());
    if (isset($_POST['action'])) {
        $action = htmlentities($_POST['action']);
    } else {
        $action = "none";
    }
    if ($fullUsersObj->users->engaged != 0) {  
        //echo "PO=".$prefOpponents;
        $fullRacesObj = new fullRaces ($fullUsersObj->users->engaged, $fullUsersObj->races);
        //$bounds = $fullRacesObj->getRacesBoundaries();
        // Sauvegarde des préférences
        // Check si liste vide, dans ce cas, on précoche l'utilisateur demandeur (bug implode signalé par Phille le 27/06/07)
        if ( $action == getLocalizedString("valider") ) {
            $list=$_POST['list'];
            //print_r($list);
            //echo implode(",",$list);
            if ( $list == "" || count($list) == 0 ) $list = array($fullUsersObj->users->idusers) ;
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $list)   );
        } else if ( $action == getLocalizedString("tous") ) {
            $oppList=array();
            foreach ( $fullRacesObj->opponents as $opp) array_push($oppList, $opp->idusers);
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );   
        } else if ( $action == getLocalizedString("top20") ) {
            $oppList=array();
            $num_opp=0;
            foreach ( $fullRacesObj->opponents as $opp) {
                $num_opp++;
                array_push($oppList, $opp->idusers);
                if ( $num_opp == 20 ) break;
            }
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );
        } else if ( $action == getLocalizedString("top10") ) {
            $oppList=array();
            $num_opp=0;
            foreach ( $fullRacesObj->opponents as $opp) {
                $num_opp++;
                array_push($oppList, $opp->idusers);
                if ( $num_opp == 10 ) break;
            }
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );    
        } else if ( $action == getLocalizedString("aucun") ) {
            setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , " "  );
        } 
        $prefOpponents=getUserPref($fullUsersObj->users->idusers,"mapPrefOpponents");
  ?>



    <form id="mercator" action="mappref.php" method="post">
      <input type="hidden" name="idraces" value="<?php echo $fullUsersObj->users->engaged; ?>" />
      <input type="hidden" name="lang" value="<?php echo $lang?>" />

      <!-- Table pour remonter un peu toute la page -->
      <table border="0" width="100%">
        <tr>
          <td class="map" align="left" valign="top"></td>
          <!-- //Colonne 2 : les trajectoires et les noms des bateaux -->
          <td class="map" align="left" valign="top">
            <h3><?php echo getLocalizedString("chooseopp") . " (You = " . getBoatPopularity($fullUsersObj->users->idusers, $fullUsersObj->users->engaged) . " times)";  ?> : </h3>
<?
        //List of players, check boxes
        $fullRacesObj->dispHtmlForm(explode(",", $prefOpponents));
?>
          </td>
        </tr>
      </table>
      <input type="submit" name="action" value="<? echo getLocalizedString("valider") ?>" />
      <input type="submit" name="action" value="<? echo getLocalizedString("tous") ?>" />
      <input type="submit" name="action" value="<? echo getLocalizedString("top20") ?>" />
      <input type="submit" name="action" value="<? echo getLocalizedString("top10") ?>" />
      <input type="submit" name="action" value="<? echo getLocalizedString("aucun") ?>" />
    </form>
<?php
    } else {
        echo  getLocalizedString("mustbeengaged");
    }
    //TODO : write into cookie at submission
    include_once("includes/footer.inc");
?>
