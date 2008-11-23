<?php
include_once("header.inc");
include_once("config.php");
include_once("strings.inc");
//TODO read from cookie and populate form
//TODO if no cookie found, put default values in the form
?>
<?php //print_r($_COOKIE);?>
<div>
<h2><?php echo $strings[$lang][mymappref]; ?></h2>
<BR>
</div>
<?php 
$fullUsersObj = new fullUsers(getLoginId());
if ($fullUsersObj->users->engaged != 0) {	
    //echo "PO=".$prefOpponents;
    $fullRacesObj = new fullRaces ($fullUsersObj->users->engaged);
    //$bounds = $fullRacesObj->getRacesBoundaries();
    // Sauvegarde des préférences
    // Check si liste vide, dans ce cas, on précoche l'utilisateur demandeur (bug implode signalé par Phille le 27/06/07)
    if ( htmlentities($_POST['action']) == $strings[$lang]["valider"] ) {
	$list=$_POST['list'];
	//print_r($list);
	//echo implode(",",$list);
        if ( $list == "" || count($list) == 0 ) {
            $list = array($fullUsersObj->users->idusers) ;
        }
        setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $list)   );

    } else if ( htmlentities($_POST['action']) == $strings[$lang]["tous"] ) {
        $oppList=array();
        foreach ( $fullRacesObj->opponents as $opp) {
	    array_push($oppList, $opp->idusers);
	}
        setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );

    } else if ( htmlentities($_POST['action']) == $strings[$lang]["top20"] ) {
	$oppList=array();
	$num_opp=0;
        foreach ( $fullRacesObj->opponents as $opp) {
	  $num_opp++;
	  array_push($oppList, $opp->idusers);
	  if ( $num_opp == 20 ) break;
	}
        setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );

    } else if ( htmlentities($_POST['action']) == $strings[$lang]["top10"] ) {
	$oppList=array();
	$num_opp=0;
        foreach ( $fullRacesObj->opponents as $opp) {
	  $num_opp++;
	  array_push($oppList, $opp->idusers);
	  if ( $num_opp == 10 ) break;
	}
        setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $oppList)   );

    } else if ( htmlentities($_POST['action']) == $strings[$lang]["aucun"] ) {
        setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , " "  );
    } 
    $prefOpponents=getUserPref($fullUsersObj->users->idusers,"mapPrefOpponents");
?>
<form id="mercator" action="mappref.php" method="POST">
<input type="hidden" name="idraces" value="<?php echo $fullUsersObj->users->engaged; ?>" />


<!-- Table pour remonter un peu toute la page -->
<table border=0 width=100%><tr><td class=map align=left valign=top>

<!-- //Colonne 2 : les trajectoires et les noms des bateaux -->
<td class=map align=left valign=top>

<!--
<h3><?php echo $strings[$lang]["tracks"] ?> : </h3>
<input type="checkbox" name="tracks" <?php if (($_COOKIE['tracks'])=="on") echo "checked=\"checked\"";?> /><?php echo $strings[$lang]["disptracks"] ?>
<br>
-->

<h3><?php echo $strings[$lang]["chooseopp"] . " (You = " . getBoatPopularity($fullUsersObj->users->idusers, $fullUsersObj->users->engaged) . " times)";  ?> : </h3>
<?
//List of players, check boxes


//$fullRacesObj->dispHtmlForm($strings, $lang, explode(",", $_COOKIE['list']));
$fullRacesObj->dispHtmlForm($strings, $lang, explode(",", $prefOpponents)  );


?>
<BR>
</td></tr></table>

<input type="submit" name="action" value="<? echo $strings[$lang]["valider"]?>"/>
<input type="submit" name="action" value="<? echo $strings[$lang]["tous"]?>"/>
<input type="submit" name="action" value="<? echo $strings[$lang]["top20"]?>"/>
<input type="submit" name="action" value="<? echo $strings[$lang]["top10"]?>"/>
<input type="submit" name="action" value="<? echo $strings[$lang]["aucun"]?>"/>
      </form>
<?php
   }
else
{
  echo  $strings[$lang]["mustbeengaged"];
}
//TODO : write into cookie at submission
include_once("footer.inc");
?>
