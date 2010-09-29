<?php
    $PAGETITLE = "Connect as [insert your idp]";
    include ("htmlstart.php");
    include_once ("functions.php");
    
// Main code


    $idp = get_cgi_var("idp", "");
    $confirm = get_cgi_var("confirm", "yes");
    $action = get_cgi_var("action", "blank");
    
    if ($action == "connectas" && $confirm == "yes" && intval($idp) > 0 && !is_null(getPlayerObject(intval($idp))) ) {
            //We're done.
            insertAdminChangelog(Array("operation" => "Connect as", "rowkey" => $idp));
            $player = getPlayerObject(intval($idp));
            $defaultuser = getUserObject($player->getDefaultBoat());
            loginPlayer($defaultuser->idusers, $defaultuser->username, $player->idplayers, $player->playername);
            echo "<h3>OK, you are now logged as ".$player->playername."</h3>";
            echo "<p><a href=\"/\">Click here to go to the welcome page.</a></p>";
    } else {
        echo "<h2>Input the idp you want to connect as...</h2>";
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="connectas" />
            <input type="hidden" name="confirm" value="yes" />
            <input name="idp" value="<?php echo $idp; ?>" />
            <input type="submit" value="Connect as..." />
        </form>
<?
    }
    
    include ("htmlend.php");
?>
