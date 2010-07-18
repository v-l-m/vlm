<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $actioncreate = get_cgi_var("createplayer");

    function printFormRequest($emailid = "", $password ="", $playername="") {
        echo "<div id=\"createplayerbox\">";

        echo "<h2>".getLocalizedString("chooseaccount")."</h2>";
?>
        <form action="#" method="post" name="createplayer">
            <input size="25" maxlength="35" name="emailid" value="<?php echo $emailid; ?>" />
            <span class="texthelpers"><?echo getLocalizedString("email")?></span>
            <br />
            <input size="25" maxlength="15" name="password" value="<?php echo $password; ?>" />
            <span class="texthelpers"><?echo getLocalizedString("password")?></span>
            <br />
            <input size="25" maxlength="15" name="playername" value="<?php echo $playername; ?>" />
            <span class="texthelpers"><?echo getLocalizedString("playername")?></span>
            <input type="hidden" name="lang" value="<?php echo $lang; ?>"/>
            <input type="hidden" name="createplayer" value="requested"/>
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


    if ($actioncreate == "requested") {

        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        if (!$player->checkNonconformity()) {
            echo "<div id=\"createplayerbox\">";
                echo "<h2>".getLocalizedString("Here is your request for creating an account")."&nbsp;:</h2>";
                echo "<ul>";
                    echo "<li>".getLocalizedString("email")." : $emailid</li>";
                    echo "<li>".getLocalizedString("password")." : $password</li>";
                    echo "<li>".getLocalizedString("playername")." : $playername</li>";
                echo "</ul>";

    ?>
            <form action="#" method="post" name="createplayer">
                <input type="hidden" name="emailid" value="<?php echo $emailid; ?>"/>
                <input type="hidden" name="password" value="<?php echo $password; ?>"/>
                <input type="hidden" name="playername" value="<?php echo $playername; ?>"/>
                <input type="hidden" name="lang" value="<?php echo $lang; ?>"/>
                <input type="hidden" name="createplayer" value="confirmed"/>
                <input type="submit" value="<?php echo getLocalizedString("Confirm account creation ?"); ?>" />
            </form> 
    <?
            echo "</div>";
        } else {
            echo "<h2>INVALID</h2>";
            echo "<h2>".nl2br($player->error_string)."</h2>";
            printFormRequest($emailid, $password, $playername);
        }   
    } else if ($actioncreate == "confirmed") {
        $player = new playersPending();
        $player->email = $emailid;
        $player->playername = $playername;
        $player->setPassword($password);
        $player->setSeed();
        if (!$player->checkNonconformity()) $player->insert();
        if (!$player->error_status) {
        echo "<div id=\"createplayerbox\">";
            $player->mailValidationMessage();
            echo getLocalizedString("An email has been sent. Click on the link to validate.");

        echo "</div>";

        } else {
            echo "<h2>INVALID</h2>";
            echo "<h2>".nl2br($player->error_string)."</h2>";
            printFormRequest($emailid, $password, $playername);
        }   
    } else if ($actioncreate == "validate") {
        $seed = get_cgi_var("seed");
        $player = new playersPending($emailid, $seed);
        if (!$player->validate()) {
            print "ERRORRRR";
            print $player->error_string;
        } else {
            print "VALIDATED";
        }
    } else {
        printFormRequest();
    }
    
    include_once("includes/footer.inc");
?>
