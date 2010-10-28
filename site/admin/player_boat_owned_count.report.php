<?php
    $PAGETITLE = "Report : Boat per player count(*)";
    include ("htmlstart.php");
    include_once ("functions.php");

    $query  = "SELECT P.idplayers, P.playername, count(*) AS `boat count()`";
    $query .= "FROM players P LEFT JOIN playerstousers PU ON (P.idplayers = PU.idplayers AND linktype = 1) ";
    $query .= "GROUP BY P.`idplayers` ORDER BY count(*) DESC, playername LIMIT 50;";

    htmlQuery($query);

    include ("htmlend.php");
?>
