<?php
    $PAGETITLE = "Report : Duplicates by IP";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    htmlQuery("select group_concat(distinct idusers) as pseudos, idraces, ipaddr, count(distinct idusers) as n from user_action group by idraces, ipaddr having count(distinct idusers) > 1;");

    include ("htmlend.php");
?>
