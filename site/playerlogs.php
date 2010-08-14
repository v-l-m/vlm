<?php 
    include_once("includes/header.inc");
    include_once("config.php");

    if ( isPlayerLoggedIn() ) {
        echo "<h2>".getLocalizedString("Recent actions for this player")." : ".getLoggedPlayerObject()->htmlPlayername()."</h2>";
        $query = "SELECT `time` AS `TIME`, HEX(CRC32(user_action.ipaddr)) AS `COMPUTER ID`, concat(username, ' #', users.idusers) AS `BOAT #id`, `action` AS ACTION FROM user_action LEFT JOIN users ON (users.idusers = user_action.idusers) WHERE idplayers = ".getPlayerId()." ORDER BY time DESC LIMIT ".MAX_LOG_USER_ACTION_VIEW.";";
        htmlQuery($query);
        echo "<h3>".getLocalizedString("computerid")."</h3>";

    } else {
        echo "<h4>You should not do that...your IP : " . getip() . "</h4>";
    }

    include_once("includes/footer.inc");
?>

