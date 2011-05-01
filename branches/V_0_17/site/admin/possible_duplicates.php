<?php
    $PAGETITLE = "Report : Duplicates by IP";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "select idraces, ipaddr, group_concat(distinct concat('@', PL.idplayers, '/', PL.playername)) as playernames from user_action as UA LEFT JOIN players as PL ON (PL.idplayers = UA.idplayers) where idraces > 0 and UA.idplayers > 0 group by ipaddr, idraces having count(distinct UA.idplayers) > 1 order by count(distinct UA.idplayers);"

    htmlQuery($q);

    echo '<br />';

    $q = "SELECT ".
         "GROUP_CONCAT(DISTINCT CONCAT(username, '-', USRA.idusers)) AS pseudos, idraces, USRA.ipaddr, COUNT(DISTINCT USRA.idusers) AS n ".
         "FROM user_action as USRA LEFT JOIN users as USR ON (USRA.idusers = USR.idusers AND USRA.idraces = USR.engaged) ".
         "WHERE engaged > 0 ".
         "GROUP BY USRA.idraces, USRA.ipaddr ".
         "HAVING COUNT(DISTINCT USRA.idusers) > 1;";

    htmlQuery($q);

    include ("htmlend.php");
?>
