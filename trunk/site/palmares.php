<?php 
    include_once("includes/header.inc");
    include_once("config.php");

    $lang=getCurrentLang();

    $palmares_type = htmlentities(quote_smart($_REQUEST['type']));
    
    if ( $palmares_type == 'user' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
        if (!(intval($idusers) > 0)) $idusers = getLoginId();
        
        $userobj = new users($idusers);
        echo '<h1>' . getLocalizedString('boatdescription') . '</h1>';
        echo '<ul>';
        echo '<li>' . getLocalizedString('login_id') . ' : ' . $userobj->idusers.'</li>';
        echo '<li>' . getLocalizedString('login_name') . ' : ' . $userobj->username.'</li>';
        echo '<li>' . getLocalizedString('boatname') . ' : ' . $userobj->boatname.'</li>';
        echo '<li>' . getLocalizedString('country') . ' : ' . $userobj->htmlFlagImg() . " ( " . $userobj->country. ' ) </li>';
        echo '</ul>';
        if ($userobj->engaged > 0) {
            $raceobj = new races($userobj->engaged);
            echo "<h2>" . sprintf( getLocalizedString('boatengaged'), $raceobj->htmlRacenameLink($lang), $raceobj->htmlIdracesLink($lang) ) . "</h2>";
            if ($idusers == getLoginId()) echo htmlAbandonButton($userobj->idusers, $userobj->engaged);
        } else {
            echo "<h2>" . getLocalizedString('boatnotengaged') . "</h2>";
        }
        echo "<h1>" . sprintf (getLocalizedString("palmares"),$idusers) . "</h1>";
        displayPalmares($lang, $idusers);
    } else if ( $palmares_type == 'flag' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idflag']));
        //TODO
    } else {
        echo "Nothing to display";
    }


    include_once("includes/footer.inc");
?>

