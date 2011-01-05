<?php
    $PAGETITLE = "Report : Player action count()";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "SELECT ".
         "UA.`idplayers` AS `@idplayers`, `playername`, count(*) as `actions`".
         "FROM user_action UA LEFT JOIN `players` P ON P.idplayers = UA.idplayers ".
         "WHERE P.`idplayers` IS NOT NULL ".
         "GROUP BY P.idplayers ORDER BY actions DESC LIMIT 50;";

    htmlQuery($q);

    include ("htmlend.php");
?>
