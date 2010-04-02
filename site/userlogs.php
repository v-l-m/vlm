<?php 
    include_once("includes/header.inc");
    include_once("config.php");
    include_once("includes/strings.inc");

    if ( isLoggedIn() ) {
        $query = "SELECT time as TIME, ipaddr as IP, action as ACTION FROM user_action WHERE idusers = ".getLoginId()." ORDER BY time DESC LIMIT 20;";
        htmlQuery($query);
    } else {
        echo "<h4>You should not do that...your IP : " . getip() . "</h4>";
    }

    include_once("includes/footer.inc");
?>

