<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $actionattach = get_cgi_var("attachboat");
    $emailid = get_cgi_var("emailid");
    $password = get_cgi_var("password");

    function printAttachmentSummary($emailid = "", $playername = "") {
        echo "<h2>".getLocalizedString("Attachment to this account")."</h2>";
        echo "<ul>";
            echo "<li>".getLocalizedString("email")." : $emailid</li>";
            echo "<li>".getLocalizedString("playername")." : $playername</li>";
        echo "</ul>";
        echo "<h2>".getLocalizedString("Boat to attach")."</h2>";
        echo "<ul>";
            echo "<li>".getLocalizedString("Boat id")." : ".getLoginId()."</li>";
            echo "<li>".getLocalizedString("Boat login")." : ".getLoginName()."</li>";
        echo "</ul>";

    }

    function printFormRequest($emailid = "", $password = "") {
        echo "<div id=\"attachboatbox\">";
        echo "<h2>".getLocalizedString("Here you can attach your boat to your player account. Please input your credentials.")."</h2>";
?>
        <form action="#" method="post" name="attachboat">
            <input size="25" maxlength="64" name="emailid" value="<?php echo $emailid; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("email"); ?></span>
            <br />
            <input size="25" maxlength="15" name="password" value="<?php echo $password; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("password"); ?></span>
            <input type="hidden" name="attachboat" value="requested" />
            <br />
            <input type="submit" />
        </form> 
        <p><?php echo getLocalizedString("Log out to create a player account"); ?>.</p>
        </div>
<?php
    }

    if (isPlayerLoggedIn() || !isLoggedIn()) { //il ne faut pas être loggué en tant que player, il faut être loggué en tant que boat
        echo "<div id=\"attachboatbox\">";
        echo "<p>";
        echo getLocalizedString("You have to be logged with the user/boat credential to attach the boat.");
        echo "</p></div>";
        include_once("includes/footer.inc");
        exit();
    }
    
    $users = getLoggedUserObject();
    
    if ($users->getOwnerId() > 0) { //no way to reattach a boat
        $player = getPlayerObject($users->getOwnerId());
        echo "<div id=\"attachboatbox\">";
        echo "<p>";
        echo getLocalizedString("Current boat is already attached to the following player :")."&nbsp;";
        echo $player->htmlPlayername();
        echo "</p></div>";
        include_once("includes/footer.inc");
        exit();
    }
    
    /* At this point :
     * - boat credentials are checked
     * - we are logged in as a boat
     * - the boat is not already attached to someone
     */
     
    if ($actionattach == "requested") { //REQUESTED
        $player = new players(0, $emailid);
        if ($player->checkPassword($password)) {
            echo "<h2>".getLocalizedString("Here is your request for attaching this boat")."&nbsp;:</h2>";
            echo "<div id=\"attachboatbox-request\">";
            printAttachmentSummary($player->email, $player->playername);
?>
            <form action="#" method="post" name="attachboat">
                <input type="hidden" name="emailid" value="<?php echo $emailid; ?>"/>
                <input type="hidden" name="password" value="<?php echo $password; ?>"/>
                <input type="hidden" name="attachboat" value="confirmed"/>
                <input type="submit" value="<?php echo getLocalizedString("Confirm attachment request ?"); ?>" />
            </form> 
<?php
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("Player account is not valid or not created.")."</h2>";
            printFormRequest($emailid, $password);
        }
    } else if ($actionattach == "confirmed") { //CONFIRMED
        $player = new players(0, $emailid);
        if (!$player->error_status && $player->checkPassword($password)) {
            if ($users->setOwnerId($player->idplayers) && !$users->error_status) {
                echo "<div id=\"attachboatbox\">";
                echo '<h2>'.getLocalizedString("Attachment successful").'.</h2>';
                printAttachmentSummary($player->email, $player->playername);
                echo "</div>";
            } else {
                echo "<h2>".getLocalizedString("It was not possible to attach this boat. Please report this error.")."</h2>";
                if ($users->error_status) {
                    print nl2br($users->error_string);
                }
                printFormRequest($emailid, $password);
           }   
       }
    } else {
        printFormRequest();
    }
    include_once("includes/footer.inc");
  
?>
