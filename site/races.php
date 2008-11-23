<?php
include_once("header.inc");
include_once("config.php");
include_once("strings.inc");
include_once("showdiv.js");

/*
 * Affichage de l'onglet 
 * de choix du classement 
 */
function displayRankingMenu($fullRacesObj, $tableType, $extra_arg, $lang = "fr") {
    //echo "<H1>" ; printf ($strings[$lang]["palmares"],$idusers); echo "</H1>";
    echo "<H1>" ; printf ("CHOIX DU CLASSEMENT"); echo "</H1>";

    $tables = array();

    // Ajout en début de tableau d'un classement par WP (uniquement si >1)
    if ( count($fullRacesObj->races->waypoints) > 1 ) {
         for ($wp_num=1; $wp_num < count($fullRacesObj->races->waypoints); $wp_num++) {
	     array_push($tables, "WP" . $wp_num);
	 }
    }

    array_push($tables, "arrived", "racing", "dnf", "abd", "htp", "hc");
    if ( ! in_array($tableType, $tables) ) {
         printf ("<H1>You should not do that, this type of ranking is not accepted : %s</H1>\n", $tableType); exit;
    }

    echo "<table><tr>";
    // Affichage des classements classiques 
    foreach ($tables as $table) {
        if ( $table == $tableType ) {
           $class="class=\"hilight\"";
	   $cellcontent=ucfirst($table);
        } else {
           $class="class=\"nohilight\"";
	   if ( strstr($table, "WP") ) {
              $tlabel=substr($table,2);
           } else {
              $tlabel=$table;
           }
	   $cellcontent="<A href=" . $_SERVER["PHP_SELF"] . "?lang=".$lang."&amp;idraces=" . $fullRacesObj->races->idraces . "&amp;type=".$table. ">";
	   $cellcontent.=ucfirst($tlabel);
	   $cellcontent.="</A>";
        }
        echo "<td " . $class . ">\n";
	
	    echo "$cellcontent";

        echo "</td>\n";
    }
    echo "</tr></table>";
	/*
	"raceresults" => "R&eacute;sultats de la course",
	"raceresultarr" => "Bateaux arriv&eacute;s",
	"raceresultdnf" => "Bateaux disqualifi&eacute;s",
	"raceresultabd" => "Bateaux ayant abandonn&eacute;",
	"raceresulthtp" => "Bateaux ayant fini hors temps",
	"raceresulthc" => "Bateaux Hors Classement",
	*/

}

function displayPrevious100($startnum) {
          // Si on ne part pas de 1, on propose le bouton "-100"
          if ( $startnum > 1 ) {
               $FORMULAIRE="<form method=GET name=moins method=GET action=" . $_SERVER["PHP_SELF"] . ">\n";

               $new_startnum=$startnum-MAX_BOATS_ON_RANKINGS;
               if ( $new_startnum < 0 ) $new_startnum=1;
               $FORMULAIRE.="<input type=hidden name=\"startnum\" value=\"$new_startnum\">\n";

               foreach($_REQUEST as $keyname => $value) {
                    if ( $keyname != "startnum" && $keyname != "PHPSESSID" ) {
                         $FORMULAIRE.="<input type=hidden name=\"$keyname\" value=\"$value\">\n";
                    }
               }
               $FORMULAIRE.="<input type=submit value=\"< " . MAX_BOATS_ON_RANKINGS . "\">\n";
               $FORMULAIRE.="</form>\n";
               echo $FORMULAIRE;
          }
}

