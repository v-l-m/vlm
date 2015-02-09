<?php
    $PAGETITLE = "Report : Boat action count()";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "SELECT ".
         "UA.`idusers` AS `#idboat`, `username` AS `boatpseudo`, count(*) as `actions`".
         "FROM user_action UA LEFT JOIN `users` U ON U.idusers = UA.idusers ".
         "GROUP BY UA.idusers ORDER BY actions DESC LIMIT 50;";

    htmlQuery($q);

    include ("htmlend.php");
?>
