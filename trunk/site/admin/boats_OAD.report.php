<?php
    $PAGETITLE = "Boats engaged on Once a day trophy";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    $query  = "SELECT U.controldate, U.score, UT.idraces, UT.idusers, US.boatname, US.username ";
	$query  .= "FROM racetrophycontrol U ";
	$query  .= "left join users_Trophies UT on U.idusertrophy = UT.idUsersTrophies ";
	$query  .= "right join users US on UT.idusers = US.idUsers ";
	$query  .= "WHERE UT.idusers IS NOT NULL AND UT.quitdate IS NULL ";
	$query  .= "ORDER BY U.controldate DESC, UT.idUsers ASC, UT.RefTrophy ASC; ";
	
	
    htmlQuery($query);

    include ("htmlend.php");
?>
