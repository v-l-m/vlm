<?php 
    include_once("includes/header.inc");
    include_once("config.php");

    if ( isLoggedIn() ) {
        echo "<h2>".getLocalizedString("Recent actions for this boat")." : </h2>";
        $query = "SELECT `time` AS `TIME`, HEX(CRC32(ipaddr)) AS `COMPUTER ID`, concat(`playername`, ' @', players.idplayers) AS `PLAYER @id`, `action` AS ACTION FROM user_action LEFT JOIN players ON (user_action.idplayers = players.idplayers) WHERE user_action.idusers = ".getLoginId()." ORDER BY time DESC LIMIT ".MAX_LOG_USER_ACTION_VIEW.";";
        htmlQuery($query);
        echo "<h3>".getLocalizedString("computerid")."</h3>";

    } else {
        echo "<h4>You should not do that...your IP : " . getip() . "</h4>";
    }

    include_once("includes/footer.inc");
?>

