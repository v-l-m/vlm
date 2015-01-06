<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    //FIXME
    if (!isPlayerLoggedIn()) die('ERROR');

    $action = get_cgi_var("modifypassword");

    function printFormRequest($oldpassword = "") {
        echo "<div id=\"modifypasswordbox\">";
        echo "<h2>".getLocalizedString("Change your password")."&nbsp;:</h2>";
?>
        <form action="#" method="post" name="modifypassword">
            <input type="password" size="25" maxlength="15" name="old_password" value="<?php echo $oldpassword; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("Current password"); ?></span>
            <br />
            <input type="password" size="25" maxlength="15" name="new_password" value="" />
            <span class="texthelpers"><?php echo getLocalizedString("New password"); ?></span>
            <br />
            <input type="password" size="25" maxlength="15" name="check_password" value="" />
            <span class="texthelpers"><?php echo getLocalizedString("New password"); ?> (<?php echo getLocalizedString('check'); ?>)</span>
            <input type="hidden" name="modifypassword" value="requested" />
            <br />
            <input type="submit" />
        </form> 
        </div>
<?php
    }

    $old_password = htmlentities(get_cgi_var("old_password"));
    $new_password = htmlentities(get_cgi_var("new_password"));
    $check_password = htmlentities(get_cgi_var("check_password"));

    if ($action == "requested") { //REQUESTED
        echo "<div id=\"modifypasswordbox\">";
        $player = getLoggedPlayerObject();
        if (!$player->checkPassword($old_password)) {
            echo "<h2>".getLocalizedString("Error when keying your current password").".</h2>";
            printFormRequest($old_password);
        } else if ($new_password != $check_password) {
            echo "<h2>".getLocalizedString("Your two inputs of your new password differ").".</h2>";
            printFormRequest($old_password);
        } else if ($player->modifyPassword($new_password)) {
             //current password is OK
            echo "<h2>".getLocalizedString("Your password has been updated.")."&nbsp;".getLocalizedString("An email has been sent for your information.")."</h2>";
        } else {
            echo "<h2>".getLocalizedString("Error when changing password.")."</h2>";
            printFormRequest($old_password);
        };
        echo "</div>";
    } else {
        printFormRequest($old_password);
    }
    
    include_once("includes/footer.inc");
?>
