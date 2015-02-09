<?php
    $PAGETITLE = "Report : Boats with more than one owner";
    include ("htmlstart.php");
    include_once ("functions.php");

    echo "<h2>Please correct manually in <a href=\"playerstousers.php\">Playerstousers Admin</a>.</h2>";

    $query  = "SELECT PU.idusers, U.username, U.boatname, group_concat(concat('@', P.idplayers, ' ', P.playername)) AS playernames ";
    $query .= "FROM playerstousers as PU ";
    $query .= "LEFT JOIN players as P ON (PU.idplayers = P.idplayers) ";
    $query .= "LEFT JOIN users as U ON (PU.idusers = U.idusers) ";
    $query .= "WHERE linktype = ".PU_FLAG_OWNER." ";
    $query .= "GROUP BY PU.idusers ";
    $query .= "HAVING COUNT(*) > 1";
    htmlQuery($query);

    include ("htmlend.php");
?>
