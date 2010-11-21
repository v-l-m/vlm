<?php 
    echo "<div id=\"raceslistbox\" class=\"basic\">";

    // Le user peut-t'il s'engager dans les courses ou est il déjà engagé
    $display_submit = FALSE;
    if (isLoggedIn()) {
        $users = getLoggedUserObject();
        $display_submit = ($users->engaged == 0);
        if ($display_submit) {
          	$av_races=availableRaces($users->idusers);
        }
    }

    // spf - we optimize the race object calls by creating the whole tab in one db call
    $result1 = queryRacesBatch("WHERE ((started=0 AND deptime>UNIX_TIMESTAMP()) OR ".
			       "(closetime > UNIX_TIMESTAMP())) ORDER BY started ASC,".
			       " deptime ASC, closetime ASC");

    // Recherche de toutes les courses prévues
    $allRacesRows = array();
    $allRacesIds  = array();
    while($row = mysql_fetch_array($result1, MYSQL_ASSOC)) {
      array_push($allRacesRows, $row);
      array_push($allRacesIds, $row['idraces']);      
    }
    $allNumOpponents = getNumOpponentBatch($allRacesIds);

    foreach($allRacesRows as $row) {
      $cur_race = new races($row['idraces'], $row);
      $dep          = $row['deptime'];
      $idraces      = $row['idraces'];
      $racename     = $row['racename'];
      $closetime    = $row['closetime'];
      $boattype     = $row['boattype'];
      $firstpcttime = $row['firstpcttime'];
      $maxboats     = $row['maxboats'];
      $racetype     = $row['racetype'];
      
      $numopps = $allNumOpponents[$idraces];
      $num_arrived = $numopps['num_arrived'];
      $num_racing  = $numopps['num_racing'];
      $num_engaged = $numopps['num_engaged'];
      
      $time = time();
      $duration = $dep - time();

        if ( $dep < $time AND $closetime <$time ) { //no race planned
            printf("      ".getLocalizedString("norace")."\n");
        } else {
            echo "<table class=\"racesummary-type$racetype\">\n<tr class=\"racesummary-type$racetype\">\n";
            echo "<td class=\"racesummary-minimap\">";
            // Carte de la course
            $href = "/".DIRECTORY_RACEMAPS."/".$idraces.".png";
            echo "<a href=\"$href\" rel=\"lightbox[vlm]\" title=\"$racename\">" .
                 "<img src=\"/" . DIRECTORY_MINIMAPS . "/" . $idraces . ".png".
                 "\" alt=\"" .getLocalizedString("racemap"). "\" /></a>";
            if ($display_submit && in_array($idraces, $av_races)) {
                // Subscribe button/form
                echo "<form action=\"myboat.php\" method=\"post\">
                      <input type=\"hidden\" name=\"idraces\" value=" . $idraces . ">
                      <input type=\"hidden\" name=\"idusers\" value=" . $users->idusers . ">
                      <input type=\"hidden\" name=\"type\" value=\"subscribe\">
                      <input type=\"submit\" value=\"" . getLocalizedString("subscribe") . "\" >
                      </form>";
            } 
            echo "</td>\n";
      
            echo "<td class=\"racesummary-type$racetype\">";
            //Les fonction html* sont destinées à normaliser l'ihm (une même information pointe toujours vers la même page)
            $urlidraces  = htmlIdracesLink($idraces); // pointe vers les ICS
            $urlboattype = htmlBoattypeLink($boattype); //pointe vers la polaire
            $urlracename = htmlRacenameLink($idraces, $racename, 0); // pointe vers le classement de la course
            
            $urlnumengaged = sprintf("<a href=\"races.php?type=racing&amp;idraces=%d\">%s</a>", $idraces, $num_engaged);
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
            // Affichage des IC destinées à la racelist
            printf("    <li><a href=\"/ics.php?idraces=%s\">WP(s)</a>" , $idraces);
            printf(" - %s nm - %s : %d minutes</li>\n" , round($cur_race->getRaceDistance()), getLocalizedString('crank'),  $cur_race->vacfreq);
            foreach ( $cur_race->getICS() as $ic) {
                if (($ic['flag'] & IC_FLAG_VISIBLE) and (IC_FLAG_RACELIST & $ic['flag']) ) {
                    if ($ic['flag'] & IC_FLAG_LINKFORUM) {
 		        printf ("    <li class=\"icflagracelist\"><a class=\"icflagforum\" href=\"".htmlentities($ic['instructions'])."\" target=\"_ic\">".getLocalizedString("icforum")."</a></li>\n");
                    } else {
		        printf ("    <li class=\"icflagracelist\">".htmlentities($ic['instructions'])."</li>\n");
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
