<?php
    include_once("includes/header.inc");
    include_once("config.php");

    // Controle de saisie / soumission
    $pilotmodes = array("autopilot" => 1, "windangle" => 2, "orthodromic" => 3, "bestvmg" => 4, "vbvmg" => 5, "bestspeed" => 6);
    if ( ! array_key_exists($pilotmode, $pilotmodes) ) {
        printf ("<h1>PILOTMODE = $pilotmode IS UNSUPPORTED.");
        printf ("<h2>Your IP address is : " . getip() . "</h2>\n");
        printf ("<h2>It has been logged. Don't try this again.</h2>\n");
        sleep (1);
        exit;
    } 

    if ( strspn($boatheading, "-+.0123456789") != strlen($boatheading)  ||
         strspn($pilotparameter, "-+.0123456789") != strlen($pilotparameter)  ) {

        printf ("<h1>This is not a nice thing to try this.</h1>");
        printf ("<h2>Your IP address is : " . getip() . "</h2>\n");
        sleep (1);
        printf ("<h2>It has been logged. Don't try this again.</h2>\n");
        exit;
    }

    if ($idusers != 0 ) {
        if ( $idusers == getLoginId() ) {

            //Contrôles amont...
            if ( $boatheading >= 360 ) $boatheading -= 360;
            if ( $boatheading < 0 ) $boatheading += 360;
            if ( $pilotparameter >= 360 ) $pilotparameter -= 360;
            if ( $pilotparameter > 180 ) $pilotparameter -= 360;
            if ( $pilotparameter < -180 ) $pilotparameter += 360;
            // $pilotmode est lié  à l ihm, on le bascule en int pour l'interne VLM
            $pim = $pilotmodes[$pilotmode];

            // Tout ça pour mettre à jour la ligne de la table users... on simplifie
            $fullUsersObj = new fullUsers($idusers);
            if ($fullUsersObj->writeNewheading($pim, $boatheading, $pilotparameter) === False) {
                die("<h1 class=\"error\">".$fullUsersObj->users->error_string."</h1>");
            }

            echo "<h1 class=\"info\">".getLocalizedString("angleupdated")."</h1>";
        } else {
            printf ("<h1>This is not a nice thing to try this.</h1>");
            printf ("<h2>Your IP address is : " . getip() . "</h2>\n");
            sleep (1);
            printf ("<h2>It has been logged. Don't try this again.</h2>\n");
        }     
    }

    include_once("includes/footer.inc");
?>
