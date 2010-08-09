<?php 
    include_once("includes/header.inc");
    include_once("config.php");

    if ( isPlayerLoggedIn() ) {
        $query = "SELECT `time` AS `TIME`, HEX(CRC32(ipaddr)) AS `COMPUTER ID`, `idusers` AS `USER`, `action` AS ACTION FROM user_action WHERE idplayers = ".getPlayerId()." ORDER BY time DESC LIMIT ".MAX_LOG_USER_ACTION_VIEW.";";
        htmlQuery($query);
        echo "<h3>".getLocalizedString("computerid")."</h3>";

    } else {
        echo "<h4>You should not do that...your IP : " . getip() . "</h4>";
    }

    include_once("includes/footer.inc");
?>

