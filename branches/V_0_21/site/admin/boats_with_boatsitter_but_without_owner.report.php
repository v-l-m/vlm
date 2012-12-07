<?php
    $PAGETITLE = "Report : Boats with boatsitter(s) but without owner";
    include ("htmlstart.php");
    include_once ("functions.php");

    echo "<h2>Please correct manually in <a href=\"playerstousers.php\">Playerstousers Admin</a>.</h2>";

    $query  = "SELECT U.idusers, U.username, U.boatname ";
    $query .= "FROM users as U ";
    $query .= "WHERE U.idusers IN (SELECT DISTINCT idusers FROM playerstousers) ";
    $query .= "AND U.idusers > 0 ";
    $query .= "AND U.idusers NOT IN (SELECT DISTINCT idusers FROM playerstousers WHERE linktype = ".PU_FLAG_OWNER.") ";
    htmlQuery($query);

    include ("htmlend.php");
?>
