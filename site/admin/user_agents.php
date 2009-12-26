<?php
    $PAGETITLE = "Report : User agent statistics";
    include ("htmlstart.php");
    include_once ("functions.php");

    $q = "SELECT ".
         "useragent, count(*) ".
         "FROM user_action ".
         "GROUP BY useragent ;";

    htmlQuery($q);

    include ("htmlend.php");
?>
