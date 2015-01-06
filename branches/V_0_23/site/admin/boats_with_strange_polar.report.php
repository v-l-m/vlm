<?php
    $PAGETITLE = "Boat with unknown polar";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    $query  = "SELECT U.idusers, U.username, U.engaged, U.boattype, R.boattype as `Race boattype` ";
    $query .= "FROM users U ";
    $query .= "LEFT JOIN races R ON (U.engaged = R.idraces)";
    $query .= "WHERE R.boattype != U.boattype AND U.engaged > 0";
    htmlQuery($query);

    include ("htmlend.php");
?>
