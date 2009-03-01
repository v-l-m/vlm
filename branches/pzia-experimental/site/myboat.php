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
echo "<!-- DELAY_BETWEEN_UPDATES=" . DELAYBETWEENUPDATE . "-->\n";

  $usersObj = new fullUsers(getLoginId());


  if ( $usersObj->users->engaged == 0 ) {

    // Le palmares du joueur
    echo "<h1>" ; printf ($strings[$lang]["palmares"],$usersObj->users->idusers); echo "</h1>";
    displayPalmares($usersObj->users->idusers);

    // S'engager dans une course
    printf("<h3>" . $strings[$lang]["notengaged"] . "</h3>",$lang);
    //include ("subscribe_race.php");
    include ("includes/raceslist.inc");

  } else {

    // 2008/01/14 : DESACTIVE ICI, pour accelerer le refresh de la page.
    // 2008/01/19 : REACTIVE AVEC PREFERENCE, tant pis 

    $autoUpdateAngles = getUserPref($usersObj->users->idusers,"autoUpdateAngles");
    if ( $autoUpdateAngles != "false" ) {
          $usersObj->updateAngles();
    }

    $winddir = (360 - $usersObj->wheading ) + 90;
    while ( $winddir > 360 ) $winddir-=360;
    while ( $winddir < 0 ) $winddir+=360;

    if ( $usersObj->users->pilotmode == PILOTMODE_HEADING 
      OR $usersObj->users->pilotmode == PILOTMODE_BESTVMG  
      OR $usersObj->users->pilotmode == PILOTMODE_BESTSPD  ) {

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
    $mapOpponents = getUserPref($usersObj->users->idusers,"mapOpponents");
    if ( $mapOpponents == "NULL" ) $mapOpponents="myboat";
    $mapTools = getUserPref($usersObj->users->idusers,"mapTools");
    if ( $mapTools == "NULL" ) $mapTools="compas";
    $mapCenter = getUserPref($usersObj->users->idusers,"mapCenter");
    if ( $mapCenter == "NULL" ) $mapCenter="myboat";
    $mapArea = getUserPref($usersObj->users->idusers,"maparea");
    if ( $maparea == "NULL" ) $mapArea=10;
    $mapAge = getUserPref($usersObj->users->idusers,"mapAge");
    if ( $mapAge == "NULL" ) $mapAge=3;
    $mapLayers = getUserPref($usersObj->users->idusers,"mapLayers");
    if ( $mapMaille == "NULL" ) $mapLayers="merged";
    $mapMaille = getUserPref($usersObj->users->idusers,"mapMaille");
    if ( $mapMaille == "NULL" ) $mapMaille=3;
    $mapEstime = getUserPref($usersObj->users->idusers,"mapEstime");
    if ( $mapEstime == "NULL" ) $mapEstime=50;
    $mapX = getUserPref($usersObj->users->idusers,"mapX");
    if ( $mapX == "NULL" ) $mapX = 800;
    $mapY = getUserPref($usersObj->users->idusers,"mapY");
    if ( $mapY == "NULL" ) $mapY = 600;
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
        $href = "images/racemaps/regate".$usersObj->users->engaged.".jpg";
        if ( file_exists($href) ) {
          $status_content = "&lt;img src=&quot;$href&quot; " . 
                            "alt=&quot;".$strings[$lang]["racemap"]."&quot; /&gt;";
                  list($xSize, $ySize, $type, $attr) = getimagesize($href);
          echo "<img class=\"minimap\" src=\"images/site/cartemarine.png\" " . 
          " onmouseover=\"showDivLeft('infobulle'" .
          ",'$status_content', $xSize, $ySize);\" " .
          " onmouseout=\"hideDiv('infobulle');\" " .
          " alt=\"" .$strings[$lang]["racemap"]. "\" />";
        }
?>

    </div>
<?php
        $user_ranking=getCurrentRanking($usersObj->users->idusers,$usersObj->users->engaged) ;
?>
    <div id="racenamebox">
        <a href="races.php?lang=<?php echo $lang ; ?>&amp;type=racing&amp;idraces=<?php echo $usersObj->users->engaged ; ?>&amp;startnum=<?php echo (floor(($user_ranking-1)/MAX_BOATS_ON_RANKINGS)*MAX_BOATS_ON_RANKINGS+1); ?>">
        <?php echo $usersObj->races->racename. '&nbsp;('. round($usersObj->races->racedistance) . "nm)"; ?>
        </a>
    </div> <!-- fin de racenamebox -->
    <div id="raceicbox">
      <div id="wplistbox">
<?php
      /* Cartes du départ et des WP */
      $oppList="&amp;maptype=compas&amp;wp=1&amp;list=myboat" .
                "&amp;boat=" . $usersObj->users->idusers .
                "&amp;age=0&amp;ext=right";
?>
        <a href="<?php echo MAP_SERVER_URL ; ?>/mercator.img.php?idraces=<?php
                   echo $usersObj->users->engaged ?>&amp;lat=<? 
                   echo ($usersObj->races->startlat/1000) ?>&amp;long=<?php
                   echo ($usersObj->races->startlong/1000) ?>&amp;maparea=5&amp;drawwind=no&amp;tracks=on<? echo $oppList ?>&amp;x=800&amp;y=600&amp;proj=mercator" 
                   target="_new"><?php echo $strings[$lang]["startmap"] ; ?></a> - WP: 
<?php
      // On va afficher des liens vers des waypoints
      // Ces derniers possèdent un acronym qui affiche le meilleur temps de passage 
  
      // Cartes des Waypoints
      $wp_num=1;
      //echo "NWP = " . $usersObj->users->nwp;
      foreach ($usersObj->races->waypoints as $wp) {
         // label = colonne wptype de races_waypoints
         $wp_label=$wp[5];
         $wp_libelle=htmlentities($wp[6]);
         $wp_laisser_au=$wp[7];
         $wp_maparea=$wp[8];
  
         $status_content="&lt;div class=&quot;infobulle&quot;&gt;&lt;b&gt;WP" . $wp_num . "&lt;/b&gt;&lt;br /&gt;";
         $status_content.=$wp_libelle." (".$wp_label.")" ;
         $status_content.="&lt;br /&gt;";
  
         if ( $wp[4] == WPTYPE_PORTE ) {
            $wp_north = max ($wp[0], $wp[2]);
            $wp_east  = max ($wp[1], $wp[3]);
            $wp_south = min ($wp[0], $wp[2]);
            $wp_west  = min ($wp[1], $wp[3]);
  
                $status_content.="Gate Coords=&lt;b&gt;" . 
                                round($wp[0]/1000,3) . "," . round($wp[1]/1000,3) . 
                        " &lt;----&gt; " . round($wp[2]/1000,3) . "," . round($wp[3]/1000,3) . "&lt;/b&gt;";
  
         } else {
            $wp_south = $wp_north = $wp[0];
            $wp_west  = $wp_east  = $wp[1];
  
                $status_content.="Waypoint Coords=&lt;b&gt;" . 
                                round($wp[0]/1000,3) . "," . round($wp[1]/1000,3) . " ($wp_laisser_au)" . "&lt;/b&gt;&lt;br /&gt;"; 
  
         }
         if ( $wp_num > $usersObj->users->nwp ) {
             $WPCLASS="notpassedwp";
         } else if ( $wp_num < $usersObj->users->nwp ) {
             $WPCLASS="passedwp";
         } else {
              // This one if the next one : we put it YELLOW (class=nextwp)
             $WPCLASS="nextwp";
         }
  
         $wp_racetime = getWaypointBestTime($usersObj->users->engaged, $wp_num);
         if ( $wp_racetime[0] != "N/A" ) {
              $racetime = duration2string ($wp_racetime[1]);
                      $status_content.="&lt;br /&gt;&lt;b&gt;";
                  $status_content.=sprintf( $strings[$lang]["bestwptime"]."(%d)" , $racetime[0],$racetime[1],$racetime[2],$wp_racetime[0]);
                      $status_content.="&lt;/b&gt;";
             }
  
             $status_content .= "&lt;/div&gt;";
  
         echo "<a href=\"" .  MAP_SERVER_URL . "/mercator.img.php?idraces=" . $usersObj->users->engaged .
           "&amp;lat=". ($wp_north+$wp_south)/2/1000  .
           "&amp;long=" . ($wp_west+$wp_east)/2/1000  .
           "&amp;maparea=" . $wp_maparea . "&amp;drawwind=no"  .
           "&amp;tracks=on" . $oppList . 
           "&amp;wp=" . $wp_num . 
           "&amp;x=800&amp;y=600&amp;proj=mercator\" target=\"_new\" class=\"" . $WPCLASS . 
           "\" onmouseover=\"showDivRight('infobulle','$status_content', 400, 0);\" " .
           " onmouseout=\"hideDiv('infobulle');\" " .
           ">" . $wp_num ;
         
         echo "</a> \n";
         
         $wp_num++;
      }
?>
      </div> <!-- fin de wplistbox -->
  
<?php
      if ( $usersObj->races->coastpenalty  >= 3600 ) {
          echo '<div id="costpenaltybox">'.$strings[$lang]["locktime"].($usersObj->races->coastpenalty/3600). " h</div>";
      } else if ( $usersObj->races->coastpenalty  >= 60 ) {
          echo '<div id="costpenaltybox">'.$strings[$lang]["locktime"].($usersObj->races->coastpenalty/60). " min</div>";
      }
?>
    </div> <!--fin de raceicbox -->
  </div> <!--fin de racebox -->
<?php /*  DEUXIEME LIGNE : le bateau */ ?>
  <div id="yourboatbox">
      <div id="yourboatsummarybox">
        <b><?php echo $strings[$lang]["yourboat"]; ?></b>&nbsp;
        n&deg; <b><?php echo $usersObj->users->idusers ; ?></b>&nbsp;
        / &quot;<? echo $usersObj->users->boatname ?>&quot;
<?php
        echo " / <a href=\"speedchart.php?boattype=" . $usersObj->users->boattype . "\" target=\"_speedchart\">" . substr($usersObj->users->boattype,5) . "</a>&nbsp;";
        echo "<img src=\"".DIRECTORY_COUNTRY_FLAGS."/".$usersObj->users->country.".png\" align=\"middle\" alt=\"" . $usersObj->users->country . "\" />";
        echo  "<br />".$strings[$lang]["ranking"] . " : " . $user_ranking;

        // Estimation de la prochaine VAC pour ce bateau là

        if ( $usersObj->users->lastupdate + DELAYBETWEENUPDATE >= time() ) {
            printf ("<br />".$strings[$lang]["nextupdate"] . "%s sec.", 10 * round($usersObj->users->lastupdate + DELAYBETWEENUPDATE - time())/10 );
        }
?>
      </div> <!--fin de yourboat1box -->
      <div id="yourboatstatusbox">
<?php
        // Colone droite

        /* Si l'heure du départ est dépassée */
        if ( time() > $usersObj->races->deptime ) {
            /* Si le bateau n'est pas encore parti, affichage "depart a la prochaine VAC" */
            if ( $usersObj->users->userdeptime < $usersObj->races->deptime ) {
                if ( $usersObj->users->pilotmode == PILOTMODE_WINDANGLE && $usersObj->users->pilotparameter <= 1 ) {
                    printf($strings[$lang]["nostartpending"]);
                } else {
                    printf($strings[$lang]["startpending"]);
                }
            /* Sinon affichage "En course depuis ... ou bateau locké depuis..." */
            } else {
                // Si le bateau est libre
                if ( time() > $usersObj->users->releasetime ) {
                    $racingtime = duration2string(time() - $usersObj->users->userdeptime);
                    printf($strings[$lang]["racingtime"] . $strings[$lang]["days"]."\n",$racingtime[0],$racingtime[1],$racingtime[2],$racingtime[3]);
                } else {
                    $locktime = duration2string($usersObj->users->releasetime - time());
                    //printf($strings[$lang]["locktime"] . $strings[$lang]["days"]."\n",$locktime[0],$locktime[1],$locktime[2],$locktime[3]);
                    printf("<img src=\"images/site/attention.png\"><font color=\"#F0F0F0\"><b>".$strings[$lang]["locked"]. $strings[$lang]["days"]."</b></font>\n",$locktime[0],$locktime[1],$locktime[2],$locktime[3]);
                }
            }
        /* Sinon (heure départ pas atteinte), affichage de la date de départ */
        } else {
            $departure = gmdate("Y/m/d H:i:s",$usersObj->races->deptime)." GMT";
            echo $strings[$lang]["departuredate"]." : $departure\n";
        }
        echo "<br />\n";
        // Le mode de pilotage
        //echo $strings[$lang]["pilotmode"]."<br/>";

        if ( $usersObj->users->pilotmode == PILOTMODE_HEADING ) {
            echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
        } else if ( $usersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
            echo $strings[$lang]["constantengaged"]." " ;
            if ( $usersObj->users->pilotparameter > 0 ) echo " +";
            echo $usersObj->users->pilotparameter ." ". $strings[$lang]["degrees"];
        } else if ( $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC ) {
            echo $strings[$lang]["orthoengaged"];
        } else if ( $usersObj->users->pilotmode == PILOTMODE_BESTVMG ) {
            echo $strings[$lang]["bestvmgengaged"];
            //echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
        } else if ( $usersObj->users->pilotmode == PILOTMODE_BESTSPEED ) {
            //echo $strings[$lang]["bestspeedengaged"];
            echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
        }
        // Ligne complémentaire si pilote ortho
        if ( $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC or $usersObj->users->pilotmode == PILOTMODE_BESTVMG      )  {
            echo "--&gt;" . giveDegMinSec ('html', $usersObj->LatNM/1000, $usersObj->LongNM/1000);
        }
        echo "<br />\n";

        if ( $usersObj->VMGortho != 0 ) {
            $_timetogo=60 * 60 * $usersObj->distancefromend / $usersObj->VMGortho;
            if ( $_timetogo > 0 ) {
                echo "\n";
                printf($strings[$lang]["ETA="]. gmdate('Y-m-d H:i:s', time() + $_timetogo)) ;
                $eta=$usersObj->distancefromend / $usersObj->VMGortho;
                //$etad=floor($eta/24);
                //$etah=ceil($eta - 24*$etad);
                //echo " ( &lt; ". $etad . "d " .$etah ."h )";
                $etatime = duration2string($eta*3600);
                printf(" ( " . $strings[$lang]["days"]." )\n",$etatime[0],$etatime[1],$etatime[2],$etatime[3]);
            }
        }
?>
      </div> <!--fin de yourboat2box -->
<?php
        // Colone SOS
        $status_content="&lt;div class=&quot;infobulle&quot; align=&quot;center&quot;&gt;" . $strings[$lang]["racingcomite"] . "&lt;/div&gt;"; ?>
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
          "\" onmouseover=\"showDivRight('infobulle','$status_content', 400, 0);\" " .
          " onmouseout=\"hideDiv('infobulle');\" " .
          "><img src=\"images/site/sos.png\" alt=\"SOS COMITE\" /></a>";
?>
      </div> <!--fin de sosbox -->
    </div> <!-- fin de yourboatbox -->
</div> <!--fin de statusbox -->

<!-- ********SIMPLE******* -->

<div id="instrumentbox">

    <!-- le beau GPS multifonctions -->
        <div id="gpsbox"  class="instrument">
        <img alt="GPS" src="gps.php?
        latitude=<?php   echo ($usersObj->lastPositions->lat)  ?>&amp;
        longitude=<?php  echo ($usersObj->lastPositions->long) ?>&amp;
        speed=<?php  printf ('%2.2f', round($usersObj->boatspeed, 2)) ?>&amp;
        cap=<?php    printf ('%04.1f' , $usersObj->users->boatheading ) ?>&amp;
        dnm=<?php    printf ('%4.2f', round($usersObj->distancefromend,2)) ?>&amp;
        cnmo=<?php   printf ('%03.1f' , $usersObj->orthoangletoend ) ?>&amp;
        cnml=<?php   printf ('%03.1f' , $usersObj->loxoangletoend ) ?>&amp;
        vmg=<?php    printf ('%2.2f', round($usersObj->VMGortho, 2)) ?>&amp;
        loch=<?php   printf ('%02.1f', round($usersObj->users->loch, 1)) ?>&amp;
        avg=<?php    printf ('%02.1f', 3600*$usersObj->users->loch/(time() - $usersObj->users->userdeptime)) ?>"
        />
        </div>
        <div id="windanglebox"  class="instrument">
    <!-- Affichage de windangle -->
        <img alt="wind angle" src="windangle.php?
        wheading=<?php printf ('%03d' , ($usersObj->wheading )) ?>&amp;
        boatheading=<?php printf ('%03d' , $usersObj->users->boatheading ) ?>&amp;
        wspeed=<?php echo intval($usersObj->wspeed) ?>&amp;
        roadtoend=<?php echo $usersObj->orthoangletoend ?>"
    />
        </div>
        <div id="anemobox"  class="instrument">
    <!-- Affichage de l'anémo -->
        <img alt="anemo" src="anemo.php?
        twd=<?php    if ( $usersObj->wheading + 180 > 360 ) {
                           printf ('%4.1f' , ($usersObj->wheading -180 ) );
                     } else {
                           printf ('%4.1f' , ($usersObj->wheading +180 ) );
                 } ?>&amp;
        tws=<?php    printf ('%4.1f' , $usersObj->wspeed ) ?>&amp;
        cap=<?php    printf ('%4.1f' , $usersObj->users->boatheading ) ?>"
    />
        </div>

        <?php
            $messages = Array();

            // Messages specifiques dans le panneau de controle en fonction des courses
            // Blackout ?
            $now = time();
            $ichref="ics.php?lang=".$lang."&idraces=".$usersObj->races->idraces;
            if ( $usersObj->races->bobegin > $now ) {
                $bobegin = gmdate($strings[$lang]["dateClassificationFormat"],$usersObj->races->bobegin);
                $boduration = ($usersObj->races->boend - $usersObj->races->bobegin ) /3600;
                $messages[] = Array("id" => "incomingbo", "txt" => $strings[$lang]["blackout"]." : $bobegin ($boduration h)", "class" => "ic", "url" => $ichref);
            }
            if ( $now > $usersObj->races->bobegin && $now < $usersObj->races->boend ) {    
                $msg = $strings[$lang]["blackout"] . " : <b>". gmdate($strings[$lang]["dateClassificationFormat"] . "</b>", 
                   $usersObj->races->boend);
                $messages[] = Array("id" => "activebo", "txt" => $msg, "class" => "ic", "url" => $ichref);
            }
            // Affichage des IC destinées à la console
            foreach ( $usersObj->races->ics as $ic) {
                if (($ic['flag'] & IC_FLAG_VISIBLE) and (IC_FLAG_CONSOLE & $ic['flag']) ) {
                    $messages[] = Array("id" => "ic".$usersObj->races->idraces , "txt" => nl2br($ic['instructions']), "class" => "ic", "url" => $ichref);
                }
            }
            // Email vide ?
            if ( ! preg_match ("/^.+@.+\..+$/",$usersObj->users->email)  ) {
                $msg = "<b>NO E-MAIL ADDRESS</b>&nbsp;Please give one (".$strings[$lang]["choose"] . ")";
                $messages[] = Array("id" => "voidemail", "txt" => $msg, "class" => "warn", "url" => "modify.php?lang=$lang");
            }
            // OMOROB ?
            if ( $usersObj->users->country == "000" ) {
                $msg = "<b>** ONE BOAT PER PLAYER PER RACE **</b>&nbsp;<b>Please contact race Comittee, click on the SOS icon</b><";
                $messages[] = Array("id" => "omorob", "txt" => $msg, "class" => "warn");   
            }
            //BLOCNOTE
            if ( $usersObj->users->blocnote != "" and $usersObj->users->blocnote != null  ) {
                $msg = nl2br(substr($usersObj->users->blocnote,0,250)); //nombre max de caractères à ajuster...
                $messages[] = Array("id" => "blocnote", "txt" => $msg, "class" => "info", "url" => "modify.php?lang=$lang");
            }

            //Synthese
            if (count($messages) > 0) {
                echo "<div id=\"messagebox\"><span id=\"messagelist\">\n";
                foreach ($messages as $msgstruct) {
                    echo "<div class=\"" . $msgstruct['class'] . "message\" id=\"" . $msgstruct['id'] . "box\">"
                         . $msgstruct["txt"];
                    if (array_key_exists("url", $msgstruct)) {
                        echo "&nbsp;[<a href=\"".$msgstruct["url"]."\">?</a>]";
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
        <?php echo "<span class=\"texthelpers\">". PILOTMODE_HEADING . ": " .$strings[$lang]["autopilotengaged"]."</span>\n"; ?>
        <form class="controlform" name="autopilot" action="update_angle.php" method="post"> 
            <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
            <input type="hidden" name="lang" value="<?php echo $lang?>"/>
            <input type="hidden" name="pilotmode" value="autopilot"/>
            <div id="autopilotrange">
                <input type="button" value="&lt;" onclick="decrement(); updateSpeed();"/>
                <input type="text" size="5" maxlength="5" value="<?php echo round($usersObj->users->boatheading,1); ?>" name="boatheading" onchange="updateBoatheading(); updateSpeed();"/>
                <input type="button" value="&gt;" onclick="increment(); updateSpeed();"/>
            </div>
            <span id="estimatespeed" class="inputhelpers">
                <?php echo $strings[$lang]["estimated"] ?>
                <input type="text" size="5" maxlength="5" name="speed" readonly="readonly" value="<?php echo $usersObj->boatspeed?>"/>
            </span>
            <div id="autopilotaction">          
                <input class="actionbutton" type="submit" value="<?php echo $strings[$lang]["autopilot"]?>"/>
            </div>
        </form>
    </div>

    <!-- Régulateur d'allure -->
    <div id="windanglecontrolbox" class="controlitem">
        <?php echo "<span class=\"texthelpers\">". PILOTMODE_WINDANGLE . ": ".$strings[$lang]["constantengaged"]."</span>"?>
        <form class="controlform" name="angle" action="update_angle.php" method="post"> 
            <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
            <input type="hidden" name="lang" value="<?php echo $lang?>"/>
            <input type="hidden" name="pilotmode" value="windangle"/>
            <div id="windanglerange">
                <input type="button" value="&lt;" onclick="decrementAngle(); "/>
                <input type="text"  size="6" maxlength="6"  name="pilotparameter" value="<?php echo $baww; ?>" />
                <input type="button" value="&gt;" onclick="incrementAngle();"/>
            </div>
            <input class="inputhelpers" type="button" value="<?php echo $strings[$lang]["tack"]; ?>" onclick="tack();" />
            <div id="windangleaction">
                <input class="actionbutton" type="submit" value="<?php echo $strings[$lang]["constant"]; ?>" />
            </div>
        </form>
    </div>


    <!--WP pilot based -->
    <div id="wpbasedcontrolbox" class="controlitem">
        <!-- Pilote Orthodromique -->
        <div id="orthocontrolbox"  class="controlitem">
            <?php echo "<span class=\"texthelpers\">". PILOTMODE_ORTHODROMIC . ": ".$strings[$lang]["orthoengaged"]."</span>"?>
            <form class="controlform" name="ortho" action="update_angle.php" method="post"> 
                <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
                <input type="hidden" name="lang" value="<?php echo $lang?>"/>        
                <input type="hidden" name="pilotmode" value="orthodromic"/>
                <input class="actionbutton" type="submit" value="<?php  echo $strings[$lang]["orthodromic"]?>" />
            </form>
        </div>
        
        <!-- BEST VMG -->
        <div id="bvmgcontrolbox" class="controlitem">
            <?php echo "<span class=\"texthelpers\">". PILOTMODE_BESTVMG . ": ".$strings[$lang]["bestvmgengaged"]."</span>"?>
            <form class="controlform" name="bestvmg" action="update_angle.php" method="post"> 
                <input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
                <input type="hidden" name="lang" value="<?php echo $lang?>"/>
                <input type="hidden" name="pilotmode" value="bestvmg"/>
                <input class="actionbutton" type="submit" value="<?php  echo $strings[$lang]["bestvmgengaged"]?>" />
            </form>
            <p class="texthelperscomment">
                <?php echo $strings[$lang]["orthodromic_comment"]; ?>
            </p>
        </div>
    </div>

<!-- PROGRAMMATION WP -->
<div id="wpcontrolbox" class="controlitem">
    <form name="coordonnees" action="myboat.php" method="post">
        <input type="hidden" name="type" value="savemywp"/>
        <div id="wpcoordscontrolbox">
        <?php echo "<span class=\"texthelpers\">". $strings[$lang]["mytargetpoint"] . "</span>"; ?>

            <div id="wplatcontrolbox" class="coordcontrol">
                <span class="texthelpers">Lat</span>
                <input type="text" size="8" maxlength="8" name="targetlat" onkeyup="convertdmslat();" value="<?php echo $usersObj->users->targetlat; ?>" />
                <input disabled="disabled" type="text" size="8" class="dynamichelper" name="latdms" value="none"/>
            </div>
            <div id="wplongcontrolbox" class="coordcontrol">
                <span class="texthelpers">Long</span>
                <input type="text" size="8" maxlength="8" name="targetlong" onkeyup="convertdmslong();" value="<?php echo $usersObj->users->targetlong; ?>" />
                <input disabled="disabled" type="text" size="8" class="dynamichelper" name="longdms" />
            </div>
        </div>
        <div id="wpmorecontrolbox">
            <div id="wphcontrolbox">
                <span class="texthelpers">@WPH</span>
                <?php
                echo "<input type=\"text\" size=\"4\" maxlength=\"4\"  name=\"targetandhdg\" " ;
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
                <input class="actionbutton" type="submit" value="<?php  echo $strings[$lang]["save"]?>" />
            </div>
        </div>
    </form>
</div>

<div id="morecontrolbox" class="controlitem">
    <!-- Pilote programmable -->
    <div id="pilototocontrolbox">       
        <?php 
            echo "<span class=\"texthelpers\">".$strings[$lang]["pilototoengaged"];
            $pilototoTasks=$usersObj->users->pilototoCountTasks(PILOTOTO_PENDING);
            if ( $pilototoTasks > 0 ) {
                echo "&nbsp;(" . $pilototoTasks . ")</span>";
            } else {
                echo "</span>";
            }
        ?>
        <div id="pilototoaction">
            <input class="actionbutton" type="button" value="<?php echo $strings[$lang]["pilototo_prog"]; ?>" onclick="<?php echo "javascript:palmares=popup_small('pilototo.php?lang=".$lang."&amp;idusers=" . $idusers. "', 'Pilototo');"; ?>" />
        </div>
    </div>

    <!-- VMG POUR VLM -->
    <div id="vlmvmgcontrolbox">
        <form class="controlform" name="vlmvmg" action="<?php echo VMG_SERVER_URL ?>" target="_VMG"> <!-- FIXME POST -->
            <?php echo "<span class=\"texthelpers\">".$strings[$lang]["vmgsheet"]."</span>"; ?>
            <div id="vlmvmgaction">
                <input type="submit" value="Go !" />
            </div>
            <input type="hidden" name="boattype" value="<?php echo substr($usersObj->users->boattype,5); ?>"/>
            <input type="hidden" name="lang" value="<?php echo $lang?>"/>
            <input type="hidden" name="boatlat" value="<?php echo $usersObj->lastPositions->lat/1000; ?>" />
            <input type="hidden" name="boatlong" value="<?php echo $usersObj->lastPositions->long/1000; ?>" />
            <input type="hidden" name="wdd" value="<?php echo ($usersObj->wheading+180)%360; ?>" />
            <input type="hidden" name="wds" value="<?php echo $usersObj->wspeed; ?>" />
            <input type="hidden" name="wp1lat" value="<?php echo $usersObj->users->targetlat; ?>" />
            <input type="hidden" name="wp1long" value="<?php echo $usersObj->users->targetlong; ?>" />
            <?php
        
            $nwp_coords=giveWaypointCoordinates ($usersObj->users->engaged , $usersObj->nwp, WPLL/WP_NUMSEGMENTS);
            // print_r($nwp_coords);
            //                                Lat              Long
            $lat_xing = new doublep();
            $long_xing = new doublep();
            $xing_ratio = new doublep();
        
            $xing_dist = VLM_distance_to_line_ratio_xing($usersObj->lastPositions->lat, $usersObj->lastPositions->long,
                     $nwp_coords[0], $nwp_coords[1],
                     $nwp_coords[2], $nwp_coords[3],
                     $lat_xing, $long_xing, $xing_ratio);
            ?>
            <input type="hidden" name="wp2lat" value="<?php echo (doublep_value($lat_xing) / 1000.0); ?>" />
            <input type="hidden" name="wp2long" value="<?php echo (doublep_value($long_xing) / 1000.0); ?>" />
        </form>
    
    </div>

    </div>
</div> <!-- Fin des controlbox -->

<!-- Mapbox -->
<form id="mercator" action="map.img.php" target="_new" method="get">
<div id="mapbox">
    <?php echo "<h3>".$strings[$lang]["navigation"]. "</h3>"?>

    <div id="mapcenterbox" class="mapboxitem">
        <span class="titlehelpers"><?php echo $strings[$lang]["mymaps"]; ?></span>
        <p><input type="radio" name="mapcenter" value="myboat" <?php if ($mapCenter == "myboat" ) echo " checked=\"checked\""; ?>  />
        <label for="myboat"><?php echo $strings[$lang]["mymapboat"]; ?></label>
        </p>
        <p>
        <input type="radio" name="mapcenter" value="mywp" <?php if ($mapCenter == "mywp" )  echo " checked=\"checked\""; ?>  />
        <label for="mywp"><?php echo "waypoint"; ?></label>
        </p>
        <p>
        <input type="radio" name="mapcenter" value="roadtowp" <?php if ($mapCenter == "roadtowp" )  echo " checked=\"checked\""; ?>  />
        <label for="roadtowp"><?php echo $strings[$lang]["mymaproute"]; ?></label>
        </p>
    </div>

    <!-- Wind Layer separate/merge -->
    <div id="maplayerbox"  class="mapboxitem">
        
        <span class="titlehelpers"><?php echo $strings[$lang]["maplayers"]; ?></span>
        <p>
        <input type="radio" name="maplayers" value="multi" 
        <?php if ($mapLayers == "multi" ) echo " checked=\"checked\""; ?>  /> 
        <?php echo $strings[$lang]["maplayersmulti"]; ?>
        </p>
        <p>        
        <input type="radio" name="maplayers" value="merged"
        <?php if ($mapLayers == "merged" )  echo " checked=\"checked\""; ?>  />
        <?php echo $strings[$lang]["maplayersone"]; ?>
        </p>
    </div>
     <!-- Maillage  et tailles -->
    <div  id="mapdisplaybox"  class="mapboxitem">
        <span class="titlehelpers"><?php echo $strings[$lang]["mapimagesize"]; ?></span>
         <p>X=<input type="text" size="4" maxlength="4" name="x" value="<?php echo $mapX;?>" />
         Y=<input type="text" size="4" maxlength="4" name="y" value="<?php echo $mapY;?>" /></p>
         <p id="displaymapaction"><input type="submit" value="<?php echo $strings[$lang]["map"] ?>" /></p>
    </div>
    <div  id="maptools"  class="mapboxitem">
        <input type="hidden" name="idraces" value="<?php echo $usersObj->users->engaged; ?>" />
        <input type="hidden" name="boat" value="<?php echo $usersObj->users->idusers; ?>" />
        <input type="hidden" name="save" value="on" />
        <?php echo "<b>".$strings[$lang]["maptype"]."</b>"; ?>
        <!--      <input type="radio" name="maptype" value="traceur" /><?php echo $strings[$lang]["mapline"] ?><br /> -->
        <p><input type="radio" name="maptype" value="compas" <?php if ($mapTools == "compas" ) echo " checked=\"checked\""; ?> />
        <?php echo $strings[$lang]["mapcompas"]; ?>
        </p>
        <p>
        <input type="radio" name="maptype" value="floatingcompas" <?php if ($mapTools == "floatingcompas" ) echo " checked=\"checked\""; ?> /> 
        <?php echo $strings[$lang]["mapfloatingcompas"]; ?><br />
        </p>
        <p>
        <input type="radio" name="maptype" value="bothcompass" <?php if ($mapTools == "bothcompass" ) echo " checked=\"checked\""; ?> /> 
        <?php echo $strings[$lang]["mapbothcompas"]; ?><br />
        </p>
        <p>
        <input type="radio" name="maptype" value="simple" <?php if ($mapTools == "none" ) echo " checked=\"checked\"" ; ?> />
        <?php echo $strings[$lang]["mapsimple"]; ?> <br />
        </p>
    </div>
    <div  id="mapopponents"  class="mapboxitem">
        <?php echo "<b>". $strings[$lang]["mapwho"] . "</b>"; ?>
        <p><input type="radio" name="list" value="myboat" <?php if ($mapOpponents == "myboat") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["maponlyme"] ?></p>
        <p><input type="radio" name="list" value="my5opps" <?php if ($mapOpponents == "my5opps") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmy5opps"] ?></p>
        <p><input type="radio" name="list" value="my10opps" <?php if ($mapOpponents == "my10opps") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmy10opps"] ?></p>
        <p><input type="radio" name="list" value="meandtop10" <?php if ($mapOpponents == "meandtop10") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmeandtop10"] ?></p>
        <p><input type="radio" name="list" value="mylist" <?php if ($mapOpponents == "mylist") echo "checked=\"checked\"";?>  /><?php echo "<acronym style=\" border: solid 1px #336699\" title=\"". $strings[$lang]["seemappref"] . "\">" . $strings[$lang]["mapselboats"] . "</acronym>" ; ?></p>
        <p><input type="radio" name="list" value="all" <?php if ($mapOpponents == "all") echo "checked=\"checked\"";?> /><?php echo $strings[$lang]["mapallboats"] ?></p>
    </div>
    <div  id="mapparameters"  class="mapboxitem">
        <p>
            <?php echo "<b>". $strings[$lang]["maille"] . "(0..9)</b>"; ?>
            <input type="text" size="1" maxlength="1" name="maille" value="<?php echo $mapMaille;?>" />
        </p>
        <p>
            <?php echo "<b>". $strings[$lang]["estime"] . "(0...)</b>"; ?>
            <input type="text" size="3" maxlength="4" name="estime" value="<?php echo $mapEstime;?>" /><?php echo " (" .round($usersObj->boatspeed*DELAYBETWEENUPDATE/3600, 2) . ")"; ?>
        </p>
        <p>
            <?php echo  "<b>" . $strings[$lang]["trackage"] . "(0..168)</b>" ; ?>
            <input type="text" size="3" maxlength="3" name="age" value="<?php echo $mapAge;?>" />h
        </p>
        <p>
            <b><?php echo "". $strings[$lang]["mapsize"]  ;  ?> 
            0-20
            </b>
            <input type="text" size="3" maxlength="3" name="maparea" value="<?php echo $mapArea;?>" />
        </p>
    </div>

     <input type="hidden" name="lat" value="<?php echo $usersObj->lastPositions->lat/1000; ?>" />
     <input type="hidden" name="long" value="<?php echo $usersObj->lastPositions->long/1000; ?>" />
      <?php
          if ( $usersObj->users->targetlat == 0 && $usersObj->users->targetlong == 0 ) {
               $latwp=($usersObj->races->waypoints[$usersObj->users->nwp-1][0] + $usersObj->races->waypoints[$usersObj->users->nwp-1][2])/2/1000;
               $longwp=($usersObj->races->waypoints[$usersObj->users->nwp-1][1] + $usersObj->races->waypoints[$usersObj->users->nwp-1][3])/2/1000;
               if ( abs($usersObj->races->waypoints[$usersObj->users->nwp-1][1] - $usersObj->races->waypoints[$usersObj->users->nwp-1][3] ) > 180 ) {
                   //on inverse le centre si un wp à l'air de faire plus de 180°
                   $longwp += 180;
               }
          } else {
               $latwp=$usersObj->users->targetlat;
               $longwp=$usersObj->users->targetlong;
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
    include ("abandon_race.php");
    lastUpdate($strings, $lang);
    ?>
</div>


<?php
}

include_once("includes/footer.inc");
?>