function displayNext100($startnum, $num_engaged) {
          // Si on ne part pas de 1, on propose le bouton "+100"
          $new_startnum=$startnum+MAX_BOATS_ON_RANKINGS;
          if ( $startnum >= 1 && $new_startnum  < $num_engaged ) {
               $FORMULAIRE="<form method=GET name=plus action=" . $_SERVER["PHP_SELF"] . ">\n";
               $FORMULAIRE.="<input type=hidden name=\"startnum\" value=\"$new_startnum\">\n";

               foreach($_REQUEST as $keyname => $value) {
                    if ( $keyname != "startnum" && $keyname != "PHPSESSID" ) {
                         $FORMULAIRE.= "<input type=hidden name=\"$keyname\" value=\"$value\">\n";
                    }
               }
               $FORMULAIRE.="<input type=submit value=\"> " . MAX_BOATS_ON_RANKINGS . "\">\n";
               $FORMULAIRE.="</form>\n";
               echo $FORMULAIRE;
          }

}
// Query and QueryArgument

/*
VARIABLES d'APPEL : q= lang= idraces= idusers= sortkey= sortorder= disttype= startnum=
$idusers=htmlentities(quote_smart($_REQUEST['idusers']));

*/


if ( quote_smart($_REQUEST['full']) == "yes" ) {
     $startnum=0;
} else {
     $startnum=max(1,quote_smart($_REQUEST['startnum'])) ;
}

$q=htmlentities(quote_smart($_REQUEST['type'])) ;
if ( $q == "" ) $q = "arrived";


if ( $q == "palmares" ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
	echo "<H1>" ; printf ($strings[$lang]["palmares"],$idusers); echo "</H1>";
	displayPalmares($idusers);
}

echo "<span id=\"infobulle\">
      </span>";

