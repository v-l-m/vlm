<?php
    include_once("includes/header.inc");
    include_once("config.php");
    include_once("players.class.php");

    $actionreset = get_cgi_var("resetpassword");

    function printFormRequest($emailid = "", $hashpassword = "", $action = "requested") {
        echo "<div id=\"resetpasswordbox\">";
        $request = ($action != "validated" && $action != "confirmed");
        if ($request) echo "<h2>".getLocalizedString("Enter your email for resetting your password")."</h2>";
?>
        <form action="reset_password.php" method="post" name="resetpassword">
            <input size="25" maxlength="64" name="emailid" value="<?php echo $emailid; ?>" <?php echo ($request ? "" : "type=\"hidden\" "); ?>/>
            <span class="texthelpers"><?php echo ($request ? getLocalizedString("email") : $emailid); ?></span>
            <input type="hidden" name="hashpassword" value="<?php echo $hashpassword; ?>" />
            <input type="hidden" name="resetpassword" value="<?php echo $action; ?>" />
            <br />
            <input type="submit" />
        </form> 
        </div>
<?php
    }

    $emailid = get_cgi_var("emailid");
    $hashpassword = get_cgi_var("hashpassword");

    if ($actionreset == "requested") { //REQUESTED
        $player = new players(0, $emailid);
        if ($player->idplayers != 0) {
            echo "<div id=\"resetpasswordbox-request\">";
            $player->requestPasswordReset();
            echo "<h2>".getLocalizedString("An email has been sent. Click on the link inside.")."&nbsp;:</h2>";
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Input invalid")."</h2>";
            printFormRequest($emailid);
        }
    } else if ($actionreset == "validated") { //VALIDATED
        $player = new players(0, $emailid);
        if ($player->idplayers != 0 && $player->password == $hashpassword) {
            echo "<div id=\"resetpasswordbox-validated\">";
            echo "<h2>".getLocalizedString("Click below to reset your password.")."&nbsp;:</h2>";
            printFormRequest($emailid, $hashpassword, 'confirmed');
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Input invalid")."</h2>";
            printFormRequest($emailid);
        }
    } else if ($actionreset == "confirmed") { //CONFIRMED
        $player = new players(0, $emailid);
        if ($player->idplayers != 0 && $player->password == $hashpassword) {
            $newpass = generatePassword($emailid);
            $player->modifyPassword($newpass);
            $player->mailInformation(getLocalizedString("Here is your new password"), getLocalizedString("Your password is now")." : $newpass\n".getLocalizedString("Please change it quickly").".");
            echo "<div id=\"resetpasswordbox-confirmed\">";
            echo "<h2>".getLocalizedString("Check your inbox to get your new password.")."</h2>";
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Input invalid")."</h2>";
            printFormRequest($emailid);
        }
    } else {
        printFormRequest();
    }
    
    include_once("includes/footer.inc");
?>
