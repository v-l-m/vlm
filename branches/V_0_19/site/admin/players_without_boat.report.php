<?php
    $PAGETITLE = "Report : Players without boats";
    include ("htmlstart.php");
    include_once ("functions.php");

    $query  = "SELECT P.idplayers, P.playername ";
    $query .= "FROM players as P ";
    $query .= "WHERE P.idplayers NOT IN (SELECT DISTINCT idplayers FROM playerstousers WHERE linktype = ".PU_FLAG_OWNER.") ";
    htmlQuery($query);

    include ("htmlend.php");
?>
