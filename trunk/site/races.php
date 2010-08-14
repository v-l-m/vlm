<?php
    include_once("includes/header.inc");
    include_once("config.php");

    /*
     * Affichage de l'onglet 
     * de choix du classement 
     */
    function displayRankingMenu($fullRacesObj, $tableType, $extra_arg) {
        //echo "<H1>" ; printf (getLocalizedString("palmares"),$idusers); echo "</H1>";
        echo "<h1>".getLocalizedString("rankingtype")."</h1>";

        $tables = array();

        // Ajout en début de tableau d'un classement par WP (uniquement si >1)
        $nbWPs = $fullRacesObj->races->getWPsCount();
        if ( $nbWPs > 1 ) {
            for ($wp_num=1; $wp_num < $nbWPs; $wp_num++) {
              	$WP=$fullRacesObj->races->giveWPCoordinates($wp_num);
              	if ( !strstr($WP['wptypelabel'],'IceGate') ) {
                	  array_push($tables, "WP" . $wp_num);
              	}
            }
        }

        array_push($tables, "arrived", "racing", "dnf", "abd", "htp", "hc");
        if ( ! in_array($tableType, $tables) ) {
             printf ("<h1>You should not do that, this type of ranking is not accepted : %s</h1>\n", $tableType); exit;
        }

        echo "<table>\n<tr>";

        // Affichage des classements classiques 
        foreach ($tables as $table) {
            if ( $table == $tableType ) {
                $class="class=\"hilight\"";
                if ( strstr(substr($table, 0, 2), "WP") ) {
                    $WP=$fullRacesObj->races->giveWPCoordinates(substr($table,2));
                    $cellcontent=$WP['libelle']."<br />(".$table.")";
                } else {
                    $cellcontent=ucfirst($table);
                }
            } else {
                $class="class=\"nohilight\"";
                if ( strstr(substr($table, 0, 2), "WP") ) {
                    //$tlabel=substr($table,2);
                    $tlabel=$WP['libelle']."<br />(".$table.")";
                } else {
                    $tlabel=$table;
                }
                $cellcontent="<a href=\"" . $_SERVER["PHP_SELF"] . "?idraces=" . $fullRacesObj->races->idraces . "&amp;type=".$table. "\">";
                $cellcontent.=ucfirst($tlabel);
                $cellcontent.="</a>";
            }

            echo "<td " . $class . ">\n";
            echo "$cellcontent";

            echo "</td>\n";
        }
        echo "</tr></table>";

    }

    function displayPrevious100($startnum) {
        // Si on ne part pas de 1, on propose le bouton "-100"
        if ( $startnum > 1 ) {
            $FORMULAIRE="<form method=\"get\" name=\"moins\" action=\"" . $_SERVER["PHP_SELF"] . "\">\n";

            $new_startnum=$startnum-MAX_BOATS_ON_RANKINGS;
            if ( $new_startnum < 0 ) $new_startnum=1;
            $FORMULAIRE.="<input type=\"hidden\" name=\"startnum\" value=\"$new_startnum\" />\n";

            foreach($_REQUEST as $keyname => $value) {
                if ( $keyname != "startnum" && $keyname != "PHPSESSID" ) {
                    $FORMULAIRE.="<input type=\"hidden\" name=\"$keyname\" value=\"$value\" />\n";
                }
            }
            $FORMULAIRE.="<input type=\"submit\" value=\"&lt; " . MAX_BOATS_ON_RANKINGS . "\" />\n";
            $FORMULAIRE.="</form>\n";
            echo $FORMULAIRE;
        } else { //sinon, on le pose desactive pour garder la coherence d'ensemble
            echo "<input disabled=\"disabled\" type=\"button\" value=\"&lt; " . MAX_BOATS_ON_RANKINGS . "\" />\n";
        }
    }

    function displayNext100($startnum, $num_engaged) {
        // Si on ne part pas de 1, on propose le bouton "+100"
        $new_startnum=$startnum+MAX_BOATS_ON_RANKINGS;
        if ( $startnum >= 1 && $new_startnum  < $num_engaged ) {
            $FORMULAIRE="<form method=\"get\" name=\"plus\" action=\"" . $_SERVER["PHP_SELF"] . "\" />\n";
            $FORMULAIRE.="<input type=\"hidden\" name=\"startnum\" value=\"$new_startnum\" />\n";

            foreach($_REQUEST as $keyname => $value) {
                if ( $keyname != "startnum" && $keyname != "PHPSESSID" ) {
                    $FORMULAIRE.= "<input type=\"hidden\" name=\"$keyname\" value=\"$value\" />\n";
                }
            }
            $FORMULAIRE.="<input type=\"submit\" value=\"&gt; " . MAX_BOATS_ON_RANKINGS . "\" />\n";
            $FORMULAIRE.="</form>\n";
            echo $FORMULAIRE;
        } else { //sinon, on le pose desactive pour garder la coherence d'ensemble
            echo "<input disabled=\"disabled\" type=\"button\" value=\"&gt; " . MAX_BOATS_ON_RANKINGS . "\" />\n";
        }

    }

    // Query and QueryArgument

    /*
    VARIABLES d'APPEL : q= idraces= idusers= sortkey= sortorder= disttype= startnum=
    $idusers=htmlentities(quote_smart($_REQUEST['idusers']));

    */


    if ( get_cgi_var('full', 'no') == "yes" ) {
        $startnum=0;
    } else {
        $startnum=max(1, get_cgi_var('startnum', 0)) ;
    }

    $q = htmlentities(get_cgi_var('type', "arrived")) ;

    $sortkey = htmlentities(get_cgi_var('sortkey', "")) ;
    $sortorder = strtolower(htmlentities(get_cgi_var('sortorder', 'asc'))) ;
    $disttype = strtolower(htmlentities(get_cgi_var('disttype', 'tonm'))) ;
    // Analyse de la clé de tri demandée
    $sortkeys = array("", "idusers", "userdeptime", "loch", "last1h", "last3h","last24h","latitude","longitude");
    if ( ! in_array($sortkey, $sortkeys, TRUE) ) {
        $sortkey="";
        printf ("<h1>BAD sortkey : %s, defaulting to nextwaypoint and distance</h1>\n", $sortkey); 
    }
    if ($sortorder != "asc" and $sortorder != "desc") $sortorder="asc";
    if ($disttype != "tofirst" and $disttype != "tonm") $disttype="tonm";

    if ( $q == "palmares" ) {
        $idusers = htmlentities(get_cgi_var('idusers', 0));
        echo "<h1>" ; printf (getLocalizedString("palmares"),$idusers); echo "</h1>";
        displayPalmares($idusers);
    }

    echo "<span id=\"infobulle\">
          </span>";

    if ($idraces != 0) {

        $fullRacesObj = new fullRaces($idraces);
        list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);
        if ( $startnum > 0 ) {
            $startnum-=$num_arrived;
        }

        //show alls races
        echo "<div id=\"raceheader\">\n";
            printf("<h3>".getLocalizedString("racestarted")."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));
            echo "<h3><a href=\"ics.php?idraces=".$idraces."\">".getLocalizedString("ic")."</a></h3>";
        echo "</div>\n";     

        // Carte de la course
        echo htmlTinymap($fullRacesObj->races->idraces, $fullRacesObj->races->racename);

        echo "<table class=\"boat\"><tr class=\"boat\">";
        echo "<td class=\"boat\">";
        // ** Onglet de choix sur différents classements
        displayRankingMenu($fullRacesObj, $q, $sortkey);
        echo "</td><td class=\"boat\" valign=\"bottom\">";
        displayPrevious100($startnum);
        echo "</td><td class=\"boat\" valign=\"bottom\">";
        displayNext100($startnum, $num_engaged);
        echo "</td>";
        echo "</tr></table>";
      
        echo "<a name=\"ARR\" id=\"ARR\"></a>";

        if ( $q == "arrived" ) {
            if ( isset($sortkey) && $sortkey != "" ) {
                $numarrived=$fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ARR, $sortkey , $sortorder, 0, $startnum);
            } else {
                if ( $fullRacesObj->races->racetype == RACE_TYPE_RECORD ) {
                    // dispHtmlResults : 2 derniers paramètres = critère de tri + ordre (asc/desc)
                    // Pour une course record : c'est le temps de course par défaut
                    $numarrived=$fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ARR, "duration" , "asc" ,0,  $startnum);
                } else {
                  // Pour une course classique, c'est tout simplement la date d'arrivée (on l'a avec deptime + duration)
                  $numarrived=$fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ARR, "arrtime", "asc", 0, $startnum);
                }
            }
        }

        if ( strstr($q, "WP") ) {
            //echo "<H1>Classement au WP ".substr($q,2) ."</H1>";
            if ( $fullRacesObj->races->racetype == RACE_TYPE_RECORD ) {
                // Pour une course record : c'est le temps de course par défaut
                $numarrived=$fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ARR, "duration" , "asc" , substr($q,2), 0, $startnum);
            } else {
                // Pour une course classique, c'est tout simplement la date d'arrivée (on l'a avec deptime + duration)
                $numarrived=$fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ARR, "arrtime", "asc", substr($q,2), 0, $startnum);
            }
        }
      
        if ( $q == "racing" ) {
            if ( $fullRacesObj->races->started ) {
                echo "<a name=\"ENC\" id=\"ENC\"></a>";
                if ( isset($sortkey) && $sortkey != "" ) {
                    $fullRacesObj->dispHtmlClassification($num_arrived , $sortkey . " " . $sortorder , $disttype, $startnum);
                } else {
                    $fullRacesObj->dispHtmlClassification($num_arrived, "nwp desc, dnm asc", $disttype, $startnum);
                }
            } else {
                printf( "<h3>". getLocalizedString("hasnotstart")."<br /><b>\n",$fullRacesObj->races->racename);
                $departure = gmdate("Y/m/d H:i:s",$fullRacesObj->races->deptime)." GMT";
                echo "</b>" . getLocalizedString("departuredate")." : $departure </h3>\n";
                echo "<h4>".getLocalizedString("playersengaged")."</h4>";
                $fullRacesObj->dispHtmlEngaged($startnum);
            }
        }
      
      
        if ( $q == "htp" ) {
            echo "<a name=\"HTP\" id=\"HTP\"></a>";
            $fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_HTP, "duration" , "asc", 0, $startnum);
        }
        if ( $q == "dnf" ) {
            echo "<a name=\"DNF\" id=\"DNF\"></a>";
            $fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_DNF, "duration" , "asc", 0, $startnum);
        }
        if ( $q == "abd" ) {
            echo "<a name=\"ABD\" id=\"ABD\"></a>";
            $fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_ABD, "duration" , "asc", 0, $startnum);
        }
        if ( $q == "hc" ) {
            echo "<a name=\"HC\" id=\"HC\"></a>";
            $fullRacesObj->dispHtmlRacesResults(BOAT_STATUS_HC, "duration" , "asc", 0, $startnum);
        }
      
        echo "<p>";
        echo '<a href="races.php">';
        echo getLocalizedString("see")."</a>"; 
        echo "</p>";

        echo "<table class=\"boat\"><tr class=\"boat\">";
        echo "<td class=\"boat\">";
        // ** Onglet de choix sur différents classements
        displayRankingMenu($fullRacesObj, $q, $sortkey);
        echo "</td><td class=\"boat\" valign=\"bottom\">";
        displayPrevious100($startnum);
        echo "</td><td class=\"boat\" valign=\"bottom\">";
        displayNext100($startnum, $num_engaged);
        echo "</td>";
        echo "</tr></table>";

    } else  { //idraces ==0 means display all races
        if (isset($_REQUEST['fulllist']) and $_REQUEST['fulllist']=1) {
            echo "<h4>".getLocalizedString("races")."</h4>";
            dispHtmlRacesList();
        } else {
            echo "<h4>".getLocalizedString("current_races")."&nbsp;(<a href=\"races.php?fulllist=1\">".getLocalizedString("see")."</a>)"."</h4>";
            dispHtmlCurrentRacesList();
        }
    }

    include_once("includes/footer.inc");
?>
