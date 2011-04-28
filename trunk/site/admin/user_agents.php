<?php
    $PAGETITLE = "Report : User agent statistics";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q  = "SELECT SUBSTRING_INDEX(`useragent`, '/', 1) AS `tools`, COUNT(DISTINCT idplayers) AS num_players ";
    $q .= "FROM `user_action` WHERE SUBSTRING_INDEX(`useragent`, '/', 1) IN ('Frog', 'qtVlm', 'SbsRouteur', 'Tcv') ";
    $q .= "GROUP BY SUBSTRING_INDEX(`useragent`, '/', 1) ";
    $q .= "ORDER BY COUNT(*) DESC;";

    htmlQuery($q);

    $q = "SELECT ".
         "useragent, count(*) ".
         "FROM user_action ".
         "GROUP BY useragent ORDER BY count(*) DESC;";

    htmlQuery($q);

    include ("htmlend.php");
?>
