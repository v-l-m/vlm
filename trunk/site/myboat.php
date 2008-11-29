<?php
/*
this page is the control panel for the boat.
it include information, a boat image,
3 forms to choose boat course
javascripts to directly compute speed
links
it'a little bit messy (html+java+php)
*/
include_once("header.inc");
echo "<!-- DELAY_BETWEEN_UPDATES=" . DELAYBETWEENUPDATE . "-->";

  $usersObj = new fullUsers(getLoginId());


  if ( $usersObj->users->engaged == 0 ) {

    // Le palmares du joueur
    echo "<h1>" ; printf ($strings[$lang]["palmares"],$usersObj->users->idusers); echo "</h1>";
    displayPalmares($usersObj->users->idusers);

    // S'engager dans une course
    printf("<h3>" . $strings[$lang]["notengaged"] . "</h3>",$lang);
    //include ("subscribe_race.php");
    include ("raceslist.inc");

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
    //printf("amure=%s<BR>",$amure);

include_once("myboat.js");
?>
<!-- Affichage de la page -->
<div id="boat">
  <!-- Le Div "infobulle" -->
  <span id="infobulle">
  </span>

  <table width="100%">
    <tr class="boat"><!-- premiere ligne -->
      <td colspan="3" class="boat" align="center">
	<table width="100%">
	  <tr class="boat">
	    <td class="boat" align="center" valign="top">
	      <?php
		// Carte de la course
		$href = "racemaps/regate".$usersObj->users->engaged.".jpg";
		if ( file_exists($href) ) {
		  $status_content = "&lt;img src=&quot;$href&quot; " . 
		                    "alt=&quot;engaged&quot; /&gt;";
                  list($xSize, $ySize, $type, $attr) = getimagesize($href);
		  echo "<img src=\"cartemarine.png\" " . 
		  " onmouseover=\"showDivLeft(&quot;infobulle&quot;" .
		  ",&quot;$status_content&quot;, $xSize, $ySize);\" " .
		  " onmouseout=\"hideDiv(&quot;infobulle&quot;);\" " .
		  " alt=\"" .$strings[$lang]["racemap"]. "\" />";
		} ?>
	   </td>
	   <?php  /* le nom de la course */ ?>
	   <td class="boat" align="left" valign="top">
	     <?php
		$user_ranking=getCurrentRanking($usersObj->users->idusers, 
						$usersObj->users->engaged) ; ?>
	     <a href="races.php?lang=<? echo $lang ?>&amp;type=racing&amp;idraces=<?php echo $usersObj->users->engaged ?>&amp;startnum=<? echo (max(1,($user_ranking-MAX_BOATS_ON_RANKINGS/20)))?>"><b><? echo $usersObj->races->racename ?></b></a>
	   </td>
	   <?php /* Cartes du départ et des WP */ ?>
	   <td class="boat" align="right" valign="top">
	     <?php
		$oppList="&amp;maptype=compas&amp;wp=1&amp;list=myboat" .
		  "&amp;boat=" . $usersObj->users->idusers .
		  "&amp;age=0&amp;ext=right"; ?>
	     <b><a href="<? echo MAP_SERVER_URL ?>/mercator.img.php?idraces=<?
	     echo $usersObj->users->engaged ?>&amp;lat=<? 
	     echo ($usersObj->races->startlat/1000) ?>&amp;long=<?
	     echo ($usersObj->races->startlong/1000) ?>&amp;maparea=5&amp;drawwind=no&amp;tracks=on<? echo $oppList ?>&amp;x=800&amp;y=600&amp;proj=mercator" 
	     target="_new"><? echo $strings[$lang]["startmap"] ?></a> - WP: 
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
	      $wp_north = max ($wp[1], $wp[3]);
	      $wp_east  = max ($wp[0], $wp[2]);
	      $wp_south = min ($wp[1], $wp[3]);
	      $wp_west  = min ($wp[0], $wp[2]);

              $status_content.="Gate Coords=&lt;b&gt;" . 
                              round($wp[1]/1000,3) . "," . round($wp[0]/1000,3) . 
                      " &lt;----&gt; " . round($wp[3]/1000,3) . "," . round($wp[2]/1000,3) . "&lt;/b&gt;";

	   } else {
	      $wp_south = $wp_north = $wp[1];
	      $wp_west  = $wp_east  = $wp[0];

              $status_content.="Waypoint Coords=&lt;b&gt;" . 
                              round($wp[1]/1000,3) . "," . round($wp[0]/1000,3) . " ($wp_laisser_au)" . "&lt;/b&gt;&lt;br /&gt;"; 

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

        echo "<br />";
        if ( $usersObj->races->coastpenalty  >= 3600 ) {
	    echo $strings[$lang]["locktime"]."<font color=\"#E0F080\"><b>".($usersObj->races->coastpenalty/3600). " h</b></font> / ";
	} else if ( $usersObj->races->coastpenalty  >= 60 ) {
	    echo $strings[$lang]["locktime"]."<font color=\"#E0F080\"><b>".($usersObj->races->coastpenalty/60). " min</b></font> / ";
        }
        echo $strings[$lang]["racedistance"] . " : ". round($usersObj->races->racedistance) . "nm";
?></b>
           </td>
	 </tr>
       </table>
     </td>
   </tr>
   <?php /*  DEUXIEME LIGNE : le bateau */ ?>
   <tr class="boat">
     <td class="boat" width="50%" align="center" valign="top">
       <b><? echo $strings[$lang]["yourboat"] ?></b>
       n&deg; <b><? echo $usersObj->users->idusers ?></b>
       / &quot;<? echo $usersObj->users->boatname ?>&quot;<?php
	echo " / <a href=\"speedchart.php?boattype=" . $usersObj->users->boattype . "\" target=\"_speedchart\">" . substr($usersObj->users->boattype,5) . "</a>&nbsp;";
        echo "<img src=\"pavillons/" . $usersObj->users->country . ".png\" align=\"center\" alt=\"" . $usersObj->users->country . "\" />"; 
        
        echo "<br />" . $strings[$lang]["ranking"] . " : " . $user_ranking;

	// Estimation de la prochaine VAC pour ce bateau là
        
	if ( $usersObj->users->lastupdate + DELAYBETWEENUPDATE >= time() ) {
	    printf ("<br />".$strings[$lang]["nextupdate"] . "%s sec.", 10 * round($usersObj->users->lastupdate + DELAYBETWEENUPDATE - time())/10 );
	} ?>
     </td>
     <td class="boat" width="50%" align="center" valign="top">
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
	           printf("<img src=\"attention.png\"><font color=\"#F0F0F0\"><b>".$strings[$lang]["locked"]. $strings[$lang]["days"]."</b></font>\n",$locktime[0],$locktime[1],$locktime[2],$locktime[3]);
               }
	    }
	/* Sinon (heure départ pas atteinte), affichage de la date de départ */
	} else {
	    $departure = gmdate("Y/m/d H:i:s",$usersObj->races->deptime)." GMT";
	    echo $strings[$lang]["departuredate"]." : $departure\n";
	}

	// Le mode de pilotage
	//echo $strings[$lang]["pilotmode"]."<br>";

        echo "<br />\n";
	if ( $usersObj->users->pilotmode == PILOTMODE_HEADING ) {
		echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
	}
	else if ( $usersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
		echo $strings[$lang]["constantengaged"]." " ;
		if ( $usersObj->users->pilotparameter > 0 )
			echo " +";
		echo $usersObj->users->pilotparameter ." ". $strings[$lang]["degrees"];
	}
	else if ( $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC ) {
		echo $strings[$lang]["orthoengaged"];
	}
	else if ( $usersObj->users->pilotmode == PILOTMODE_BESTVMG ) {
		echo $strings[$lang]["bestvmgengaged"];
		//echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
	}
	else if ( $usersObj->users->pilotmode == PILOTMODE_BESTSPEED ) {
		//echo $strings[$lang]["bestspeedengaged"];
		echo $strings[$lang]["autopilotengaged"]." ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
	}
	// Ligne complémentaire si pilote ortho
	if (      $usersObj->users->pilotmode == PILOTMODE_ORTHODROMIC
	       or $usersObj->users->pilotmode == PILOTMODE_BESTVMG      )  {
		echo "--&gt;" . giveDegMinSec ('html', $usersObj->LatNM/1000, $usersObj->LongNM/1000);
	}
        if ( $usersObj->VMGortho != 0 ) {
            $_timetogo=60 * 60 * $usersObj->distancefromend / $usersObj->VMGortho;
            if ( $_timetogo > 0 ) {
               echo "<br />\n";
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
     </td>
<?php
        // Colone SOS
        $status_content="&lt;div class=&quot;infobulle&quot; align=&quot;center&quot;&gt;" . $strings[$lang]["racingcomite"] . "&lt;/div&gt;"; ?>
     <td class="boat" align="center" valign="top">
<?php  echo "<a href=\"mailto:" . EMAIL_COMITE_VLM .
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
	  "><img src=\"sos.png\" alt=\"SOS COMITE\" /></a>"; ?>
	</td>
      </tr>
    </table>
  </div>

<!-- ********SIMPLE******* -->

<div id="simple">

	<!-- le beau GPS multifonctions -->
        <table class="boat"><tr class="boat">
        <td class="boat">
    	<img alt="GPS" src="gps.php?
		latitude=<?php   echo ($usersObj->lastPositions->lat)  ?>&amp;
		longitude=<?php  echo ($usersObj->lastPositions->long) ?>&amp;
		speed=<?php  printf ('%2.2f', round($usersObj->boatspeed, 2)) ?>&amp;
		cap=<?php    printf ('%04.1f' , $usersObj->users->boatheading ) ?>&amp;
		dnm=<?php    printf ('%4.2f', round($usersObj->distancefromend,2)) ?>&amp;
		cnmo=<?php   printf ('%03.1f' , $usersObj->orthoangletoend ) ?>&amp;
		cnml=<?php   printf ('%03.1f' , $usersObj->loxoangletoend ) ?>&amp;
		vmg=<?php    printf ("%2.2f", round($usersObj->VMGortho, 2)) ?>&amp;
		loch=<?php   printf ("%02.1f", round($usersObj->users->loch, 1)) ?>&amp;
		avg=<?php    printf ("%02.1f", 3600*$usersObj->users->loch/(time() - $usersObj->users->userdeptime)) ?>"
		/>
        </td><td class="boat">
	<!-- Affichage de windangle -->
    	<img alt="wind angle" src="windangle.php?
		wheading=<?php    printf ('%03d' , ($usersObj->wheading )) ?>&amp;
		boatheading=<?php    printf ('%03d' , $usersObj->users->boatheading ) ?>&amp;
		wspeed=<?php echo intval($usersObj->wspeed) ?>&amp;
		roadtoend=<?php echo $usersObj->orthoangletoend ?>"
	/>
        </td><td class="boat">
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
        </td><td class="boat" valign="top">
        <?php
        // Test si blackout à prévoir
        	echo "<b>Messages : </b> ...";
                // Blackout ?
                $now = time();
                if ( $usersObj->races->bobegin > $now ) {
			$bobegin = gmdate($strings[$lang]["dateClassificationFormat"],$usersObj->races->bobegin);
			$boduration = ($usersObj->races->boend - $usersObj->races->bobegin ) /3600;
			echo "<br />" . "Blackout : " . $bobegin . " (". $boduration . "h)" ;
                }
	        if ( $now > $usersObj->races->bobegin && $now < $usersObj->races->boend ) {	
                     echo "<br />" . $strings[$lang]["blackout"]
                          . "<br /><b>". gmdate($strings[$lang]["dateClassificationFormat"] . "</b>", 
                          $usersObj->races->boend);
                }

		// Email vide ?
		if ( ! preg_match ("/^.+@.+\..+$/",$usersObj->users->email)  ) {
			echo "<br /><b>NO E-MAIL ADDRESS</b>";
			echo "<br />Please give one (".$strings[$lang]["choose"] . ")";
		}
		if ( $usersObj->users->blocnote != "" and $usersObj->users->blocnote != null  ) {
			echo "<br /><b>Notes:</b><br />";
			echo nl2br(substr($usersObj->users->blocnote,0,250)); //nombre max de caractères à ajuster...
			echo "<br />";
		}
		
		// OMOROB ?
		if ( $usersObj->users->country == "000" ) {
			echo "<br /><b>** ONE BOAT PER PLAYER PER RACE **</b>";
			echo "<br /><b>Please contact race Comittee, click on the SOS icon</b>";
		}

		// Vent Des Globes ?
                if ( $usersObj->users->engaged != 20081109 ) {
                     $available_races=availableRaces(getLoginId());
                     /*
                     if ( in_array (20081109, $available_races) ) {
                          printf ( "Race Vent Des Globes : <b>Qualif OK</b>" );
                     } else {
                          printf ( "NOT QUALIFIED for Race Vent Des Globes" );
                     }
                     */
                }

        ?>
        </td></tr></table>
	<hr />
		<!--
		twa=<?php    printf ('%3.1f' , round($usersObj->boatanglewithwind,1) ) ?>&amp;
		amure=<?php  echo $amure;  ?>&amp;
		-->
<!--
<img src="radar.php?
		idusers=<?php   echo ($usersObj->users->idusers)  ?>&amp;
	">
-->

<!-- Pilote automatique -->
<table width="99%">
  <tr>
    <td class="capfixe" align="center" width="20%">
      <?php echo "<b>". PILOTMODE_HEADING . ": " .$strings[$lang]["autopilotengaged"]."</b>"; ?>
      <form name="autopilot" action="update_angle.php"> <!-- FIXME POST -->
	<input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
	<input type="hidden" name="lang" value="<?php echo $lang?>"/>
	<input type="hidden" name="pilotmode" value="autopilot"/>
	<input type="button" value="&lt;" onclick="decrement(); updateSpeed();"/>
	<input type="text" size="5" maxlength="5" value="<?php echo round($usersObj->users->boatheading,1); ?>" name="boatheading" onchange="updateBoatheading(); updateSpeed();"/>
	<input type="button" value="&gt;" onclick="increment(); updateSpeed();"/><br />
	<?php echo $strings[$lang]["estimated"] ?><br />
	<input type="text" size="5" maxlength="5" name="speed" readonly="readonly" value="<?php echo $usersObj->boatspeed?>"/><br />
	<input type="submit" value="<?php echo $strings[$lang]["autopilot"]?>"/>
      </form>
    </td>

<!-- Régulateur d'allure -->
<td class="regulateur" align="center" width="25%">
<?php echo "<b>". PILOTMODE_WINDANGLE . ": ".$strings[$lang]["constantengaged"]."</b>"?>
<form name="angle" action="update_angle.php"> <!-- FIXME POST -->
<input type="button" value="&lt;" onclick="decrementAngle(); "/>
<input type="text"  size="6" maxlength="6"  name="pilotparameter" value="<?php echo $baww; ?>"/>
<!--
<input type=button  class="blue" name="pim" value=<?php echo $baww; ?>>
-->
<input type="button" value="&gt;" onclick="incrementAngle();"/>
<input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
<input type="hidden" name="lang" value="<?php echo $lang?>"/>
<input type="hidden" name="pilotmode" value="windangle"/><br />
<input type="button" value="<?php echo $strings[$lang]["tack"]?>" onclick="tack();"/><br />
<input type="submit" value="<?php echo $strings[$lang]["constant"]?>" />
</form>

<!-- BEST SPEED -->
<!--
<?php //echo "<B>".$strings[$lang]["bestspeedengaged"]."</B>"?>
<form name="bestspeed" action="update_angle.php"> 
<input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
<input type="hidden" name="lang" value="<?php echo $lang?>"/>

<input type="hidden" name="pilotmode" value="bestspeed"/>
<input type="submit" value="<?php  echo $strings[$lang]["bestspeedengaged"]?>" />
</form>
-->
</td>

<!-- Pilote Orthodromique -->
<td class="orthopilot" align="center" width="25%">
<?php echo "<b>". PILOTMODE_ORTHODROMIC . ": ".$strings[$lang]["orthoengaged"]."</b>"?>
<form name="ortho" action="update_angle.php"> <!-- FIXME POST -->
<input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
<input type="hidden" name="lang" value="<?php echo $lang?>"/>

<input type="hidden" name="pilotmode" value="orthodromic"/>
<input type="submit" value="<?php  echo $strings[$lang]["orthodromic"]?>" />
</form>

<br />

<!-- BEST VMG -->
<?php echo "<b>". PILOTMODE_BESTVMG . ": ".$strings[$lang]["bestvmgengaged"]."</b>"?>
<form name="bestvmg" action="update_angle.php"> <!-- FIXME POST -->
<input type="hidden" name="idusers" value="<?php echo $usersObj->users->idusers?>"/>
<input type="hidden" name="lang" value="<?php echo $lang?>"/>

<input type="hidden" name="pilotmode" value="bestvmg"/>
<input type="submit" value="<?php  echo $strings[$lang]["bestvmgengaged"]?>" />
</form>

       <?php echo $strings[$lang]["orthodromic_comment"]; ?>
</td>

<!-- PROGRAMMATION AUTO PILOT  + SAISIE WP visé-->
<td class="pilototo" align="center" width="20%">
       <!-- Pilote programmable -->
       <?php 
            echo "<b>".$strings[$lang]["pilototoengaged"]."</b>";
            $pilototoTasks=$usersObj->users->pilototoCountTasks(PILOTOTO_PENDING);
            if ( $pilototoTasks > 0 ) {
                 echo " <b>(" . $pilototoTasks . ")</b>";
            }
       ?>
       <br />
       <input type="button" value="<?php echo $strings[$lang]["pilototo_prog"]; ?>" 
       onclick="<?php echo "javascript:palmares=popup_small('pilototo.php?lang=".$lang."&amp;idusers=" . $idusers. "', 'Pilototo');"; ?>" />

<br />
       <form name="coordonnees" action="myboat.php" method="post">
       <input type="hidden" name="type" value="savemywp"/>
       <?php echo "<b>". $strings[$lang]["mytargetpoint"] . "</b>"; ?>

       <table>
	 <tr>
	   <td align="right" class="boat">
	     <b>Lat</b>
	   </td>
	   <td align="left" class="boat">
	     <input type="text" size="8" maxlength="8" name="targetlat" 
		    onkeyup="convertdmslat();" value="<?php echo $usersObj->users->targetlat; ?>" />
	     <input type="button"  class="blue" name="latdms" />
	   </td>
	   </tr>
	   <tr>
	     <td align="right" class="boat">
	       <b>Long</b>
	     </td>
	     <td align="left" class="boat">
	       <input type="text" size="8" maxlength="8" 
		      name="targetlong" onkeyup="convertdmslong();" value="<?php echo $usersObj->users->targetlong; ?>" />
	       <input type="button"  class="blue" name="longdms" />
	     </td>
	   </tr>
	   <tr>
	   </tr>
	   <tr>
	     <td align="right" class="boat">
	       <b>@WPH</b>
	     </td>
	     <td align="left" class="boat">
	       <?php
                    echo "<input type=\"text\" size=\"4\" maxlength=\"4\"  name=\"targetandhdg\" " ;
                    if ( $usersObj->users->targetandhdg >= 0 and $usersObj->users->targetandhdg <= 360 ) {
                         echo "value=\""  . $usersObj->users->targetandhdg . "\" />" ;
                         echo "<input type=\"checkbox\" name=\"andhdg\" checked=\"checked\" onclick=\"toggle_andhdg()\" />";
                    } else {
                         echo "disabled value=\"" . -1*abs($usersObj->users->targetandhdg) . "\" />" ;
                         echo "<input type=\"checkbox\" name=\"andhdg\" onclick=\"toggle_andhdg()\" />";
                    }
               ?>

	     </td>
	     </tr>
	     <tr>
	       <td colspan="2" align="right" class="boat">
	       </td>
	     </tr>
	   </table>
	   <input type="submit" value="<?php  echo $strings[$lang]["save"]?>" />
	 </form>

<br />
<!-- VMG POUR VLM -->
<form name="vlmvmg" action="<?php echo VMG_SERVER_URL ?>" target="_VMG"> <!-- FIXME POST -->
<?php echo "<b>".$strings[$lang]["vmgsheet"]."</b>"?>
    <br />
    <input type="submit" value="Go !" />
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
	$bestway=coordonneescroisement($nwp_coords[1], $nwp_coords[0], 
	                               $nwp_coords[3], $nwp_coords[2], 
				       $usersObj->lastPositions->lat, $usersObj->lastPositions->long);

	//echo $bestway[0] . "/" . $bestway[1];
    ?>
    <input type="hidden" name="wp2lat" value="<?php echo $bestway[0]/1000; ?>" />
    <input type="hidden" name="wp2long" value="<?php echo $bestway[1]/1000; ?>" />
</form>

</td>
</tr>
</table>
<hr />
	<?php echo "<h3>".$strings[$lang]["navigation"]. "</h3>"?></td>
	<form id="mercator" action="map.img.php" target="_new" method="get">
	<table width="100%">
	  <tr valign="middle"><td class="boat"></td>
	  <td class="boat" align="center">
	    <?php echo "<b>" . $strings[$lang]["mymaps"] . "</b>" ?>
	    <br />
	         <input type="radio" name="mapcenter" value="myboat" 
	         <?php if ($mapCenter == "myboat" ) echo " checked=\"checked\""; ?>  /> 
		 <?php echo $strings[$lang]["mymapboat"]; ?>
	       <br />
	         <input type="radio" name="mapcenter" value="mywp"
                 <?php if ($mapCenter == "mywp" )  echo " checked=\"checked\""; ?>  />
                 <?php 
		      echo "waypoint";
		      /*
		      echo $strings[$lang]["mymapmywp"] ; 
                      echo $strings[$lang]["mymapnextwp"]; 
		      */
		 ?>
	       <br />
	         <input type="radio" name="mapcenter" value="roadtowp"
                 <?php if ($mapCenter == "roadtowp" )  echo " checked=\"checked\""; ?>  />
                 <?php echo $strings[$lang]["mymaproute"]; ?>
	   </td>

	 <!-- Wind Layer separate/merge -->
	   <td class="boat" align="center">
	       <?php echo "<b>". $strings[$lang]["maplayers"] . "</b>"; ?>
	       <br />
	       <input type="radio" name="maplayers" value="multi" 
	              <?php if ($mapLayers == "multi" ) echo " checked=\"checked\""; ?>  /> 
		      <?php echo $strings[$lang]["maplayersmulti"]; ?>
	       <br />
	       <input type="radio" name="maplayers" value="merged"
                      <?php if ($mapLayers == "merged" )  echo " checked=\"checked\""; ?>  />
                      <?php echo $strings[$lang]["maplayersone"]; ?>
          </td>
	 <!-- Maillage  et tailles -->
	   <td class="boat" align="center">
	       <?php echo "<b>". $strings[$lang]["mapimagesize"] . "</b>"; ?>
	       <br />
	       X=<input type="text" size="4" maxlength="4" name="x" value="<?php echo $mapX;?>" />
	       Y=<input type="text" size="4" maxlength="4" name="y" value="<?php echo $mapY;?>" />

          </td>
	  <td class="boat">
	    <input type="submit" value="<?php echo $strings[$lang]["map"] ?>" />
	  </td>
	</tr>
      </table>

      <table width="100%">
	<tr>
	  <td class="boat" width="30%" align="center">
	    <input type="hidden" name="idraces" value="<?php echo $usersObj->users->engaged; ?>" />
	    <input type="hidden" name="boat" value="<?php echo $usersObj->users->idusers; ?>" />
	    <input type="hidden" name="save" value="on" />
	  <?php echo "<b>".$strings[$lang]["maptype"]."</b>"; ?>
	  <br />
<!--	  <input type="radio" name="maptype" value="traceur" /><?php echo $strings[$lang]["mapline"] ?><br /> -->
	  <input type="radio" name="maptype" value="compas" <?php if ($mapTools == "compas" ) echo " checked=\"checked\""; ?> />
	  <?php echo $strings[$lang]["mapcompas"]; ?><br />
	  <input type="radio" name="maptype" value="floatingcompas" <?php if ($mapTools == "floatingcompas" ) echo " checked=\"checked\""; ?> /> 
	  <?php echo $strings[$lang]["mapfloatingcompas"]; ?><br />
	  <input type="radio" name="maptype" value="bothcompass" <?php if ($mapTools == "bothcompass" ) echo " checked=\"checked\""; ?> /> 
	  <?php echo $strings[$lang]["mapbothcompas"]; ?><br />
	  <input type="radio" name="maptype" value="simple" <?php if ($mapTools == "none" ) echo " checked=\"checked\"" ; ?> />
	  <?php echo $strings[$lang]["mapsimple"]; ?> <br />
	</td>
	<td class="boat" width="30%" align="center">
	  <?php echo "<b>". $strings[$lang]["mapwho"] . "</b>"; ?>
	  <br />
	  <input type="radio" name="list" value="myboat" <?php if ($mapOpponents == "myboat") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["maponlyme"] ?><br />
	  <input type="radio" name="list" value="my5opps" <?php if ($mapOpponents == "my5opps") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmy5opps"] ?><br />
	  <input type="radio" name="list" value="my10opps" <?php if ($mapOpponents == "my10opps") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmy10opps"] ?><br />
	  <input type="radio" name="list" value="meandtop10" <?php if ($mapOpponents == "meandtop10") echo "checked=\"checked\"";?>  /><?php echo $strings[$lang]["mapmeandtop10"] ?><br />
          <input type="radio" name="list" value="mylist" <?php if ($mapOpponents == "mylist") echo "checked=\"checked\"";?>  /><?php echo "<acronym style=\" border: solid 1px #336699\" title=\"". $strings[$lang]["seemappref"] . "\">" . $strings[$lang]["mapselboats"] . "</acronym>" ; ?><br />
	  <input type="radio" name="list" value="all" <?php if ($mapOpponents == "all") echo "checked=\"checked\"";?> /><?php echo $strings[$lang]["mapallboats"] ?>
	</td>
	<td class="boat" width="30%" align="center">
	  <table>
	    <tr>
	      <td align="right" class="boat">
	       <?php echo "<b>". $strings[$lang]["maille"] . "(0..9)</b>"; ?>
	      </td>
	      <td align="left" class="boat">
		<input type="text" size="1" maxlength="1" name="maille" value="<?php echo $mapMaille;?>" />
	      </td>
	    </tr>
	    <tr>
	      <td align="right" class="boat">
		<?php echo "<b>". $strings[$lang]["estime"] . "(0...)</b>"; ?>
	      </td>
	      <td align="left" class="boat">
		<input type="text" size="3" maxlength="4" name="estime" value="<?php echo $mapEstime;?>" /><?php echo " (" .round($usersObj->boatspeed*DELAYBETWEENUPDATE/3600, 2) . ")"; ?>
	      </td>
	    </tr>
	    <tr>
	      <td align="right" class="boat">
		<?php echo  "<b>" . $strings[$lang]["trackage"] . "(0..168)</b>" ; ?>
	      </td>
	      <td align="left" class="boat">
		<input type="text" size="3" maxlength="3" name="age" value="<?php echo $mapAge;?>" />h
	      </td>
	    </tr>
	    <tr>
	      <td align="right" class="boat">
		<b><?php echo "". $strings[$lang]["mapsize"]  ;  ?> 
		0...
		</b>
	      </td>
	      <td align="left" class="boat">
		<input type="text" size="3" maxlength="3" name="maparea" value="<?php echo $mapArea;?>" />
		<b>
	       ...20
               </b>
	     </td>
	   </tr>
	 </table>

	 <input type="hidden" name="lat" value="<?php echo $usersObj->lastPositions->lat/1000; ?>" />
	 <input type="hidden" name="long" value="<?php echo $usersObj->lastPositions->long/1000; ?>" />
	  <?
	      if ( $usersObj->users->targetlat == 0 && $usersObj->users->targetlong == 0 ) {
	           $latwp=($usersObj->races->waypoints[$usersObj->users->nwp-1][1] + $usersObj->races->waypoints[$usersObj->users->nwp-1][3])/2/1000;
	           $longwp=($usersObj->races->waypoints[$usersObj->users->nwp-1][0] + $usersObj->races->waypoints[$usersObj->users->nwp-1][2])/2/1000;
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
	</td>
	</tr>
      </table>
    </form>
  </div>


<div id="time">
	<?php
                include ("abandon_race.php");
		lastUpdate($strings, $lang);
	//	nextUpdate($strings, $lang);
	?>
</div>


    <?php
}

include_once("footer.inc");
?>