if ($idraces != 0) {

	  $fullRacesObj = new fullRaces($idraces);
          list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);
          if ( $startnum > 0 ) $startnum-=$num_arrived;

	  //show alls races
	
	  printf("<h3>".$strings[$lang]["racestarted"]."</h3>", $fullRacesObj->races->racename, gmdate("Y/m/d H:i:s", $fullRacesObj->races->deptime));     

          // Carte de la course
	  $href = "racemaps/regate".$fullRacesObj->races->idraces.".jpg";
          if ( file_exists($href) ) {

              $status_content = "<img src=$href>";
              list($xSize, $ySize, $type, $attr) = getimagesize($href);

              echo "<img src=cartemarine.png " .
                     " onmouseover=\"showDivLeft('infobulle','$status_content', $xSize, $ySize);\" " .
                     " onmouseout=\"hideDiv('infobulle');\" " .
                  " alt=\"" .$strings[$lang]["racemap"]. "\">";
          }

          echo "<table class=boat><tr class=boat>";
          echo "<td class=boat>";
	  // ** Onglet de choix sur différents classements
          displayRankingMenu($fullRacesObj, $q, $sortkey, $lang);
          echo "</td><td class=boat valign=bottom>";
          displayPrevious100($startnum);
          echo "</td><td class=boat valign=bottom>";
          displayNext100($startnum, $num_engaged);
          echo "</td>";
          echo "</tr></table>";
	
	  echo "<A NAME=\"ARR\"";
	  echo "</A>";

	  if ( $q == "arrived" ) {
	      if ( isset($sortkey) && $sortkey != "" ) {
	         $numarrived=$fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ARR, $sortkey , $sortorder, 0, $startnum);

	      } else {
	         if ( $fullRacesObj->races->racetype == RACE_TYPE_RECORD ) {
		    // dispHtmlResults : 2 derniers paramètres = critère de tri + ordre (asc/desc)
		    // Pour une course record : c'est le temps de course par défaut
	            $numarrived=$fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ARR, "duration" , "asc" ,0,  $startnum);
	         } else {
		    // Pour une course classique, c'est tout simplement la date d'arrivée (on l'a avec deptime + duration)
	            $numarrived=$fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ARR, "arrtime", "asc", 0, $startnum);
	         }
	      }
          }

	  if ( strstr($q, "WP") ) {
	      //echo "<H1>Classement au WP ".substr($q,2) ."</H1>";
	      if ( $fullRacesObj->races->racetype == RACE_TYPE_RECORD ) {
		 // Pour une course record : c'est le temps de course par défaut
	         $numarrived=$fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ARR, "duration" , "asc" , substr($q,2), 0, $startnum);
	      } else {
		 // Pour une course classique, c'est tout simplement la date d'arrivée (on l'a avec deptime + duration)
	         $numarrived=$fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ARR, "arrtime", "asc", substr($q,2), 0, $startnum);
	      }
	  }
	
	  if ( $q == "racing" ) {
	     if ( $fullRacesObj->races->started ) {
	         echo "<A NAME=\"ENC\"";
	         echo "</A>";

                 // Analyse de la clé de tri demandée
                 $sortkeys=array();
                 array_push($sortkeys, "", "idusers", "userdeptime", "loch", "last1h", "last3h","last24h","latitude","longitude");
                 $sortkey=htmlentities(quote_smart($_REQUEST['sortkey'])) ;
                 if ( ! in_array($sortkey, $sortkeys, TRUE) ) {
                    $sortkey="";
                    printf ("<H1>BAD sortkey : %s, defaulting to nextwaypoint and distance</H1>\n", $sortkey); 
                 }

                 $sortorder=strtolower(htmlentities(quote_smart($_REQUEST['sortorder']))) ;
                 if ($sortorder != "asc" and $sortorder != "desc") $sortorder="asc";

                 $disttype=strtolower(htmlentities(quote_smart($_REQUEST['disttype']))) ;
                 if ($disttype != "tofirst" and $disttype != "tonm") $disttype="tonm";

	         if ( isset($sortkey) && $sortkey != "" ) {
	             $fullRacesObj->dispHtmlClassification($strings, $lang, $numarrived , $sortkey . " " . $sortorder , $disttype, $startnum);
                 } else {
	             $fullRacesObj->dispHtmlClassification($strings, $lang, $numarrived, "nwp desc, dnm asc", $disttype, $startnum);
                 }
	     } else {
	         printf( "<h3>". $strings[$lang]["hasnotstart"]."<br />\n",$fullRacesObj->races->racename);
	         $departure = gmdate("Y/m/d H:i:s",$fullRacesObj->races->deptime)." GMT";
	         echo $strings[$lang]["departuredate"]." : $departure </h3>\n";
	         echo "<h4>".$strings[$lang]["playersengaged"]."</h4>";
	         $fullRacesObj->dispHtmlEngaged($strings, $lang, $startnum);
	     }
	  }
	
	
	  if ( $q == "htp" ) {
	       echo "<A NAME=\"HTP\"";
	       echo "</A>";
	       $fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_HTP, 0, $startnum);
	  }
	  if ( $q == "dnf" ) {
	       echo "<A NAME=\"DNF\"";
	       echo "</A>";
	       $fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_DNF, 0, $startnum);
	  }
	  if ( $q == "abd" ) {
	       echo "<A NAME=\"ABD\"";
	       echo "</A>";
	       $fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_ABD, 0, $startnum);
	  }
	  if ( $q == "hc" ) {
	       echo "<A NAME=\"HC\"";
	       echo "</A>";
	       $fullRacesObj->dispHtmlRacesResults($strings, $lang, BOAT_STATUS_HC, 0, $startnum);
	  }
	
	  echo "<p>";
	  echo $strings[$lang]["see"];
	  echo '<a href="races.php?lang='.$lang.'"> ';
	  echo $strings[$lang]["list"]."</a>"; 
	  echo "</p>";

          echo "<table class=boat><tr class=boat>";
          echo "<td class=boat>";
	  // ** Onglet de choix sur différents classements
          displayRankingMenu($fullRacesObj, $q, $sortkey, $lang);
          echo "</td><td class=boat valign=bottom>";
          displayPrevious100($startnum);
          echo "</td><td class=boat valign=bottom>";
          displayNext100($startnum, $num_engaged);
          echo "</td>";
          echo "</tr></table>";

}

	else  //idraces ==0 means display all races
{
	  dispHtmlRacesList($strings, $lang);
}

include_once("footer.inc");
?>
