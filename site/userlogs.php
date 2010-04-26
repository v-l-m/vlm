<?php 
    include_once("includes/header.inc");
    include_once("config.php");

    if ( isLoggedIn() ) {
        $query = "SELECT time as TIME, HEX(CRC32(ipaddr)) as `COMPUTER ID`, action as ACTION FROM user_action WHERE idusers = ".getLoginId()." ORDER BY time DESC LIMIT 20;";
        htmlQuery($query);
        echo "<h3>".getLocalizedString("computerid")."</h3>";

    } else {
        echo "<h4>You should not do that...your IP : " . getip() . "</h4>";
    }

    include_once("includes/footer.inc");
?>

