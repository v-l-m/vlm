<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    //FIXME
    if (!isPlayerLoggedIn()) die("<h1 class=\"warn\">".getLocalizedString("You are not allowed to do that !")."</h1>");

    $action = get_cgi_var("action", 'revokerequest');
    $idusers = intval(get_cgi_var("idusers"));
    $userobj = getUserObject($idusers);

    echo "<div>";
    if (in_array($userobj->idusers, getLoggedPlayerObject()->getBoatsitIdList())) {
        
        if ($action == 'revokeconfirm') {
            
            if ($userobj->removeRelationship(getPlayerId(), PU_FLAG_BOATSIT)) {
                echo "<h2 class=\"info\">".getLocalizedString("You are no longer boatsitter of this boat")."</h2>";
            } else {
                echo "<h2 class=\"error\">".getLocalizedString("You are not allowed to do that !")."</h2>";
            }
        } else {
            echo "<h2>".getLocalizedString("You are currently allowed to boatsit this boat :")."</h2>";
            echo "<p>".$userobj->htmlIdusersUsernameLink()."</p>";
?>
            <form action="revoke_boatsitting.php" method="post">
            <input type="hidden" name="idusers" value="<?php echo $userobj->idusers ?>" />
            <input type="hidden" name="action" value="revokeconfirm"/>
            <input type="submit" value="<?php echo getLocalizedString("Confirm boatsitting revocation ?"); ?>" />
            </form>
<?php
          echo "<p class=\"warn\">".getLocalizedString("You will not be allowed to boatsit this boat anymore if you confirm")."</p>";
        }
    } else {
        echo "<h1 class=\"warn\">".getLocalizedString("You are not allowed to do that !")."</h1>";
    }
    echo "</div>";
?>
