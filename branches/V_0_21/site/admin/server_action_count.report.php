<?php
    $PAGETITLE = "Report : Server action count()";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "SELECT ".
         "UA.`actionserver` AS `Server`, count(*) as `actions` ".
         "FROM `user_action` AS UA ".
         "GROUP BY UA.actionserver ORDER BY UA.actionserver DESC LIMIT 10;";

    htmlQuery($q);

    include ("htmlend.php");
?>
