<?php 
    echo "<div id=\"raceslistbox\" class=\"basic\">";

    // Le user peut-t'il s'engager dans les courses ou est il d�j� engag�
    $display_submit = "false";
    if ( isLoggedIn() ) {
        $users = new Users(getLoginId());
        if ( $users->engaged == 0 ) {
            $display_submit = "true";
            $av_races=availableRaces($users->idusers);
         }
    }

    // Type of race (permanent / one shot )
    $q=htmlentities(quote_smart($_REQUEST['type'])) ;

    //change that, no SQL should appear directly in scripts
    $query1 = "SELECT deptime,idraces,racename,closetime,boattype,firstpcttime,maxboats,racetype FROM races  ";
    $query1 .= " WHERE ( ( started = 0 AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) ) ";
    $query1 .= " ORDER BY started ASC, deptime ASC, closetime ASC ";

    $result1 = wrapper_mysql_db_query_reader($query1) or die("Query [$query1] failed \n");

    // Recherche de toutes les courses pr�vues
    while($row = mysql_fetch_array($result1, MYSQL_ASSOC)) {
        $dep = $row['deptime'];
        $idraces = $row['idraces'];
        $racename = $row['racename'];
        $closetime = $row['closetime'];
        $boattype = $row['boattype'];
        $firstpcttime = $row['firstpcttime'];
        $maxboats = $row['maxboats'];
        $racetype = $row['racetype'];

        list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);

        $time = time();
        $duration = $dep - time();

        if ( $dep < $time AND $closetime <$time ) { //no race planned
            printf("      ".getLocalizedString("norace")."\n");
        } else {
            echo "<table class=\"racesummary-type$racetype\">\n<tr class=\"racesummary-type$racetype\">\n";
            echo "<td class=\"racesummary-minimap\">";
            // Carte de la course
            $href = "/racemap.php?idraces=".$idraces;
            echo "<a href=\"$href\" rel=\"lightbox[vlm]\" title=\"$racename\">" .
                 "<img src=\"minimap.php?idraces=" . $idraces . 
                 "\" alt=\"" .getLocalizedString("racemap"). "\" /></a>";
            if ( $display_submit == "true" and in_array($idraces, $av_races) ) {
                // Subscribe button/form
                echo "<form action=\"myboat.php\" method=\"post\">
                      <input type=\"hidden\" name=\"idraces\" value=" . $idraces . ">
                      <input type=\"hidden\" name=\"idusers\" value=" . $fullUsersObj->users->idusers . ">
                      <input type=\"hidden\" name=\"type\" value=\"subscribe\">
                      <input type=\"hidden\" name=\"lang\" value=\"" . $lang . "\">
                      <input type=\"submit\" value=\"" . getLocalizedString("subscribe") . "\" >
                      </form>";
            } 
            echo "</td>\n";
      
            echo "<td class=\"racesummary-type$racetype\">";
            //Les fonction html* sont destin�es � normaliser l'ihm (une m�me information pointe toujours vers la m�me page)
            $urlidraces = htmlIdracesLink($lang, $idraces); // pointe vers les ICS
            $urlboattype = htmlBoattypeLink($boattype); //pointe vers la polaire
            $urlracename = htmlRacenameLink($lang, $idraces, $racename); // pointe vers le classement de la course
            
            $urlnumengaged = sprintf("<a href=\"races.php?lang=%s&amp;type=racing&amp;idraces=%d\">%s</a>", $lang, $idraces, $num_engaged);
            if ( $maxboats != 0 ) {            
                $strengaged = " - ".getLocalizedString("engaged")." / Total : " . $urlnumengaged . "/" . $maxboats ;
            } else {
                $strengaged = " - ".getLocalizedString("engaged")." : " . $urlnumengaged . " (".getLocalizedString("unlimited").")";
            }

            printf("<h1>%s (%s) - %s</h1>\n",$urlidraces, $urlboattype, $urlracename); /* FIXME use style there */

            printf ("  <ul>");
            if ( $dep > $time ) {
                $duration = duration2string($duration);
                printf("    <li>".getLocalizedString("nextrace") . $strengaged."</li>\n" , $duration['days'],$duration['hours'],$duration['minutes']);
                printf("    <li>".getLocalizedString("startrace"). gmdate("Y/m/d H:i:s", $dep)." GMT.</li>\n");
            } else {
                $duration = duration2string(-$duration);
                printf("    <li>".getLocalizedString("openrace") . $strengaged."</li>\n", $duration['days'],$duration['hours'],$duration['minutes']);
            }
            if ($racetype == RACE_TYPE_RECORD) {
                echo "<li>".getLocalizedString("This is a permanent/record race").".</li>";
            }
        
            if ( $closetime > $dep ) {
                printf("    <li>".getLocalizedString("closerace"). gmdate("Y/m/d H:i:s", $closetime)." GMT</li>\n");
            }
      
            if ( $firstpcttime >0 ) {
                printf("    <li>".getLocalizedString("endrace"). "%d %%</li>\n", 100 + $firstpcttime );
            } else {
                printf("    <li>".getLocalizedString("endrace")." no limit</li>\n");
            }
            // Affichage des IC destin�es � la racelist
            $fullRacesObj = new fullRaces($idraces);
            printf("    <li><a href=\"/ics.php?lang=%s&amp;idraces=%s\">WP(s)</a>" , $lang,$idraces);
            printf(" - %s nm - %s : %d minutes</li>\n" , round($fullRacesObj->races->getRaceDistance()), getLocalizedString('crank'),  $fullRacesObj->races->vacfreq);
            foreach ( $fullRacesObj->races->getICS() as $ic) {
                if (($ic['flag'] & IC_FLAG_VISIBLE) and (IC_FLAG_RACELIST & $ic['flag']) ) {
                    if ($ic['flag'] & IC_FLAG_LINKFORUM) {
                        printf ("    <li class=\"icflagracelist\"><a class=\"icflagforum\" href=\"".$ic['instructions']."\" target=\"_ic\">".getLocalizedString("icforum")."</a></li>\n");
                    } else {
                        printf ("    <li class=\"icflagracelist\">".$ic['instructions']."</li>\n");
                    }
                }
            }

          printf ("  </ul>");
          echo "</td>\n";

          echo "</tr>\n</table>\n";

        }
    }

    echo "</div>";

?>