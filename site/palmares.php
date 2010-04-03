<?php 
    include_once("includes/header.inc");
    include_once("config.php");
    include_once("includes/strings.inc");

//    header("Content-type: text/html; charset=utf-8");

    $lang=getCurrentLang();

    $palmares_type = htmlentities(quote_smart($_REQUEST['type']));
    
    if ( $palmares_type == 'user' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
        
        $userobj = new users($idusers);
        echo '<h1>' . $strings[$lang]['boatdescription'] . '</h1>';
        echo '<ul>';
        echo '<li>' . $strings[$lang]['login_id'] . ' : ' . $userobj->idusers.'</li>';
        echo '<li>' . $strings[$lang]['login_name'] . ' : ' . $userobj->username.'</li>';
        echo '<li>' . $strings[$lang]['boatname'] . ' : ' . $userobj->boatname.'</li>';
        echo '<li>' . $strings[$lang]['country'] . ' : ' . $userobj->htmlFlagImg() . " ( " . $userobj->country. ' ) </li>';
        echo '</ul>';
        if ($userobj->engaged > 0) {
            $raceobj = new races($userobj->engaged);
            echo "<h2>" . sprintf( $strings[$lang]['boatengaged'], $raceobj->htmlRacenameLink($lang), $raceobj->htmlIdracesLink($lang) ) . "</h2>";
        } else {
            echo "<h2>" . $strings[$lang]['boatnotengaged'] . "</h2>";
        }
        echo "<h1>" . sprintf ($strings[$lang]["palmares"],$idusers) . "</h1>";
        displayPalmares($lang, $idusers);
    } else if ( $palmares_type == 'flag' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idflag']));
        //TODO
    } else {
        echo "Nothing to display";
    }


    include_once("includes/footer.inc");
?>

