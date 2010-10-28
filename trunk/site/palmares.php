<?php 
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $palmares_type = htmlentities(get_cgi_var('type'));
    
    if ( $palmares_type == 'user' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
        if (!(intval($idusers) > 0)) $idusers = getLoginId();
        
        $userobj = getUserObject($idusers);
        echo '<h1>' . getLocalizedString('boatdescription') . "&nbsp;:&nbsp;".$userobj->boatname. '</h1>';
        echo '<ul>';
        if ( $userobj->getOwnerId() == 0  ) {
            $msg = getLocalizedString("This boat has no owner.");
            if ($idusers == getLoginId()) {
                $msg .= " ".getLocalizedString("Please attach it to a player !")."&nbsp<a href=\"create_player.php\">".getLocalizedString("Click here")."</a>.";
            }
            echo "<li><b>".$msg."</b></li>";
        } else {
            $player = getPlayerObject($userobj->getOwnerId());          
            echo '<li>' . getLocalizedString('owner') . ' : ' . $player->htmlPlayername();
            if (isPlayerLoggedIn() && getPlayerId() == $userobj->getOwnerId()) {
                echo "&nbsp;(".getLocalizedString("You").")";
            }
            
            echo '</li>';
        }            
        echo '<li>' . getLocalizedString('login_id') . ' : #' . $userobj->idusers.'</li>';
        echo '<li>' . getLocalizedString('boatpseudo') . ' : ' . $userobj->username.'</li>';
        //FIXME : flag pour ne plus l'afficher si vieille API est dépréciée
        if (isPlayerLoggedIn() && in_array($userobj->idusers, getLoggedPlayerObject()->getOwnedBoatIdList())) {
            echo "<li><em>" . getLocalizedString('boatpassword') . ' ('.getLocalizedString("Temporarily available for compatibility").') : '. $userobj->password. '</em></li>';
        }
        echo '<li>' . getLocalizedString('boatname') . ' : ' . $userobj->boatname.'</li>';
        echo '<li>' . getLocalizedString('country') . ' : ' . $userobj->htmlFlagImg() . " ( " . $userobj->country. ' ) </li>';
        echo '</ul>';
        if (isPlayerLoggedIn() && in_array($userobj->idusers, getLoggedPlayerObject()->getManageableBoatIdList())) {
            echo "<hr /><ul>";
            echo "<li><a href=\"userlogs.php?idusers=".$userobj->idusers."\">".getLocalizedString('Recent actions') . '</a></li>';
            echo "</ul>";
        }
        echo "<hr />";
        if ($userobj->engaged > 0) {
            $raceobj = new races($userobj->engaged);
            echo "<h2>" . sprintf( getLocalizedString('boatengaged'), $raceobj->htmlRacenameLink(), $raceobj->htmlIdracesLink() ) . "</h2>";
            if ($idusers == getLoginId()) echo htmlAbandonButton($userobj->idusers, $userobj->engaged);
        } else {
            echo "<h2>" . getLocalizedString('boatnotengaged') . "</h2>";
        }
        echo "<h1>" . sprintf (getLocalizedString("palmares"), $userobj->boatname) . "</h1>";
        displayPalmares($idusers);
    } else if ( $palmares_type == 'player' ) {
        if (!isPlayerLoggedIn()) {
            echo "<h2>".getLocalizedString('You are not logged in with a player account.')."</h2>";
            echo "<h2>".getLocalizedString('You are not allowed to view player info.')."</h2>";
        } else {
            $idplayers = get_cgi_var('idplayers', getPlayerId());
            $player = getPlayerObject($idplayers);
            if (!is_null($player)) {
                echo "<h2>".getLocalizedString('playername') . ' : ' . $player->htmlPlayername().'</h2>';
                echo "<ul>";
                echo "<li>".getLocalizedString("idplayer") . ' : @' . $idplayers .'</li>';
                echo "</ul>";
                echo "<hr />";
                echo "<h2>".getLocalizedString('Boats of this player') . ' : </h2>';
                if ($player->idplayers == getPlayerId()) {
                    echo $player->htmlBoatManageableList();
                } else {
                    echo $player->htmlBoatOwnedList();
                }
                if (count($player->getBoatRecentlyBoatsittedIdList()) > 0) {
                    echo "<h2>".getLocalizedString('Recently boatsitted') . ' : </h2>';
                    echo $player->htmlBoatRecentlyBoatsittedList();
                }                

                if ($player->idplayers == getPlayerId()) {
                    echo "<hr /><ul>";
                    echo "<li><a href=\"playerlogs.php\">".getLocalizedString('Recent actions') . '</a></li>';
                    echo "<li><a href=\"create_boat.php\">".getLocalizedString('Create your boat') . "</a>. <a href=\"attach_owner.php\">".getLocalizedString("You may also attach a pre-existing boat").'.</a></li>';
                    echo "<li><a href=\"modify_password.php\">".getLocalizedString('Change your password') . '</a></li>';
                    echo "<li><a href=\"manage_skippers.php\">".getLocalizedString("Boat-sitting management") . '</a></li>';
                }


            } else {
                echo "<h2>".getLocalizedString("This player account does not exist.")."</h2>";
            }
        }
    } else if ( $palmares_type == 'flag' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idflag']));
        //TODO
    } else {
        echo "Nothing to display";
    }


    include_once("includes/footer.inc");
?>

