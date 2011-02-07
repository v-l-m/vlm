<?php
/*
this page is the control panel for the boat.
it include information, a boat image,
3 forms to choose boat course
javascripts to directly compute speed
links
it'a little bit messy (html+java+php)
*/
include_once("vlmc.php");
include_once("includes/header.inc");

  $usersObj = new fullUsers(getLoginId());

  if ( $usersObj->users->engaged == 0 ) {


    // Le palmares du joueur
    echo "<h1>" ; printf (getLocalizedString("palmares"), $usersObj->users->htmlIdusers()); echo "</h1>";
    displayPalmares($usersObj->users->idusers);

    // S'engager dans une course
    printf("<h3>" . getLocalizedString("notengaged") . "</h3>");
    //include ("subscribe_race.php");
    include ("includes/raceslist.inc");
  } else {

    $myRace = &$usersObj->races;

    echo "<!-- DELAY_BETWEEN_UPDATES=" .  60*$myRace->vacfreq . "-->\n";

    // 2008/01/14 : DESACTIVE ICI, pour accelerer le refresh de la page.
    // 2008/01/19 : REACTIVE AVEC PREFERENCE, tant pis

    $autoUpdateAngles = $usersObj->getMyPref("autoUpdateAngles");
    if ( $autoUpdateAngles != "false" ) {
        $usersObj->updateAngles();
    }

    $winddir = (360 - $usersObj->wheading ) + 90;
    while ( $winddir > 360 ) $winddir-=360;
    while ( $winddir < 0 ) $winddir+=360;

    if ( $usersObj->users->pilotmode == PILOTMODE_HEADING
        OR $usersObj->users->pilotmode == PILOTMODE_BESTVMG
        OR $usersObj->users->pilotmode == PILOTMODE_VBVMG
//      OR $usersObj->users->pilotmode == PILOTMODE_BESTSPD
        ) {

        $boatdir = (360 - $usersObj->users->boatheading ) ;

    } elseif ( $usersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
        //$boatdir = $usersObj->users->pilotparameter + $usersObj->wheading ;
        $boatdir = (($usersObj->wheading) + $usersObj->users->pilotparameter);
    } elseif ( $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC ) {
         $boatdir = $usersObj->orthodromicHeading();
    }
    //$boatdir = (360 - $usersObj->users->boatheading )%360 + 90;
    //$boatdir = (360 - $usersObj->users->boatheading ) + 90;

    // Vérification de la valeur de boatdir..
    if ( abs($boatdir) > 720 ) errorprint("Problem with BOATHEADING, please check value");

    $boatdir=$boatdir%360;
    while ( $boatdir > 360 ) $boatdir-=360;
    while ( $boatdir < 0 ) $boatdir+=360;

    $twa = $winddir - $boatdir;
    if ($twa < -180 ) $twa +=360;
    if ($twa > 180 ) $twa -=360;

    if ( $twa > 0 ) {
       $amure = "tribord";
    } else {
       $amure = "babord";
    }

    // Retrive user_prefs
    $mapOpponents = $usersObj->getMyPref("mapOpponents");
    if ( $mapOpponents == "NULL" ) $mapOpponents="myboat";
    $mapTools = $usersObj->getMyPref("mapTools");
    if ( $mapTools == "NULL" ) $mapTools="compas";
    $mapCenter = $usersObj->getMyPref("mapCenter");
    if ( $mapCenter == "NULL" ) $mapCenter="myboat";
    $mapArea = $usersObj->getMyPref("maparea");
    if ( $mapArea == "NULL" ) $mapArea=10;
    $mapAge = $usersObj->getMyPref("mapAge");
    if ( $mapAge == "NULL" ) $mapAge=3;
    $mapLayers = $usersObj->getMyPref("mapLayers");
    if ( $mapLayers == "NULL" ) $mapLayers="merged";
    $mapMaille = $usersObj->getMyPref("mapMaille");
    if ( $mapMaille == "NULL" ) $mapMaille=3;
    $mapEstime = $usersObj->getMyPref("mapEstime");
    if ( $mapEstime == "NULL" ) $mapEstime=50;
    $mapX = $usersObj->getMyPref("mapX");
    if ( $mapX == "NULL" ) $mapX = 800;
    $mapY = $usersObj->getMyPref("mapY");
    if ( $mapY == "NULL" ) $mapY = 600;
    $mapDrawtextwp = $usersObj->getMyPref("mapDrawtextwp");
    if ( $mapDrawtextwp == "NULL" ) $mapDrawtextwp = "on";

    //printf ("mO=%s, mT=%s, mC=%s\n",$mapOpponents,$mapTools,$mapCenter);
    //printf("amure=%s<BR/>",$amure);

include_once("scripts/myboat.js");
?>
<!-- Affichage de la page -->
<div id="statusbox">
  <div id="infobulle"></div>
  <div id="racebox">
    <div id="minimapbox">
        <?php
        // Carte de la course
        echo htmlTinymap($usersObj->users->engaged, getLocalizedString("racemap"));
?>

    </div>
<?php
        $user_ranking=$usersObj->getCurrentRanking() ;
?>
    <div id="racenamebox">
        <a href="races.php?type=racing&amp;idraces=<?php echo $usersObj->users->engaged ; ?>&amp;startnum=<?php echo (floor(($user_ranking-1)/MAX_BOATS_ON_RANKINGS)*MAX_BOATS_ON_RANKINGS+1); ?>">
        <?php echo $myRace->racename. '&nbsp;('. round($myRace->getRaceDistance()) . "nm)"; ?>
        </a>
    </div> <!-- fin de racenamebox -->
    <div id="raceicbox">
      <div id="wplistbox">
<?php
      /* Cartes du départ et des WP */
      $oppList="&amp;maptype=compas&amp;wp=1&amp;list=myboat" .
                "&amp;boat=" . $usersObj->users->idusers .
                "&amp;age=0&amp;ext=right";

      if ( $myRace->started ) {
          $WPCLASS = "passedwp";
      } else {
          $WPCLASS = "notpassedwp";
      }
      $status_content  = "&lt;div class=&quot;infobulle&quot;&gt;&lt;b&gt;" . htmlentities(getLocalizedString("startmap")) . "&lt;/b&gt;&lt;br /&gt;";
      $status_content .= "Waypoint Coords=&lt;b&gt;" .
                         round($myRace->startlat/1000,3) . "," . round($myRace->startlong/1000,3) . "&lt;/b&gt;&lt;br /&gt;";

//      $status_content.="&lt;br /&gt;";
      $status_content .= "&lt;/div&gt;";

?>
        <a class="passedwp" href="<?php echo MAP_SERVER_URL ; ?>/mercator.img.php?idraces=<?php
                   echo $usersObj->users->engaged ?>&amp;lat=<?php
                   echo ($myRace->startlat/1000) ?>&amp;long=<?php
                   echo ($myRace->startlong/1000) ?>&amp;maparea=5&amp;drawwind=no&amp;tracks=on<?php
                   echo $oppList ?>&amp;x=800&amp;y=600&amp;proj=mercator" target="_new" class="<?php
                   echo $WPCLASS; ?>" onmouseover="return overlib('<?php echo $status_content; ?>', FULLHTML, HAUTO);"
                   onmouseout="return nd();" ><?php echo getLocalizedString("startmap") ; ?></a> - WP:

<?php
      // On va afficher des liens vers des waypoints
      // Ces derniers possèdent un acronym qui affiche le meilleur temps de passage

      // Cartes des Waypoints
      $wp_num=1;
      //echo "NWP = " . $usersObj->users->nwp;
      foreach ($myRace->getWPs() as $wp) {
         // label = colonne wptype de races_waypoints
         $wp_label=$wp['wptypelabel'];
         $wp_libelle=str_replace("'", "&amp;apos;", htmlentities($wp['libelle']));
         $wp_laisser_au=$wp['laisser_au'];
         $wp_maparea=$wp['maparea'];

	 $wpsymbols = getWaypointHTMLSymbols($wp['wpformat']);

         $status_content="&lt;div class=&quot;infobulle&quot;&gt;&lt;b&gt;WP" . $wp_num . " ".$wpsymbols."&lt;/b&gt;&lt;br /&gt;";
         $status_content.=$wp_libelle." (".$wp_label.")" ;
         $status_content.="&lt;br /&gt;";

         if ( ($wp['wpformat'] & 0xF) == WP_TWO_BUOYS ) {
            $wp_north = max ($wp['latitude1'], $wp['latitude2']);
            $wp_east  = max ($wp['longitude1'], $wp['longitude2']);
            $wp_south = min ($wp['latitude1'], $wp['latitude2']);
            $wp_west  = min ($wp['longitude1'], $wp['longitude2']);

	    $status_content.="Gate Coords=&lt;b&gt;" .
	      round($wp['latitude1']/1000,3) . "," . round($wp['longitude1']/1000,3) .
	      " &lt;----&gt; " . round($wp['latitude2']/1000,3) . "," . round($wp['longitude2']/1000,3) . "&lt;/b&gt;";
         } else {
            $wp_south = $wp_north = $wp['latitude1'];
            $wp_west  = $wp_east  = $wp['longitude1'];

            $status_content .= "Waypoint Coords=&lt;b&gt;" .
                               round($wp_south/1000,3) . "," .
                               round($wp_east/1000,3) .
                               " ($wp_laisser_au)" . "&lt;/b&gt;";

         }
         if ( $wp_num > $usersObj->users->nwp ) {
             $WPCLASS="notpassedwp";
         } else if ( $wp_num < $usersObj->users->nwp ) {
             $WPCLASS="passedwp";
         } else {
              // This one if the next one : we put it YELLOW (class=nextwp)
             $WPCLASS="nextwp";
         }

	 if (($wp['wpformat'] & (WP_ICE_GATE_N|WP_ICE_GATE_S)) == 0) {
	   $wp_racetime = getWaypointBestTime($usersObj->users->engaged, $wp_num);
	   if ( $wp_racetime[0] != "N/A" ) {
	     $racetime = duration2string ($wp_racetime[1]);
	     $status_content.="&lt;br /&gt;&lt;b&gt;";
	     $status_content.=sprintf(getLocalizedString("bestwptime")."(%d)" , $racetime['days'],$racetime['hours'],
				      $racetime['minutes'], $wp_racetime[0]);
	     $status_content.="&lt;/b&gt;";
	   }
	 }

	 $status_content .= "&lt;/div&gt;";

         $centerwp = centerDualCoordMilli($wp_north, $wp_east, $wp_south, $wp_west);
         echo "<a href=\"" .  MAP_SERVER_URL . "/mercator.img.php?idraces=" . $usersObj->users->engaged .
           "&amp;lat=". $centerwp['mlat']/1000.  .
           "&amp;long=" . $centerwp['mlon']/1000. .
           "&amp;maparea=" . $wp_maparea . "&amp;drawwind=no"  .
           "&amp;tracks=on" . $oppList .
           "&amp;wp=" . $wp_num .
           "&amp;x=800&amp;y=600&amp;proj=mercator\" target=\"_new\" class=\"" . $WPCLASS .
           "\" onmouseover=\"return overlib('" . $status_content . "', FULLHTML, HAUTO);" .
           "\" onmouseout=\"return nd();\"" .
           ">" . $wp_num ;
         echo "</a> \n";

         $wp_num++;
      }
?>&nbsp;
      </div> <!-- fin de wplistbox -->

<?php
      if ( $myRace->coastpenalty  >= 3600 ) {
          echo '<div id="costpenaltybox">'.getLocalizedString("locktime")."<span id=\"costpenaltynumber\">".($myRace->coastpenalty/3600). " h</span></div>";
      } else if ( $myRace->coastpenalty  >= 60 ) {
          echo '<div id="costpenaltybox">'.getLocalizedString("locktime")."<span id=\"costpenaltynumber\">".($myRace->coastpenalty/60). " min</span></div>";
      }
?>
    </div> <!--fin de raceicbox -->
  </div> <!--fin de racebox -->
<?php /*  DEUXIEME LIGNE : le bateau */ ?>
  <div id="yourboatbox">
      <div id="yourboatsummarybox">
        <b><?php echo getLocalizedString("yourboat"); ?></b>&nbsp;
        n&deg; <b><?php echo $usersObj->users->htmlIdusers() ; ?></b>&nbsp;
        / &quot;<? echo $usersObj->users->boatname ?>&quot;
<?php
        echo " / " . $usersObj->users->htmlBoattypeLink() . "&nbsp;";
        echo "<img src=\"/".DIRECTORY_COUNTRY_FLAGS."/".$usersObj->users->country.".png\" align=\"middle\" alt=\"" . $usersObj->users->country . "\" />";
        echo  "<br />".getLocalizedString("ranking") . " : " . $user_ranking;

        // Estimation de la prochaine VAC pour ce bateau là

        if ( $usersObj->users->lastupdate + 60*$myRace->vacfreq  >= time() ) {
            printf ("<br />".getLocalizedString("nextupdate") . "%s sec.", 10 * round($usersObj->users->lastupdate + 60*$myRace->vacfreq  - time())/10 );
        }
?>
      </div> <!--fin de yourboat1box -->
      <div id="yourboatstatusbox">
<?php
        // Colone droite

        /* Si l'heure du départ est dépassée */
        if ( time() > $myRace->deptime ) {
            /* Si le bateau n'est pas encore parti, affichage "depart a la prochaine VAC" */
            if ( $usersObj->users->userdeptime < $myRace->deptime ) {
                if ( $usersObj->users->pilotmode == PILOTMODE_WINDANGLE && $usersObj->users->pilotparameter <= 1 ) {
                    printf(getLocalizedString("nostartpending"));
                } else {
                    printf(getLocalizedString("startpending"));
                }
            /* Sinon affichage "En course depuis ... ou bateau locké depuis..." */
            } else {
                // Si le bateau est libre
                if ( time() > $usersObj->users->releasetime ) {
                    $racingtime = duration2string(time() - $usersObj->users->userdeptime);
                    printf(getLocalizedString("racingtime") . getLocalizedString("days")."\n",$racingtime['days'],
			   $racingtime['hours'],$racingtime['minutes'],$racingtime['seconds']);
                } else {
		  $locktime = duration2string($usersObj->users->releasetime - time());
                    //printf(getLocalizedString("locktime") . getLocalizedString("days")."\n",$locktime[0],$locktime[1],$locktime[2],$locktime[3]);
		  printf("<span class=\"warnmessage\"><img src=\"images/site/attention.png\" />".getLocalizedString("locked"). getLocalizedString("days")."</span>\n",$locktime['days'],$locktime['hours'],$locktime['minutes'],$locktime['seconds']);
                }
            }
        /* Sinon (heure départ pas atteinte), affichage de la date de départ */
        } else {
            $departure = gmdate("Y/m/d H:i:s",$myRace->deptime)." GMT";
            echo getLocalizedString("departuredate")." : $departure\n";
        }
        echo "<br />\n";
        // Le mode de pilotage
        //echo getLocalizedString("pilotmode")."<br/>";

	switch ($usersObj->users->pilotmode) {
	case PILOTMODE_HEADING:
	  echo getLocalizedString("autopilotengaged")." ".$usersObj->users->boatheading." ".
	       getLocalizedString("degrees");
	  break;
	case PILOTMODE_WINDANGLE:
	  echo getLocalizedString("constantengaged")." " ;
	  if ( $usersObj->users->pilotparameter > 0 ) {
	    echo " +";
	  }
	  echo $usersObj->users->pilotparameter ." ". getLocalizedString("degrees");
	  break;
	case PILOTMODE_ORTHODROMIC:
	  echo getLocalizedString("orthoengaged");
	  break;
	case PILOTMODE_BESTVMG:
	  echo getLocalizedString("bestvmgengaged");
	  break;
	case PILOTMODE_VBVMG:
	  echo getLocalizedString("vbvmgengaged");
	  break;
	case PILOTMODE_BESTSPEED:
	  echo getLocalizedString("bestspeedengaged")." ".$usersObj->users->boatheading." ".getLocalizedString("degrees");
	  break;
	}
        // Ligne complémentaire si pilote ortho
        if ( $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC or
	     $usersObj->users->pilotmode == PILOTMODE_BESTVMG     or
	     $usersObj->users->pilotmode == PILOTMODE_VBVMG )  {
	  echo "--&gt;" . giveDegMinSec ('html', $usersObj->LatNM/1000, $usersObj->LongNM/1000);
        }
        echo "<br />\n";

        if ( $usersObj->VMGortho != 0 ) {
            $_timetogo=60 * 60 * $usersObj->distancefromend / $usersObj->VMGortho;
            if ( $_timetogo > 0 ) {
                echo "\n";
                printf(getLocalizedString("ETA="). gmdate('Y-m-d H:i:s', time() + $_timetogo)) ;
                $eta=$usersObj->distancefromend / $usersObj->VMGortho;
                //$etad=floor($eta/24);
                //$etah=ceil($eta - 24*$etad);
                //echo " ( &lt; ". $etad . "d " .$etah ."h )";
                $etatime = duration2string($eta*3600);
                printf(" ( " . getLocalizedString("days")." )\n",$etatime['days'],$etatime['hours'],$etatime['minutes'],$etatime['seconds']);
            }
        }
?>
      </div> <!--fin de yourboat2box -->
<?php
        // Colone SOS
        $status_content="&lt;div class=&quot;infobulle&quot; align=&quot;center&quot;&gt;" . getLocalizedString("racingcomite") . "&lt;/div&gt;"; ?>
      <div id="sosbox">
<?php
          echo "<a href=\"mailto:" . EMAIL_COMITE_VLM .
          "?Subject=PAN-PAN" .
          "%20%2F%20RACE%3D" . $usersObj->users->engaged .
          "%20%2F%20IDU%3D".$usersObj->users->idusers .
          "%20%2F%20USERNAME%3D".$usersObj->users->username .
          "%20%2F%20Lat%3D". $usersObj->lastPositions->lat .
          "%2C%20Long%3D" . $usersObj->lastPositions->long .
          "&amp;Body=Hello%2C%0A" .
          "%0A%20******%20EXPLICATION%20DU%20PROBLEME%20%2F%20EXPLANATION%20******%20%0A".
          "%0AFair%20winds%2C%0A" . $usersObj->users->username .
          "\" onmouseover=\"return overlib('$status_content', FULLHTML, HAUTO);\" " .
          " onmouseout=\"return nd();\" " .
          "><img src=\"images/site/sos.png\" alt=\"SOS COMITE\" /></a>";
?>
      </div> <!--fin de sosbox -->
    </div> <!-- fin de yourboatbox -->
</div> <!--fin de statusbox -->

<!-- ********SIMPLE******* -->

<div id="instrumentbox">

    <!-- le beau GPS multifonctions -->
        <div id="gpsbox"  class="instrument">
        <img alt="GPS" src="<?php
        printf( 'gps.php?latitude=%d&amp;longitude=%d&amp;speed=%2.2f&amp;cap=%04.1f&amp;dnm=%4.2f&amp;'.
                'cnmo=%03.1f&amp;cnml=%03.1f&amp;vmg=%2.2f&amp;loch=%02.1f&amp;avg=%02.1f',
                $usersObj->lastPositions->lat,
                $usersObj->lastPositions->long,
                round($usersObj->boatspeed, 2),
                $usersObj->users->boatheading,
                round($usersObj->distancefromend,2),
                $usersObj->orthoangletoend,
                $usersObj->loxoangletoend,
                round($usersObj->VMGortho, 2),
                round($usersObj->users->loch, 1),
                3600*$usersObj->users->loch/(time() - $usersObj->users->userdeptime)
                );
        ?>" />
        </div>
        <div id="windanglebox"  class="instrument">
    <!-- Affichage de windangle -->
        <img alt="wind angle" src="<?php
        printf( 'windangle.php?wheading=%03d&amp;boatheading=%03d&amp;wspeed=%.2f&amp;roadtoend=%4.1f&amp;boattype=%s',
                $usersObj->wheading,
                $usersObj->users->boatheading,
                $usersObj->wspeed,
                $usersObj->orthoangletoend,
                $usersObj->users->boattype
                );
        ?>" />
        </div>
        <div id="anemobox"  class="instrument">
    <!-- Affichage de l'anémo -->
        <img alt="anemo" src="anemo.php?<?php
        if ( $usersObj->wheading + 180 > 360 ) {
            printf ('twd=%4.1f' , ($usersObj->wheading -180 ) );
        } else {
            printf ('twd=%4.1f' , ($usersObj->wheading +180 ) );
        }
        printf ('&amp;tws=%4.1f&amp;cap=%4.1f' , $usersObj->wspeed, $usersObj->users->boatheading );
        ?>" />
        </div>

        <?php
            $messages = Array();

            // Messages specifiques dans le panneau de controle en fonction des courses
            // Blackout ?
            $now = time();
            $ichref="ics.php?idraces=".$myRace->idraces;
            if ( $myRace->bobegin > $now ) {
                $bobegin = gmdate(getLocalizedString("dateClassificationFormat"),$myRace->bobegin);
                $boduration = round(($myRace->boend - $myRace->bobegin ) /3600);
                $messages[] = Array("id" => "incomingbo", "txt" => getLocalizedString("incomingblackout")." : $bobegin ($boduration h)", "class" => "ic", "url" => $ichref);
            }
            if ( $now > $myRace->bobegin && $now < $myRace->boend ) {
                $msg = getLocalizedString("blackout") . " : <b>". gmdate(getLocalizedString("dateClassificationFormat") . "</b>",
                   $myRace->boend);
                $messages[] = Array("id" => "activebo", "txt" => $msg, "class" => "ic", "url" => $ichref);
            }
            // Affichage des IC destinées à la console
            foreach ( $myRace->getICS() as $ic) {
                if (($ic['flag'] & IC_FLAG_VISIBLE) and (IC_FLAG_CONSOLE & $ic['flag']) ) {
                    if ($ic['flag'] & IC_FLAG_LINKFORUM) {
                        $txtstr = "<a href=\"".htmlentities($ic['instructions'])."\" target=\"_ic\">".getLocalizedString("icforum")."</a>";
                        $mes = Array("id" => "ic".$myRace->idraces , "txt" => $txtstr, "class" => "ic");
                    } else {
                        $mes = Array("id" => "ic".$myRace->idraces , "txt" => nl2br($ic['instructions']), "class" => "ic");
                        }
                    if (!($ic['flag'] & IC_FLAG_HIDEONICS)) {
                        $mes["url"] = $ichref;
                    }
                    $messages[] = $mes;
                }
            }
            // player not connected as a player but as a boat/user.
            if ( ! isPlayerLoggedIn()  ) {
                $msg = "<b>".getLocalizedString("You are not logged as a player. Please create and use a player account !")."</b>";
                $messages[] = Array("id" => "oldloginmode", "txt" => $msg, "class" => "warn", "url" => "create_player.php");
            }

            // no ownership for this boat
            if ( $usersObj->users->getOwnerId() == 0  ) {
                $msg = "<b>".getLocalizedString("This boat has no owner.")." ".getLocalizedString("Please attach it to a player !")."</b>";
                $messages[] = Array("id" => "noownership", "txt" => $msg, "class" => "warn", "url" => "create_player.php");
            }


            // Email vide ?
            if ( !preg_match ("/^.+@.+\..+$/",$usersObj->users->email)  && $usersObj->users->getOwnerId() == 0) {
                $msg = "<b>NO E-MAIL ADDRESS</b>&nbsp;Please give one (".getLocalizedString("choose") . ")";
                $messages[] = Array("id" => "voidemail", "txt" => $msg, "class" => "warn", "url" => "edit_boatprefs.php");
            }
            // OMOROB ?
            if ( $usersObj->users->country == "000" ) {
                $msg = "<b>** ONE BOAT PER PLAYER PER RACE **</b>&nbsp;<b>Please contact race Comittee, click on the SOS icon</b><";
                $messages[] = Array("id" => "omorob", "txt" => $msg, "class" => "warn");
            }
            //affichage de la deadline pour les départs en ligne
            $mtr = $myRace->maxTimeRemaining();
            if ( $mtr > (48*3600) ) {
                $msg = getLocalizedString("endrace")." ". gmdate("M d Y H:i:s", $mtr+time() );
                $messages[] = Array("id" => "endrace", "txt" => $msg, "class" => "info");
            } else if ($mtr > 1) {
                $msg = sprintf(getLocalizedString("endracein"), round($mtr/3600) );
                $messages[] = Array("id" => "endrace", "txt" => $msg, "class" => "warn");
            }

            //BLOCNOTE
            if ( $usersObj->users->blocnote != "" and $usersObj->users->blocnote != null  ) {
                $msg = nl2br(substr($usersObj->users->blocnote,0,250)); //nombre max de caractères à ajuster...
                $messages[] = Array("id" => "blocnote", "txt" => $msg, "class" => "info", "url" => "edit_boatprefs.php");
            }

            //Synthese
            if (count($messages) > 0) {
                echo "<div id=\"messagebox\"><span id=\"messagelist\">\n";
                foreach ($messages as $msgstruct) {
                    echo "<div class=\"" . $msgstruct['class'] . "message\" id=\"" . $msgstruct['id'] . "box\">"
                         . $msgstruct["txt"];
                    if (array_key_exists("url", $msgstruct)) {
                        echo "&nbsp;[<a href=\"".$msgstruct["url"]."\">";
                        if ($msgstruct['class'] == 'warn') {
                            echo getLocalizedString("Click here");
                        } else {
                            echo "?";
                        }
                        echo "</a>]";
                    }
                    echo "</div>\n";
                }
                echo "</span></div>";
            }
        ?>

</div> <!-- fin de instrumentbox -->



<div id="controlbox">
    <!-- Pilote automatique -->
    <div id="autopilotcontrolbox" class="controlitem">
        <?php
        if ($usersObj->users->pilotmode == PILOTMODE_HEADING ) {
            $autopilotclass = "inputwarn";
        } else {
            $autopilotclass = "inputnormal";
        }
        ?>
        <?php echo "<span class=\"texthelpers\">". PILOTMODE_HEADING . ": " .getLocalizedString("autopilotengaged")."</span>\n"; ?>
        <form class="controlform" name="autopilot" action="update_angle.php" method="post">
            <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>" />
            <input type="hidden" name="pilotmode" value="autopilot" />
            <div id="autopilotrange">
                <input type="button" value="&lt;" onclick="decrement(); updateSpeed();"/>
                <input class="<?php echo $autopilotclass; ?>" type="text" size="5" maxlength="5" value="<?php echo round($usersObj->users->boatheading,1); ?>" name="boatheading" onchange="updateBoatheading(); updateSpeed();"/>
                <input type="button" value="&gt;" onclick="increment(); updateSpeed();"/>
            </div>
            <span id="estimatespeed" class="inputhelpers">
                <?php echo getLocalizedString("estimated") ?>
                <input type="text" size="4" maxlength="4" name="speed" readonly="readonly" value="<?php echo round($usersObj->boatspeed, 2); ?>"/>
            </span>
            <div id="autopilotaction">
                <input class="actionbutton" type="submit" value="<?php echo getLocalizedString("autopilot")?>"/>
            </div>
        </form>
    </div>

    <!-- Régulateur d'allure -->
    <div id="windanglecontrolbox" class="controlitem">
        <?php
        if ($usersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
            $inputclass = "inputwarn";
        } else {
            $inputclass = "inputnormal";
        }
        ?>
        <?php echo "<span class=\"texthelpers\">". PILOTMODE_WINDANGLE . ": ".getLocalizedString("constantengaged")."</span>"?>
        <form class="controlform" name="angle" action="update_angle.php" method="post">
            <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
            <input type="hidden" name="pilotmode" value="windangle"/>
            <div id="windanglerange">
                <input type="button" value="&lt;" onclick="decrementAngle(); "/>
                <input class="<?php echo $inputclass; ?>" type="text"  size="6" maxlength="6"  name="pilotparameter" value="<?php echo $baww; ?>" />
                <input type="button" value="&gt;" onclick="incrementAngle();"/>
            </div>
            <input class="inputhelpers" type="button" value="<?php echo getLocalizedString("tack"); ?>" onclick="tack();" />
            <div id="windangleaction">
                <input class="actionbutton" type="submit" value="<?php echo getLocalizedString("constant"); ?>" />
            </div>
        </form>
    </div>


    <!--WP pilot based -->
    <div id="wpbasedcontrolbox" class="controlitem">
        <!-- Pilote Orthodromique -->
        <?php
        if ($usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC ) {
            $buttonclass = "actionbuttonwarn";
        } else {
            $buttonclass = "actionbutton";
        }
        ?>
        <div id="orthocontrolbox"  class="controlitem">
            <?php echo "<span class=\"texthelpers\">". PILOTMODE_ORTHODROMIC . ": ".getLocalizedString("orthoengaged")."</span>"?>
            <form class="controlform" name="ortho" action="update_angle.php" method="post">
                <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
                <input type="hidden" name="pilotmode" value="orthodromic"/>
                <input title="<?php echo getLocalizedString("orthodromic_comment"); ?>" class="<?php echo $buttonclass; ?>" type="submit" value="<?php  echo getLocalizedString("orthodromic")?>" />
            </form>
        </div>

        <!-- BEST VMG -->
        <?php
        if ($usersObj->users->pilotmode == PILOTMODE_BESTVMG ) {
            $buttonclass = "actionbuttonwarn";
        } else {
            $buttonclass = "actionbutton";
        }
        ?>
        <div id="bvmgcontrolbox" class="controlitem">
            <?php echo "<span class=\"texthelpers\">". PILOTMODE_BESTVMG . ": ".getLocalizedString("bestvmgengaged")."</span>"?>
            <form class="controlform" name="bestvmg" action="update_angle.php" method="post">
                <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
                <input type="hidden" name="pilotmode" value="bestvmg"/>
                <input title="<?php echo getLocalizedString("orthodromic_comment"); ?>" class="<?php echo $buttonclass; ?>" type="submit" value="<?php  echo getLocalizedString("bestvmgengaged")?>" />
            </form>
        </div>

        <!-- VBVMG -->
        <?php
        if ($usersObj->users->pilotmode == PILOTMODE_VBVMG ) {
            $buttonclass = "actionbuttonwarn";
        } else {
            $buttonclass = "actionbutton";
        }
        ?>
        <div id="vbvmgcontrolbox" class="controlitem">
            <?php echo "<span class=\"texthelpers\">". PILOTMODE_VBVMG . ": ".getLocalizedString("vbvmgengaged")."</span>"?>
            <form class="controlform" name="vbvmg" action="update_angle.php" method="post">
                <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
                <input type="hidden" name="pilotmode" value="vbvmg"/>
                <input title="<?php echo getLocalizedString("orthodromic_comment"); ?>" class="<?php echo $buttonclass; ?>" type="submit" value="<?php  echo getLocalizedString("vbvmgengaged")?>" />
            </form>
        </div>
    </div>

<!-- PROGRAMMATION WP -->
<div id="wpcontrolbox" class="controlitem">
    <form name="coordonnees" action="myboat.php" method="post">
        <input type="hidden" name="type" value="savemywp"/>
        <div id="wpcoordscontrolbox">
        <?php echo "<span class=\"texthelpers\">". getLocalizedString("mytargetpoint") . "</span>"; ?>

            <div id="wplatcontrolbox" class="coordcontrol">
                <span class="subtitlehelpers">Lat&nbsp;&nbsp;&nbsp;</span>
                <input type="text" size="6" maxlength="8" name="targetlat" onkeyup="convertdmslat();" value="<?php echo $usersObj->users->targetlat; ?>" />
                <input type="button" class="dynamichelper" name="latdms" width="1"/>
            </div>
            <div id="wplongcontrolbox" class="coordcontrol">
                <span class="subtitlehelpers">Long</span>
                <input type="text" size="6" maxlength="8" name="targetlong" onkeyup="convertdmslong();" value="<?php echo $usersObj->users->targetlong; ?>" />
                <input type="button" class="dynamichelper" name="longdms" width="1"/>
            </div>
        </div>
        <div id="wpmorecontrolbox">
            <div id="wphcontrolbox">
                <span class="subtitlehelpers">@WPH</span>
                <?php
                echo "<input type=\"text\" size=\"5\" maxlength=\"5\"  name=\"targetandhdg\" " ;
                if ( $usersObj->users->targetandhdg >= 0 and $usersObj->users->targetandhdg <= 360 ) {
                     echo "value=\""  . $usersObj->users->targetandhdg . "\" />" ;
                     echo "<input type=\"checkbox\" name=\"andhdg\" checked=\"checked\" onclick=\"toggle_andhdg()\" />";
                } else {
                     echo "disabled=\"disabled\" value=\"" . -1*abs($usersObj->users->targetandhdg) . "\" />" ;
                     echo "<input type=\"checkbox\" name=\"andhdg\" onclick=\"toggle_andhdg()\" />";
                }
                ?>
            </div>
            <span class="dynamichelpers">&nbsp;</span>
            <div id="wpaction">
                <input class="actionbutton" type="submit" value="<?php  echo getLocalizedString("save")?>" />
            </div>
        </div>
    </form>
</div>

<div id="morecontrolbox" class="controlitem">
    <!-- Pilote programmable -->
    <div id="pilototocontrolbox">
        <?php
            echo "<span class=\"texthelpers\">".getLocalizedString("pilototoengaged");
            $pilototoTasks=$usersObj->users->pilototoCountTasks(PILOTOTO_PENDING);
            if ( $pilototoTasks > 0 ) {
                echo "&nbsp;(" . $pilototoTasks . ")</span>";
                $pilototocssclass = "actionbuttonwarn";
            } else {
                echo "</span>";
                $pilototocssclass = "actionbutton";
            }
        ?>
        <div id="pilototoaction">
            <input class="<? echo $pilototocssclass; ?>" type="button" value="<?php echo getLocalizedString("pilototo_prog"); ?>" onclick="<?php echo "javascript:palmares=popup_small('pilototo.php?idusers=" . $idusers. "', 'Pilototo');"; ?>" />
        </div>
        <!--ticket 542-->
        <input type="hidden" name="pilotmode" value="<?php echo $usersObj->users->pilotmode; ?>"/>
        <input type="hidden" name="boatheading" value="<?php echo $usersObj->users->boatheading; ?>"/>
        <input type="hidden" name="targetlat" value="<?php echo $usersObj->users->targetlat; ?>"/>
        <input type="hidden" name="targetlong" value="<?php echo $usersObj->users->targetlong; ?>"/>
        <input type="hidden" name="targetandhdg" value="<?php echo $usersObj->users->targetandhdg; ?>"/>
        <input type="hidden" name="pilotparameter" value="<?php echo $usersObj->users->pilotparameter; ?>"/>

    </div>

    <!-- VMG POUR VLM -->
    <div id="vlmvmgcontrolbox">
        <form class="controlform" name="vlmvmg" action="<?php echo VMG_SERVER_URL ?>" target="_VMG"> <!-- FIXME POST -->
            <?php echo "<span class=\"texthelpers\">".getLocalizedString("vmgsheet")."</span>"; ?>
            <div id="vlmvmgaction">
                <input type="submit" value="Go !" />
            </div>
            <input type="hidden" name="boattype" value="<?php echo substr($usersObj->users->boattype,5); ?>"/>
            <input type="hidden" name="boatlat" value="<?php echo $usersObj->lastPositions->lat/1000; ?>" />
            <input type="hidden" name="boatlong" value="<?php echo $usersObj->lastPositions->long/1000; ?>" />
            <input type="hidden" name="wdd" value="<?php echo ($usersObj->wheading+180)%360; ?>" />
            <input type="hidden" name="wds" value="<?php echo $usersObj->wspeed; ?>" />
            <input type="hidden" name="wp1lat" value="<?php echo $usersObj->users->targetlat; ?>" />
            <input type="hidden" name="wp1long" value="<?php echo longitudeConstraintDegrees($usersObj->users->targetlong); ?>" />
            <?php

            $nwp_coords=$myRace->giveWPCoordinates($usersObj->getCurrentClassificationWaypointIdx());
            // print_r($nwp_coords);
            //                                Lat              Long
            $lat_xing = new doublep();
            $long_xing = new doublep();
            $xing_ratio = new doublep();

            $xing_dist = VLM_distance_to_line_ratio_xing($usersObj->lastPositions->lat, $usersObj->lastPositions->long,
							 $nwp_coords['latitude1'], $nwp_coords['longitude1'],
							 $nwp_coords['latitude2'], $nwp_coords['longitude2'],
							 $lat_xing, $long_xing, $xing_ratio);
            ?>
            <input type="hidden" name="wp2lat" value="<?php echo (doublep_value($lat_xing) / 1000.0); ?>" />
            <input type="hidden" name="wp2long" value="<?php echo (doublep_value($long_xing) / 1000.0); ?>" />
        </form>

    </div>

    </div>
</div> <!-- Fin des controlbox -->

<!-- Mapbox -->
<form name="mapprefs" id="mercator" action="map.img.php" onSubmit="mapprefSubmitted();" target="_newmap<?php echo getLoginId(); ?>" method="get">
<div id="mapbox">
<!--    <?php echo "<h3>".getLocalizedString("navigation"). "</h3>"?> -->
    <div id="maplayerbox" class="mapboxitem">
    <!-- Layers-->
        <div id="mapgriblayerbox"  class="mapboxsubitem">
            <span class="titlehelpers"><?php echo getLocalizedString("maplayers"); ?></span>
            <p>
            <input onChange="mapprefChanged();" type="radio" name="maplayers" value="multi"
            <?php if ($mapLayers == "multi" ) echo " checked=\"checked\""; ?>  />
            <?php echo getLocalizedString("maplayersmulti"); ?>
            </p>
            <p>
            <input onChange="mapprefChanged();" type="radio" name="maplayers" value="merged"
            <?php if ($mapLayers == "merged" )  echo " checked=\"checked\""; ?>  />
            <?php echo getLocalizedString("maplayersone"); ?>
            </p>
        </div>
        <div  id="maptoolslayerbox"  class="mapboxsubitem">
            <input type="hidden" name="idraces" value="<?php echo $usersObj->users->engaged; ?>" />
            <input type="hidden" name="boat" value="<?php echo $usersObj->users->idusers; ?>" />
            <input type="hidden" name="save" value="on" />
            <span class="titlehelpers"><?php echo getLocalizedString("maptype"); ?> </span>
            <p><input onChange="mapprefChanged();" type="radio" name="maptype" value="compas" <?php if ($mapTools == "compas" ) echo " checked=\"checked\""; ?> />
            <?php echo getLocalizedString("mapcompas"); ?>
            </p>
            <p>
            <input onChange="mapprefChanged();" type="radio" name="maptype" value="floatingcompas" <?php if ($mapTools == "floatingcompas" ) echo " checked=\"checked\""; ?> />
            <?php echo getLocalizedString("mapfloatingcompas"); ?>
            </p>
            <p>
            <input onChange="mapprefChanged();" type="radio" name="maptype" value="bothcompass" <?php if ($mapTools == "bothcompass" ) echo " checked=\"checked\""; ?> />
            <?php echo getLocalizedString("mapbothcompas"); ?>
            </p>
            <p>
            <input onChange="mapprefChanged();" type="radio" name="maptype" value="simple" <?php if ($mapTools == "none" ) echo " checked=\"checked\"" ; ?> />
            <?php echo getLocalizedString("mapsimple"); ?>
            </p>
        </div>
    </div>
    <?php
        if ( $myRace->started != 1) {
            $mapopdis = "disabled"; //.$myRace->started;
        } else {
            $mapopdis = "";
        }

    ?>
    <div  id="mapopponents"  class="mapboxitem">
        <span class="titlehelpers"><?php echo getLocalizedString("mapwho"); ?></span>
        <p><input onChange="mapprefChanged();"  type="radio" name="list" value="myboat" <?php if ($mapOpponents == "myboat" or $myRace->started != 1) echo "checked=\"checked\"";?>  /><?php echo getLocalizedString("maponlyme") ?></p>
        <p><input onChange="mapprefChanged();" <?php echo $mapopdis; ?> type="radio" name="list" value="my5opps" <?php if ($mapOpponents == "my5opps" and $myRace->started == 1) echo "checked=\"checked\"";?>  /><?php echo getLocalizedString("mapmy5opps") ?></p>
        <p><input onChange="mapprefChanged();" <?php echo $mapopdis; ?> type="radio" name="list" value="my10opps" <?php if ($mapOpponents == "my10opps" and $myRace->started == 1) echo "checked=\"checked\"";?>  /><?php echo getLocalizedString("mapmy10opps") ?></p>
        <p><input onChange="mapprefChanged();" <?php echo $mapopdis; ?> type="radio" name="list" value="meandtop10" <?php if ($mapOpponents == "meandtop10" and $myRace->started == 1) echo "checked=\"checked\"";?>  /><?php echo getLocalizedString("mapmeandtop10") ?></p>
        <p><input onChange="mapprefChanged();" <?php echo $mapopdis; ?> type="radio" name="list" value="mylist" <?php if ($mapOpponents == "mylist" and $myRace->started == 1) echo "checked=\"checked\"";?>  /><?php echo "<acronym style=\" border: solid 1px #336699\" title=\"". getLocalizedString("seemappref") . "\">" . getLocalizedString("mapselboats") . "</acronym>" ; ?></p>
        <p><input onChange="mapprefChanged();" <?php echo $mapopdis; ?> type="radio" name="list" value="all" <?php if ($mapOpponents == "all" and $myRace->started == 1) echo "checked=\"checked\"";?> /><?php echo getLocalizedString("mapallboats") ?></p>
    </div>
    <div id="mapcenterbox" class="mapboxitem">
        <span class="titlehelpers"><?php echo getLocalizedString("mymaps"); ?></span>
        <p><input onChange="mapprefChanged();" type="radio" name="mapcenter" value="myboat" <?php if ($mapCenter == "myboat" ) echo " checked=\"checked\""; ?>  />
        <label for="myboat"><?php echo getLocalizedString("mymapboat"); ?></label>
        </p>
        <p>
        <input onChange="mapprefChanged();" type="radio" name="mapcenter" value="mywp" <?php if ($mapCenter == "mywp" )  echo " checked=\"checked\""; ?>  />
        <label for="mywp"><?php echo "waypoint"; ?></label>
        </p>
        <p>
        <input onChange="mapprefChanged();" type="radio" name="mapcenter" value="roadtowp" <?php if ($mapCenter == "roadtowp" )  echo " checked=\"checked\""; ?>  />
        <label for="roadtowp"><?php echo getLocalizedString("mymaproute"); ?></label>
        </p>
        <div id="mapdrawtextwpbox" class="mapboxsubitem">
            <span class="titlehelpers"><?php echo getLocalizedString("mapdrawtextwp"); ?></span>
            <p><input id="drawtextwpon" onChange="mapprefChanged();" type="radio" name="drawtextwp" value="on" <?php if ($mapDrawtextwp != "no" ) echo " checked=\"checked\""; ?>  />
            <label for="drawtextonon"><?php echo getLocalizedString("yes"); ?></label>
            </p>
            <p><input id="drawtextwpno" onChange="mapprefChanged();" type="radio" name="drawtextwp" value="no" <?php if ($mapDrawtextwp == "no" ) echo " checked=\"checked\""; ?>  />
            <label for="drawtextwpno"><?php echo getLocalizedString("no"); ?></label>
            </p>
        </div>

    </div>

    <div id="mapinputbox" class="mapboxitem">

        <div class="mapboxsubitem" id="displaymapaction"><input type="submit" value="<?php echo getLocalizedString("map") ?>" /></div>
        <div  id="mapparameters"  class="mapboxsubitem">
            <p>

                <span class="subtitlehelpers"><?php echo getLocalizedString("maille"); ?></span>
                <input onChange="mapprefChanged();" title="0..9" type="text" size="3" maxlength="1" name="maille" value="<?php echo $mapMaille;?>" />
            </p>
            <p>
                <span class="subtitlehelpers"><?php echo getLocalizedString("estime"); ?>&nbsp;<?php echo " (" .round($usersObj->boatspeed*60*$myRace->vacfreq /3600, 3) . "/" . getLocalizedString("crank") . ")"; ?></span>
                <input onChange="mapprefChanged();" title="0..." type="text" size="3" maxlength="4" name="estime" value="<?php echo $mapEstime;?>" />
            </p>
            <p>
                <span class="subtitlehelpers"><?php echo  getLocalizedString("trackage") ; ?></span>
                <input onChange="mapprefChanged();" title="0..168h" type="text" size="3" maxlength="3" name="age" value="<?php echo $mapAge;?>" />
            </p>
            <p>
                <span class="subtitlehelpers"><?php echo getLocalizedString("mapsize")  ;  ?></span>
                <input onChange="mapprefChanged();" title="0..20" type="text" size="3" maxlength="3" name="maparea" value="<?php echo $mapArea;?>" />
            </p>
        </div>
         <!-- Maillage  et tailles -->
        <div  id="mapsizebox"  class="mapboxsubitem">
            <span class="titlehelpers"><?php echo getLocalizedString("mapimagesize"); ?></span>
             <p>X=<input type="text" size="3" maxlength="4" name="x" value="<?php echo $mapX;?>" />
             Y=<input type="text" size="3" maxlength="4" name="y" value="<?php echo $mapY;?>" /></p>
        </div>

    </div>
     <input type="hidden" name="lat" value="<?php echo $usersObj->lastPositions->lat/1000; ?>" />
     <input type="hidden" name="long" value="<?php echo $usersObj->lastPositions->long/1000; ?>" />
      <?php
          if ( abs($usersObj->users->targetlat) < 0.0001 && abs($usersObj->users->targetlong) < 0.0001 ) {
	    $myWP=&$myRace->giveWPCoordinates($usersObj->getCurrentClassificationWaypointIdx());
	    $centerwp = centerDualCoordMilli($myWP['latitude1'], $myWP['longitude1'], $myWP['latitude2'], $myWP['longitude2']);
	    $latwp = $centerwp['mlat']/1000.;
	    $longwp = $centerwp['mlon']/1000.;
          } else {
	    $latwp = $usersObj->users->targetlat;
	    $longwp = $usersObj->users->targetlong;
          }
      ?>
      <input type="hidden" name="latwp" value="<?php echo $latwp; ?>" />
      <input type="hidden" name="longwp" value="<?php echo $longwp; ?>" />

      <input type="hidden" name="tracks" value="on" />
      <input type="hidden" name="proj" value="mercator" />
      <input type="hidden" name="text" value="right" />
</form>
</div>
<div id="time">
        <?php
        lastUpdate();
        ?>
</div>
        <?php if (isLoggedIn()) { ?>
<div id="user_action">
        <?php
            $lastActionDetails = lastUserAction();
            echo sprintf(getLocalizedString('lastactionip'), $lastActionDetails['action'], dechex(crc32($lastActionDetails['ipaddr'])));
            echo "&nbsp;(&nbsp;<a href=\"userlogs.php\">".getLocalizedString('moreiplogs')."</a>&nbsp;)";
        ?>
</div>
        <?php
            }
        ?>

<?php
  }

  include_once("includes/footer.inc");
?>

