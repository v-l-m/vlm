<?php
    $PAGETITLE = "Boats engaged on Once a day trophy";
    include ("htmlstart.php");
    include_once ("functions.php");
	echo "<P>Engaged boats on trophy</P>";

    $query  = "SELECT U.controldate, U.score, UT.idraces, UT.idusers, US.username ";
	$query  .= "FROM racetrophycontrol U ";
	$query  .= "left join users_Trophies UT on U.idusertrophy = UT.idUsersTrophies ";
	$query  .= "right join users US on UT.idusers = US.idUsers ";
	$query  .= "WHERE UT.idusers IS NOT NULL AND UT.quitdate IS NULL AND UT.RefTrophy=2 ";
	$query  .= "ORDER BY UT.idraces DESC, U.controldate DESC, UT.idUsers ASC; ";
    htmlQuery($query);

	echo "<P>Shot boats on trophy</P>";
    $query  = "SELECT U.controldate, U.score, UT.idraces, UT.idusers, US.username ";
	$query  .= "FROM racetrophycontrol U ";
	$query  .= "left join users_Trophies UT on U.idusertrophy = UT.idUsersTrophies AND UT.quitdate = U.controldate ";
	$query  .= "right join users US on UT.idusers = US.idUsers ";
	$query  .= "WHERE UT.idusers IS NOT NULL AND NOT UT.quitdate IS NULL AND UT.RefTrophy=2 ";
	$query  .= "ORDER BY UT.idraces DESC, U.controldate DESC, UT.idUsers ASC; ";
    htmlQuery($query);
	
    include ("htmlend.php");
?>
