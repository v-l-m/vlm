<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    //FIXME
    if (!isPlayerLoggedIn()) die('ERROR');

    $actioncreate = get_cgi_var("createboat");

    function printBoatSummary($boatpseudo = "", $boatname = "") {
        echo "<ul>";
            echo "<li>".getLocalizedString("boatpseudo")." : $boatpseudo</li>";
            echo "<li>".getLocalizedString("boatname")." : $boatname</li>";
        echo "</ul>";
    }

    function printFormRequest($boatpseudo = "", $boatname = "") {
        echo "<div id=\"createboatbox\">";
        echo "<h2>".getLocalizedString("Create your boat")."&nbsp;:</h2>";
?>
        <form action="#" method="post" name="createboat">
            <input size="25" maxlength="32" name="boatpseudo" value="<?php echo $boatpseudo; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("boatpseudo"); ?></span>
            <br />
            <input size="25" maxlength="64" name="boatname" value="<?php echo $boatname; ?>" />
            <span class="texthelpers"><?php echo getLocalizedString("boatname"); ?></span>
            <input type="hidden" name="createboat" value="requested" />
            <br />
            <input type="submit" />
        </form> 
        <p><?php echo nl2br(getLocalizedString("The boatpseudo is unique and not changeable later.")); ?></p>
    //FIXME
    <?php echo "<h2>OLD INTERFACE to create a single boat account is available <a href=\"create.php\">HERE</a> for testing purpose</h2>"; ?>

        </div>
<?php
    }

    $boatpseudo = htmlentities(get_cgi_var("boatpseudo"));
    $boatname = htmlentities(get_cgi_var("boatname"));

    if ($actioncreate == "requested") { //REQUESTED
        if (!checkLoginExists($boatpseudo)) {
            //FIXME : nom de boat correct ? 
            echo "<div id=\"createboatbox-request\">";
            echo "<h2>".getLocalizedString("Here is your request for creating a boat")."&nbsp;:</h2>";
            printBoatSummary($boatpseudo, $boatname);
?>
            <form action="#" method="post" name="createboat">
                <input type="hidden" name="boatpseudo" value="<?php echo $boatpseudo; ?>"/>
                <input type="hidden" name="boatname" value="<?php echo $boatname; ?>"/>
                <input type="hidden" name="createboat" value="create"/>
                <input type="submit" value="<?php echo getLocalizedString("Confirm boat creation request ?"); ?>" />
            </form> 
<?php
            echo "</div>";
        } else {
            echo "<h2>".getLocalizedString("This boatpseudo already exists").".</h2>";
            printFormRequest($boatpseudo, $boatname);
        }
    } else if ($actioncreate == "create") { //CREATE
        $player = getLoggedPlayerObject();

        echo "<div id=\"createboatbox\">";
        if (!checkLoginExists($boatpseudo) && $idu = createBoat($boatpseudo, $password = generatePassword($boatpseudo), $player->email, $boatname)) {
            $users = getUserObject($idu);
            echo "<h2>".getLocalizedString("Your boat has been created")."</h2>";
            printBoatSummary($boatpseudo, $boatname);
            echo "</div>";

            if ($users->setOwnerId($player->idplayers) && !$users->error_status) {
                echo "<div id=\"attachboatbox\">";
                echo '<h2>'.getLocalizedString("Attachment successful").'.</h2>';
                echo '<p>'.getLocalizedString('You own this boat').'.</p>';
                echo "</div>";
            } else {
                echo "<h2>".getLocalizedString("It was not possible to attach this boat. Please report this error.")."</h2>";
                if ($users->error_status) {
                    print nl2br($users->error_string);
                }
            }
        } else {
            echo getLocalizedString("Boat creation error");
            echo "</div>";
        }
    } else {
        $player = getLoggedPlayerObject();
        echo $player->htmlBoatCandidatesList();
        printFormRequest();

    }
    
    include_once("includes/footer.inc");
?>
