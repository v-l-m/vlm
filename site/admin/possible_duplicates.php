<?php
    $PAGETITLE = "Report : Duplicates by IP";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "SELECT ".
         "GROUP_CONCAT(DISTINCT CONCAT(username, '-', USRA.idusers)) AS pseudos, idraces, USRA.ipaddr, COUNT(DISTINCT USRA.idusers) AS n ".
         "FROM user_action as USRA LEFT JOIN users as USR ON (USRA.idusers = USR.idusers AND USRA.idraces = USR.engaged) ".
         "WHERE engaged > 0 ".
         "GROUP BY USRA.idraces, USRA.ipaddr ".
         "HAVING COUNT(DISTINCT USRA.idusers) > 1;";

    htmlQuery($q);

    include ("htmlend.php");
?>
