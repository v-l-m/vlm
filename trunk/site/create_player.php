<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $actioncreate = get_cgi_var("createplayer");

    function printAccountSummary($emailid = "", $password = "", $playername = "") {
        echo "<ul>";
            echo "<li>".getLocalizedString("email")." : $emailid</li>";
            echo "<li>".getLocalizedString("password")." : $password</li>";
            echo "<li>".getLocalizedString("playername")." : $playername</li>";
        echo "</ul>";
    }

    function printFormRequest($emailid = "", $password = "", $playername = "") {
        echo "<div id=\"createplayerbox\">";
        echo "<h2>".getLocalizedString("chooseaccount")."</h2>";
?>
        <form action="#" method="post" name="createplayer">
            <input size="25" maxlength="64" name="emailid" value="<?php echo $emailid; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("email"); ?></span>
            <br />
            <input size="25" maxlength="15" name="password" value="<?php echo $password; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("password"); ?></span>
            <br />
            <input size="25" maxlength="15" name="playername" value="<?php echo $playername; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("playername"); ?></span>
            <input type="hidden" name="createplayer" value="requested" />
            <br />
            <input type="submit" />
        </form> 
        <p><?php echo nl2br(getLocalizedString("createaccountrules")); ?></p>
        </div>
<?php
    }

    $emailid = get_cgi_var("emailid");
    $password = get_cgi_var("password");
    $playername = get_cgi_var("playername");

    if ($actioncreate == "requested") { //REQUESTED
        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        if (!$player->checkNonconformity()) {
            echo "<div id=\"createplayerbox-request\">";
            echo "<h2>".getLocalizedString("Here is your request for creating an account")."&nbsp;:</h2>";
            printAccountSummary($emailid, $password, $playername);
?>
            <form action="#" method="post" name="createplayer">
                <input type="hidden" name="emailid" value="<?php echo $emailid; ?>"/>
                <input type="hidden" name="password" value="<?php echo $password; ?>"/>
                <input type="hidden" name="playername" value="<?php echo $playername; ?>"/>
                <input type="hidden" name="createplayer" value="confirmed"/>
                <input type="submit" value="<?php echo getLocalizedString("Confirm account request ?"); ?>" />
            </form> 
<?php
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Input invalid")."</h2>";
            echo "<div>".nl2br($player->error_string)."</div>";
            printFormRequest($emailid, $password, $playername);
        }
    } else if ($actioncreate == "confirmed") { //CONFIRMED
        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        $player->setPassword($password);
        $player->setSeed();
        if (!$player->checkNonconformity()) {
            $player->insert();
            if (!$player->error_status) {
               echo "<div id=\"createplayerbox\">";
               $player->mailValidationMessage();
               echo '<h2>'.getLocalizedString("An email has been sent. Click on the link to validate.").'</h2>';
               echo "</div>";
           } else {
               echo "<h2>".getLocalizedString("Input invalid")."</h2>";
               echo "<div>".nl2br($player->error_string)."</div>";
               printFormRequest($emailid, $password, $playername);
           }   
       }
    } else if ($actioncreate == "validate") { //VALIDATE
        echo "<div id=\"createplayerbox\">";
        $seed = get_cgi_var("seed");
        $player = new playersPending($emailid, $seed);
        if (!$player->validate()) {
            print getLocalizedString("Account validation error");
            print $player->error_string;
        } else {
            echo "<h2>".getLocalizedString("Your account is ready to be created")."</h2>";
            printAccountSummary($player->email, "****", $player->playername);
?>
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" name="createplayer">
                <input type="hidden" name="seed" value="<?php echo $seed; ?>" />
                <input type="hidden" name="emailid" value="<?php echo $emailid; ?>" />
                <input type="hidden" name="createplayer" value="create" />
                <input type="submit" value="<?php echo getLocalizedString("Confirm account creation ?"); ?>" />
            </form> 
<?php
        }
        echo "</div>";
    } else if ($actioncreate == "create") { //CREATE
        $seed = get_cgi_var("seed");
        $player = new playersPending($emailid, $seed);
        echo "<div id=\"createplayerbox\">";
        if (!$player->create()) {
            echo getLocalizedString("Account creation error");
            echo $player->error_string;
        } else {
            echo "<h2>".getLocalizedString("Your account has been created")."</h2>";
            printAccountSummary($player->email, "****", $player->playername);
        }
        echo "</div>";
    } else {
        printFormRequest();
    }
    
    include_once("includes/footer.inc");
?>
