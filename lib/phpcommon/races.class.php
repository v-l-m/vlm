<?php


//****************************************************************************//
//                                                                            //
//                             CLASS  races                                   //
//                             ------------                                   //
//                                                                            //
//****************************************************************************//

class races {
  var $idraces,
    $racename,
    $started,
    $deptime,
    $startlong, $startlat,
    $boattype,
    $closetime,
    $racetype,
    $firstpcttime,
    $depend_on,
    $qualifying_races,
    $idchallenge,
    $coastpenalty,
    $bobegin,
    $boend,
    $maxboats,
    $theme,
    $waypoints,
    $racedistance,
    $ics;

  function races($id=0) {
    $query= "SELECT idraces, racename, started, deptime, startlong, startlat, 
             boattype, closetime, racetype, firstpcttime, depend_on, 
             qualifying_races, idchallenge, coastpenalty, bobegin, boend,
             maxboats, theme, vacfreq
             FROM races WHERE idraces = $id";
    $result = wrapper_mysql_db_query_reader($query) or die($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    
    $this->idraces          = $row['idraces'];
    $this->racename         = $row['racename'];
    $this->started          = $row['started'];
    $this->deptime          = $row['deptime'];
    $this->startlong        = $row['startlong']; 
    $this->startlat         = $row['startlat'];
    $this->boattype         = $row['boattype'];
    $this->closetime        = $row['closetime'];
    $this->racetype         = $row['racetype'];
    $this->firstpcttime     = $row['firstpcttime'];
    $this->depend_on        = $row['depend_on'];
    $this->qualifying_races = $row['qualifying_races'];
    $this->idchallenge      = $row['idchallenge'];
    $this->coastpenalty     = $row['coastpenalty'];
    $this->bobegin          = $row['bobegin'];
    $this->boend            = $row['boend'];
    $this->maxboats         = $row['maxboats'];
    $this->theme            = $row['theme']; // Le theme , si non null, 
                                             // force le theme de l'interface
    $this->vacfreq          = $row['vacfreq']; // 1, 5, ou 10, 
                                               //frequence des runs du moteur
  }
  
  function getWPs() {
    $this->retrieveWPs();
    return $this->waypoints;
  }

  function getWPsCount() {
    $this->retrieveWPs();
    return count($this->waypoints);
  }


  // ====================================================
  // returns an array of 2 points (lat1,long1,lat2,long2) 
  // beeing the coordinates in millidegrees of a waypoint
  // ====================================================
  function giveWPCoordinates($idwp) {
    $this->retrieveWPs();
    return $this->waypoints[$idwp];
  }
  
  function retrieveWPs() {
    if (isset($this->waypoints)) {
      return;
    }
    // retrieve all waypoints
    $this->waypoints=array();

    $query = "SELECT RW.wporder, RW.wptype, WP.libelle, RW.laisser_au,".
      "WP.maparea,WP.latitude1, WP.longitude1, WP.latitude2, WP.longitude2".
      " FROM races_waypoints RW, waypoints WP WHERE idraces=".$this->idraces . 
      " AND RW.idwaypoint=WP.idwaypoint ORDER BY wporder";
    
    $result = wrapper_mysql_db_query_reader($query);
    // printf ("Request Races_Waypoints : %s\n" , $query);
    
    while( $row = mysql_fetch_array( $result, MYSQL_ASSOC) ) {
      $WPCoords = internalGiveWaypointCoordinates($row['latitude1'],
						  $row['longitude1'], 
						  $row['latitude2'], 
						  $row['longitude2'],
						  $row['laisser_au'], WPLL);
      // On push dans le tableau des coordonnées le wptype 
      // (classement ou son nom), et le libellé et le "laisser_au" du WP
      // ainsi que le maparea adapt
      $WPCoords['wptypelabel'] = $row['wptype'];
      $WPCoords['libelle'] = $row['libelle'];
      $WPCoords['laisser_au'] = $row['laisser_au'];
      $WPCoords['maparea'] = $row['maparea'];
      // On push ce WP dans la liste des WP
      $this->waypoints[$row['wporder']] = $WPCoords;
    }
    $this->stop1lat  = $WPcoords['latitude1'];
    $this->stop1long = $WPcoords['longitude1'];
    $this->stop2lat  = $WPcoords['latitude2'];
    $this->stop2long = $WPcoords['longitude2'];
  }

  function getRaceDistance($force = 0) {
    if (!isset($this->racedistance) OR ($force != 0) ) {
      if ( $this->idraces == 35 OR $this->idraces >=40 ) {
	$this->racedistance=0;
	$lastlong=$this->startlong;
	$lastlat=$this->startlat;
	$this->retrieveWPs();
	foreach ( $this->waypoints as $WP ) {
	  $d1=ortho($lastlat,$lastlong,$WP['latitude1'], $WP['longitude1'] );
	  $d2=ortho($lastlat,$lastlong,$WP['latitude2'],$WP['longitude2']);
	  if ( $d1 < $d2 ) {
	    $lastlat=$WP['latitude1'];
	    $lastlong=$WP['longitude1'];
	    $this->racedistance+=$d1;
	  } else {
	    $lastlat=$WP['latitude2'];
	    $lastlong=$WP['longitude2'];
	    $this->racedistance+=$d2;
	  }
	}
	// + la distance entre l'avant dernier WP et le dernier
	$this->racedistance+=min(ortho($lastlat,$lastlong,
				       $WP['latitude1'], $WP['longitude1'] ), 
				 ortho($lastlat,$lastlong,
				       $WP['latitude2'], $WP['longitude2'] ));
      } else {
	$this->racedistance=0;
      }
    }
    return $this->racedistance;
  }



  /* retrieve the Race Instructions */
  function getICS($force = 0) {
    if (!isset($this->ics) OR ($force != 0) ) {
      // retrieve all IC if we are not running the engine
      if (!defined('MOTEUR')) {
        $this->ics = array();
	
        $query = "SELECT instructions, flag FROM races_instructions" .
          " WHERE idraces = 0 OR idraces = " . $this->idraces ; 
	
        $result = wrapper_mysql_db_query_reader($query);
	
        while( $row = mysql_fetch_array( $result, MYSQL_ASSOC) ) {
	  $this->ics[] = $row;
        }
      }
    }
    return $this->ics;
  }

  // maxTimeRemaining : 
  //        =0 pour les courses de type "record" (pas de temps limite) 
  //                  -NON, PLUS APRES ECHANGE D'AVIS AVEC PHILE-
  //    Pour les autres courses :
  //        >0 si le premier n'est pas arrivé ou est arrivé il y a peu de temps
  //                  => on calcule cette valeur
  //        <0 lorsque pourcentage en plus du temps du premier est dépassé
  function maxTimeRemaining($verbose = 0) {
    // Si course "record", il n'y avait pas de temps limite... 
    // if ( $this->races->racetype == RACE_TYPE_RECORD ) {
    //     return (0);
    //  }
    // => depuis le 14/10/2007, il y en a un : 2*PCT du temps du premier

    // On est encore là... c'est une course classique
    // Recherche du temps de course du premier (dans races_results)
    $query = "SELECT duration FROM races_results WHERE idraces=".
      $this->idraces." AND position=".BOAT_STATUS_ARR." ORDER BY duration ASC".
      " LIMIT 1";

    $result = wrapper_mysql_db_query_reader($query);
    if ( mysql_num_rows($result) == 0) {
      return(1);  // on s'arrete là si personne n'est arrivé !
    }
    
    // On est encore là, on a donc un enregistrement "duration"
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $WinnersRaceDuration = $row['duration'];
    
    // Sur course RECORD, c'est 2 * le pourcentage du temps du premier
    if ( $this->racetype == RACE_TYPE_RECORD ) {
      $maxArrivalTime = $this->closetime + ($WinnersRaceDuration *
					    (1 + $this->firstpcttime/100));
    } else {
      // sur les autres courses, c'est le pourcentage du temps de course 
      // du premier
      $maxArrivalTime = $this->deptime + ($WinnersRaceDuration * 
					  (1 + $this->firstpcttime/100));
    }

    $now = time();
    if ( $verbose != 0 ) {
      printf ("\n\tDeptime:%d, WinnersDuration=%d, Pct=%d, ", $this->deptime,
	      $WinnersRaceDuration, $this->firstpcttime);
      printf ("Winners + 1+pct/100=%d\n",$WinnersRaceDuration * 
	      (1 + $this->firstpcttime/100));
      printf ("\tMaxArrivalTime:%d, Now=%d...", $maxArrivalTime, $now);
    }

    // C'est trop tard...
    //   <0 lorsque pourcentage en plus du temps du premier est dépassé
    if ( $now > $maxArrivalTime ) {
      if ( $verbose != 0 ) {
        printf ("La course est finie...\n");
      }
      return (-1);   
    } 
    // Il reste du temps donc $maxArrivalTime - $now
    //   >0 si le premier n'est pas arrivé ou est arrivé il y a peu de temps
    else {
      if ( $verbose != 0 ) {
        printf ("La course n'est pas finie, fin dans %d heures\n",
		($maxArrivalTime-$now)/3600);
      }
      return ( $maxArrivalTime - $now );
    }
  }
}


//****************************************************************************//
//                                                                            //
//                               CLASS fullRaces                              //
//                               ---------------                              //
//                                                                            //
//****************************************************************************//

class fullRaces {
  var $races,
    ///not in db
    $excluded = array(), //array with users excludes (DNF, abandon, on shore)
    $opponents = array(); //array with users engaged


  function fullRaces($id = 0, $origrace = NULL)
  {
    if ($origrace == NULL) {
      $this->races = new races($id);
    } else {
      $this->races = &$origrace;
    }
    //select all the boats
    //create an array of users
    $query6 = "SELECT RR.idusers idusers " .
      " FROM  races_ranking RR, users US " .
      " WHERE RR.idusers = US.idusers " .
      " AND   RR.idraces = "  . $this->races->idraces .
      " AND   US.engaged = "  . $this->races->idraces .
      " ORDER by nwp desc, dnm asc, US.ipaddr, US.country asc";

    $result6 = wrapper_mysql_db_query_reader($query6);
    while($row = mysql_fetch_array($result6, MYSQL_ASSOC)) {
      //WARNING: dont load fullUsers inside fullRaces
      //because fullRaces contains fullUsers that contain fullRaces ..
      $userid = $row['idusers'];
      $this->opponents[$userid] = new users($userid);
      //we should sort them!
    }
    
    // On prend aussi les utilisateurs de la table "races_results", 
    // pour les retrouver une fois la course terminée. 
    $query6b = "SELECT DISTINCT races_results.idusers FROM races_results, ".
      "users WHERE idraces = ".$this->races->idraces.
      " AND users.idusers = races_results.idusers AND users.engaged != ".
      $this->races->idraces;
    $result6b = wrapper_mysql_db_query_reader($query6b);
    while($row = mysql_fetch_array($result6b, MYSQL_ASSOC)) {
      $userid = $row['idusers'];
      $this->excluded[$userid] = new users($userid);
    }
  }
  
  function cleanRaces() {
    // Delete from races_results
    $query5  = "DELETE from races_results WHERE idraces = ".
      $this->races->idraces ;
    wrapper_mysql_db_query_writer($query5);

    // Delete from waypoints_crossing
    $query5  = "DELETE from waypoint_crossing WHERE idraces = ".
      $this->races->idraces ;
    wrapper_mysql_db_query_writer($query5);

    //  Update Positions of all engaged boats to start line
    $query5  = "DELETE from positions WHERE race = ".$this->races->idraces;
    wrapper_mysql_db_query_writer($query5);
  }

  function startRaces() {
    //set started to 1
    $this->races->started = 1;
    $query5 = "UPDATE races SET `started` = 1 WHERE idraces = ".
      $this->races->idraces;
    wrapper_mysql_db_query_writer($query5);
  }

  function stopRaces() {
    //set started to -1 
    $this->races->started =  -1;
    echo "=> CLOSING RACE " . $this->races->idraces ."\n";
    
    $query  = "UPDATE races SET `started` = -1 WHERE idraces = ".
      $this->races->idraces ;
    wrapper_mysql_db_query_writer($query);
    
    $query  = "DELETE FROM races_ranking WHERE idraces = ".
      $this->races->idraces ;
    wrapper_mysql_db_query_writer($query);
    
    $query  = "UPDATE users SET engaged = 0 WHERE engaged = ".
      $this->races->idraces ;
    wrapper_mysql_db_query_writer($query);
    
    $queryhistopositions = "INSERT INTO histpos SELECT * FROM ". 
      "positions WHERE race=" . $this->races->idraces;
    wrapper_mysql_db_query_writer($queryhistopositions);

    $querypurgepositions = "DELETE FROM positions WHERE race =". 
      $this->races->idraces;
    wrapper_mysql_db_query_writer($querypurgepositions);
  }


  //===========================================//
  //                                           //
  // Function dispHtmlEngaged($strings, $lang) //
  //                                           //
  //-------------------------------------------//
  //                                           //
  //       Display engaged boats, when         //
  //         the race not start yet            //
  //                                           //
  //===========================================//

  function dispHtmlEngaged($strings, $lang)
  {
    //table header
    echo "\n<table>\n";
    echo "  <thead>\n";
    echo "    <tr>\n";
    //    echo "      <th>".$strings[$lang]["country"]."</th>\n";
    echo "      <th>".$strings[$lang]["skipper"]."</th>\n";
    echo "      <th>".$strings[$lang]["boat"]."</th>\n";
    echo "    </tr>\n";
    echo "  </thead>\n";
    echo "  <tbody>\n";
    //echo "    <tr><td></td><td></td></tr>\n";
    //for xhtml  compliance, find other solution

    $num_inscrits=0;
    foreach ($this->opponents as $users)
      {
        echo "    <tr class=ranking>\n";
        // ============= Affichage des noms de bateaux en acronyme
        echo "<td>";
        echo $this->htmlFlagImg($users->country);
        echo "\n";
        echo "<acronym style=\" border-bottom: solid #" . $users->color."\" ".
          "title=\"". $users->boatname."\">".$users->username." (".
	  $users->idusers . ")</acronym></td>\n";
        echo "<td>" . $users->boatname .  "</td>";
        // =================================================================
        echo "    </tr>\n";
        $num_inscrits++;
      }
    
    //table footer
    echo "  </tbody>\n";
    echo "</table>\n";
    echo "<br />\n<h3>Total : ". $num_inscrits . " inscrits.</h3>";
  }

  //==================================================//
  //                                                  //
  // Function dispHtmlClassification($strings, $lang) //
  //                                                  //
  //--------------------------------------------------//
  //                                                  //
  //            Display classification                //
  //           when the race is running               //
  //                                                  //
  //==================================================//

  function dispHtmlClassification($strings, $lang, $numarrived = 0 , 
                                  $sortclause="nwp desc, dnm asc", 
				  $disttype ="tofirst", 
                                  $startnum = 1) {
    
    $now=time();
    
    $IDU=intval(getLoginId());
    if ( $IDU != 0 ) {
      $list = explode ("," , getUserPref($IDU,"mapPrefOpponents") );
    } else {
      $list = "empty";
    }
    
    //if (!isset($toBeSort[0])) return; // plus personne en course. On arrete là !
    $classification_time=lastUpdateTime();

    // L'URL de la page affichant ces classements :
    // http://vlm/races.php?lang=fr&type=racing&idraces=20071111&sortkey=idusers&sortorder=asc
    $baseurl=$_SERVER['PHP_SELF'];
    $baseurl.="?lang=".$lang;
    $baseurl.="&amp;type=racing";
    $baseurl.="&amp;idraces=".$this->races->idraces;

    // idraces , idusers , nwp  , dnm  , latitude , longitude , last1h  , last3h  , last24h
    $query_ranking = "SELECT RR.idusers idusers, US.username username, US.boatname boatname, US.color color, US.country country, nwp, dnm, userdeptime, US.loch loch, US.releasetime releasetime, US.pilotmode pim, US.pilotparameter pip, latitude, longitude, last1h, last3h, last24h " . 
      " FROM  races_ranking RR, users US " . 
      " WHERE RR.idusers = US.idusers " . 
      " AND   RR.idraces = "  . $this->races->idraces . 
      " ORDER by " . $sortclause ;

    $result = wrapper_mysql_db_query_reader($query_ranking) or die ($query_ranking);
    if (mysql_num_rows($result)==0) return;  // on s'arrete là si personne n'est concerné !

    // On est encore là, on affiche le classement
    // Si on est en cours de Blackout, on prévient
    echo "<h3>";
    if ( $this->races->bobegin < $now && $now < $this->races->boend ) {
      echo $strings[$lang]["classification"]
        .gmdate($strings[$lang]["dateClassificationFormat"], $this->races->bobegin);
      echo "<br />\n";
      echo $strings[$lang]["blackout"]
        .gmdate($strings[$lang]["dateClassificationFormat"], $this->races->boend);
    } else {
      echo $strings[$lang]["classification"]
        .gmdate($strings[$lang]["dateClassificationFormat"], $classification_time);
    }
    echo "</h3>\n";

    //echo $strings[$lang]["classification_dnf"]."<BR><BR>";



    //table header
    echo "\n<table>\n";
    echo "<thead>\n";
    echo "<tr>\n";

    // position au classement 
    echo "<th>Pos.</th>\n"; 

    // Titre colonne SKIPPER (avec appels des tris)
    $str=$strings[$lang]["skipper"];
    $str.=" <a href=\"".$baseurl."&amp;sortkey=idusers&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=idusers&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";

    // Distance à la prochaine marque (c'est le tri par défaut)
    $str="<a href=\"".$baseurl."\">".$strings[$lang]["distance"]."</a>";
    echo "<th>".$str."</th>\n";

    // Temps de course
    $str=$strings[$lang]["racingtime"];
    $str.=" <a href=\"".$baseurl."&amp;sortkey=userdeptime&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=userdeptime&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";

    // Distance parcourue
    $str=$strings[$lang]["loch"];
    $str.=" <a href=\"".$baseurl."&amp;sortkey=loch&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=loch&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";

    // Position : tris nord,sud/west,east
    $str=$strings[$lang]["position"];
    $str.=" <a href=\"".$baseurl."&amp;sortkey=latitude&amp;sortorder=desc\">^</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=latitude&amp;sortorder=asc\">v</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=longitude&amp;sortorder=asc\">&lt;</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=longitude&amp;sortorder=desc\">&gt;</a>";
    echo "<th>".$str."</th>\n";

    //   echo "<th>".$strings[$lang]["speed"]."</th>\n";
    //   echo "<th>Cap</th>\n";

    // Distances parcourues sur 1h, 3h, 24h...
    $str="1h";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last1h&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last1h&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";

    $str="3h";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last3h&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last3h&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";

    $str="24h";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last24h&amp;sortorder=asc\">+</a>";
    $str.=" <a href=\"".$baseurl."&amp;sortkey=last24h&amp;sortorder=desc\">-</a>";
    echo "<th>".$str."</th>\n";


    // Ecart par rapport au premier : pas de tri
    $str=$strings[$lang]["ecart"];
    if ( strtolower(htmlentities(quote_smart($_REQUEST['disttype']))) == "tonm" ) { 
      $str.=" <a href=\"".$baseurl."&amp;disttype=tofirst\">1st</a>";
    } else {
      $str.=" <a href=\"".$baseurl."&amp;disttype=tonm\">NM</a>";
    }
    echo "<th>".$str."</th>\n";

    echo "</tr>\n";
    echo "</thead>\n";
    echo "<tbody>\n";
    //echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>\n";
    //for xhtml  compliance, find other solution

    $key = 0; $printed =0;
    while( $row = mysql_fetch_assoc( $result ) ) {

      // Si on a déjà affiché suffisament de lignes, on rend la main
      if ( $startnum >0 && $printed >= MAX_BOATS_ON_RANKINGS ) break;

      // N'entrent dans les tableaux que les bateaux effectivement en course
      if ( $row['nwp'] == "" || $row['loch'] == 0 ) {
	continue;
      }

      if ( $key == 0 ) {
        $FirstNwp = $row['nwp'];
        $FirstDnm = $row['dnm'];
        $FirstLat = $row['latitude'];
        $FirstLon = $row['longitude'];
      }

      //table lines
      // key++ uniquement pour les "joueurs VLM"
      if ( $row['idusers'] > 0 ) {
	$key++;
      }

      $rank=$key + $numarrived;

      // On saute les "N"(startnum) premiers
      if ( $startnum > 0 && $key < $startnum ) {
	continue;
      }

      if ( $row['idusers'] < 0 ) {
        $class="class=\"realboat\"";
      } else if ( $row['idusers'] == $IDU ) {
        $class="class=\"hilight\"";
      } else if ( $list != "empty" && in_array($row['idusers'], $list) )  {
        $class="class=\"hilightopps\"";
      } else {
        $class="class=\"ranking\"";
      }
      // Bateaux bloqués (lock) ou seulement à la cote (oncoast)
      if ( $row['releasetime'] > $now ) {
        $class="class=\"locked\"";
      } else if ( $row['pim'] == 2 && abs($row['pip']) <= 1 ) {
        $class="class=\"oncoast\"";
      }
      echo "<tr " . $class . ">\n";
      if ( $row['idusers'] > 0 ) {
        echo "<td>". $rank ."</td>\n";
      } else {
        echo "<td>&nbsp;</td>\n";
      }
      // ============= Affichage des noms de bateaux en acronyme
      if ( $row['idusers'] > 0 ) {
	echo "<td class=\"ranking\">";
	echo $this->htmlFlagImg($row['country']);
	echo "<acronym onmousedown=\"javascript:palmares=popup_small('palmares.php?lang=".$lang."&amp;type=palmares&amp;idusers=" . $row['idusers'] . "', 'palmares');\" style=\" border-bottom: solid #" . $row['color'] . "\" " .
	  "title=\"". $row['boatname'] . "\">" . 
	  " (". $row['idusers'] . ") " . 
	  $row['username'] .
	  "</acronym>\n";
        echo "</td>";
      } else {
        $idu=-$row['idusers'];
        if ( $idu >=100 and $idu <=199 ) {
	  $idu-=100;
	}
        echo "<td>".$row['username']. " <b>(". $idu .")</b>" ."</td>\n";
      }
      //  echo "<td>" . substr($row[boatname],0,20) . "</td>";
      // =================================================================

      // we give distance to the next WP
      printf( "<td>" . "[" . $row['nwp'] . "]" . "->" .
              $strings[$lang]["nautics"].
              "</td>\n", $row['dnm']);

      // Give the racing time (if the boat has started)
      if ( $row['nwp'] != 0 ) {
        $racingtime=$now-$row['userdeptime'];
        $duration = duration2string($racingtime);
        printf("      <td>".$strings[$lang]["days"]."</td>\n",$duration['days'],$duration['hours'],$duration['minutes'],$duration['seconds']);
      } else {
        printf("      <td>-</td>\n");
      }
      // Loch
      printf("      <td>%5.2f</td>\n", $row['loch']);

      // Affichage de l'ETA
      //               en milles  en noeuds ==> temps en heures avec décimale
      // Maintenant + (distance / vitesse) * 3600 (on parle en secondes)
      // Si VMG != 0 alors on fait le calcul, sinon, pas la peine
      /*
        if ( $usersObj->VMGortho != 0 )
        {
        $etr=( 60 * $row[dnm]) / $usersObj->VMGortho;
        $etr_h=floor($etr / 60);
        $etr_m=$etr -($etr_h*60);
        printf( "<td>%dh %dm</td>\n",  $etr_h, $etr_m );
        } else {
        // Client dont le VMG est nul (abandon en chantier...)
        printf( "<td>N/A</td>\n" );
        }
      */

      // Position
      $longitude=$row['longitude'];
      $latitude=$row['latitude'];
      // Longitude : W ou E
      if ( $longitude > 0 ) {
	$long_side='E';
      } else {
        $long_side='W';
      }
      
      // Latitude : N ou S
      if ( $latitude > 0 ) {
	$lat_side='N';
      } else {
        $lat_side='S';
      }

      // Calcul de l'URL de la carte Carte sur les 1° autour du bateau
      $mapurl="<a class=\"ranking\" href=\"" . MAP_SERVER_URL . "/mercator.img.php?idraces=" . $this->races->idraces .
        "&amp;lat=". round($latitude/1000,2) .
        "&amp;long=" . round($longitude/1000,2) .
        "&amp;maparea=16" .
        "&amp;tracks=on&amp;age=6&amp;list[]=" . $row['idusers'] .
        "&amp;x=800&amp;y=600&amp;proj=mercator&amp;text=right\" target=\"_new\">"  ;
      //               "&tracks=on&list=all" .

      // Affichage de la position
      //printf("<td>" . $mapurl . "%3.3f&deg;" . $lat_side . ", %3.3f&deg;" . $long_side . "</A>, <A target=_gm HREF=http://maps.google.fr/maps?ie=UTF8&z=8&ll=%f,%f&t=k>GM</A></td>\n", abs($latitude/1000), abs($longitude/1000), $latitude/1000, $longitude/1000);
      if ( $startnum == 0 ) {
        printf("<td class=\"ranking\">%3.2f" . $lat_side . ", %3.2f" . $long_side . "</td>\n", abs(round($latitude/1000,2)), abs(round($longitude/1000,2)));
      } else {
        printf("<td class=\"ranking\">" . $mapurl . "%3.2f" . $lat_side . ", %3.2f" . $long_side . "</a>, <a target=\"_gm\" href=\"http://maps.google.fr/maps?ie=UTF8&amp;z=8&amp;ll=%f,%f&amp;t=k\">GM</a></td>\n", abs($latitude/1000), abs($longitude/1000), round($latitude/1000,2), round($longitude/1000,2));
      }

      // Affichage de la vitesse
      //printf( "<td>".$strings[$lang]["knots"]."</td>\n", $usersObj->boatspeed);

      // Affichage du cap du voilier
      //printf( "<td>"."%3d"."</td>\n", $usersObj->users->boatheading);

      // La progression des dernieres heures
      printf( "<td>%3.2f</td>\n", $row['last1h']);
      printf( "<td>%3.2f</td>\n", $row['last3h']);
      printf( "<td>%3.2f</td>\n", $row['last24h']);
      // If player is reaching the same WP as the first boat, we give the distance
      // between the two players
      
      if ( $disttype == "tofirst" ) {
        $dtl=ortho($FirstLat,$FirstLon, $latitude, $longitude);
      } else {
        if ( $row['nwp'] == $FirstNwp ) {
          $dtl=$row['dnm']-$FirstDnm ;
        } else {
          $dtl=max($dtl,ortho($FirstLat,$FirstLon, $latitude, $longitude));
        }
      }

      // Remarque Batafieu du 29/12 sur l'avance réelle de Toushuss
      // On compare les distances ortho entre les bateaux
      printf( "<td>%3.2f</td>\n", $dtl);

      echo "</tr>\n";
      $printed++;
    }

    //table footer
    echo "</tbody>\n";
    echo "</table>\n";
  }


  /* This is the static comparing function: */
  function cmpPosition($a, $b) {
    $al = $a->position;
    $bl = $b->position;
    return ($al > $bl);
  }


  //==================================================//
  //                                                  //
  // Function dispHtmlForm($strings, $lang)           //
  //                                                  //
  //--------------------------------------------------//
  //                                                  //
  //            Display a htmltable                   //
  //           w ith a form to select the             //
  //           boats to se lect                       //
  //                                                  //
  //==================================================//
  function dispHtmlForm($strings, $lang, $list )
  {
    //table header
    echo "\n<table>\n";
    /*
      echo "<thead>\n";
      echo "<tr>\n";
      //echo "<th>".$strings[$lang]["position"]."</th>\n";
      echo "<th class=htmltable>&nbsp;</th>\n";
      //echo "<th>".$strings[$lang]["skipper"]."</th>\n";
      echo "<th class=htmltable>".$strings[$lang]["boat"]."</th>\n";
      echo "<th class=htmltable>&nbsp;</th>\n";
      echo "<th class=htmltable>".$strings[$lang]["boat"]."</th>\n";
      echo "<th class=htmltable>&nbsp;</th>\n";
      echo "<th class=htmltable>".$strings[$lang]["boat"]."</th>\n";
      echo "<th class=htmltable>&nbsp;</th>\n";
      echo "<th class=htmltable>".$strings[$lang]["boat"]."</th>\n";
      echo "</tr>\n";
      echo "</thead>\n";
    */
    echo "<tbody class=\"htmltable\">\n";
    //echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>\n";
    //for xhtml  compliance, find other solution
    // idraces , idusers , nwp  , dnm  , latitude , longitude , last1h  , last3h  , last24h
    $query_listusers = "SELECT DISTINCT RR.idusers idusers, US.username username, US.boatname boatname, US.color color, US.country country , US.engaged engaged" . 
      " FROM  races_ranking RR, users US " . 
      " WHERE RR.idusers = US.idusers " . 
      " AND   engaged != 0 " . 
      " ORDER by engaged desc, nwp desc, dnm asc, RR.idusers asc";
    //    " AND   RR.idraces = "  . $this->races->idraces . 

    $result = wrapper_mysql_db_query_reader($query_listusers) or die ($query_listusers);

    $key = 0;
    $lastrace=0;
    $printtd=0;

    while( $row = mysql_fetch_assoc( $result ) ) {
      if ( $row['engaged'] != $lastrace ) {
        $lastrace = $row['engaged'];
        if ( $printtd != 0 ) {
          echo "</tr>" ;
          $printtd = 1;
        }
        echo "<tr class=\"htmltable\">";
        echo "<td class=\"htmltable\" colspan=\"8\"><input type=\"submit\" name=\"action\" value=\"" . $strings[$lang]["valider"] . "\" /></td>";
        echo "</tr><tr class=\"htmltable\">";
        echo "<td class=\"htmltable\" colspan=\"8\"><b>RACE " . $lastrace . "</b></td>";
        echo "</tr><tr class=\"htmltable\">";
        $key=0;
      }
      //table lines
      $key++;
      echo "<td class=\"htmltable\">".$key."</td>\n";
      echo "<td class=\"htmltable\">";
      echo $this->htmlFlagImg($row['country']);

      // ============= Affichage des noms de bateaux en acronyme
      //echo "<td class=htmltable>" ;
      printf("<input type=\"checkbox\" name=\"list[]\" value=\"%s\" ", $row['idusers'] );
      if ( in_array($row['idusers'], $list  ) || (empty($list[0]) ))
        echo " checked=\"checked\"";
      echo " />";

      echo "<acronym onmousedown=\"javascript:palmares=popup_small('palmares.php?lang=".
        $lang."&amp;type=palmares&amp;idusers=" . $row['idusers'] . 
        "', 'palmares');\" style=\" border-bottom: solid #" . $row['color'] . "\" " .
        "title=\"". $row['boatname'] . "\">" ;
      if ( $row['engaged'] == $this->races->idraces ) {
        echo "<b>" . $row['username'] . "</b>";
      } else {
        echo $row['username'] ;
      }
      echo " (". $row['idusers'] . ")"  ;

      echo "</acronym>\n";

      echo "</td>";

      if ( $key/4 == floor($key/4) ) echo "</tr></tr>\n";
    }
    echo "</tr>\n";

    //table footer
    echo "</tbody>\n";
    echo "</table>\n";

  }
  
  //================================================//
  //                                                //
  // Function dispHtmlRacesResults($strings, $lang) //
  //                                                //
  //------------------------------------------------//
  //                                                //
  //            Display race results.               //
  //        Nothing to display if no boat           //
  //               arrived yet .....                //
  //                                                //
  // * $status = type de classement                 //
  //================================================//
  function dispHtmlRacesResults($strings, $lang, $status, $sortkey = "duration" , $sortorder = "asc", $WP = 0 , $startnum=1)
  {

    $IDU=intval(getLoginId());
    if ( $IDU != 0 ) {
      $list = explode ("," , getUserPref($IDU,"mapPrefOpponents") );
    } else {
      $list = "empty";
    }

    /*
      POUR CLASSEMENT DE TYPE TOTALTIME (temps cumulé entre plusieurs manches)
      select idusers,sum(duration) from races_results where idraces in (404402, 404401) and position=1 and idusers in (select distinct(idusers) from races_results where idraces=404402 and position=1) group by idusers order by sum(duration) ASC limit 20 ;
    */

    // WP=0 : classement à l'arrivée
    if ( $WP == 0 ) {
      $query = "SELECT RR.position, RR.duration + RR.penalty duration, RR.idusers idusers, username, 
                        color, country, boatname, longitude, latitude, RR.deptime deptime, RR.loch loch, penalty
              FROM      races_results RR, users US
              WHERE     idraces=".$this->races->idraces."
              AND       US.idusers = RR.idusers
              AND       position=" . $status . " " ;

      /* Gaffe à l'injection SQL : normalement, c'est OK */
      $valid_sortkeys=array("duration", "deptime", "arrtime","loch");
      if ( in_array($sortkey, $valid_sortkeys) ) {
        if ( $sortkey == "arrtime" ) $sortkey = "deptime + duration + penalty ";
        $query .= " ORDER BY " . $sortkey ;
      } else {
        //$query .= " ORDER BY deptime+duration";
        printf ("<h1>You should not do that, this sortkey is not accepted : %s</h1>\n", $sortkey); exit;
      }

      $valid_sortorders=array("asc", "desc");
      if ( in_array($sortorder, $valid_sortorders) ) {
        $query .= " " . $sortorder ;
      } else {
        $query .= " ASC";
      }

    } else {
      // WP!=0 : classement au WP donné 
      $sortclause="";
      $valid_sortkeys=array("duration", "arrtime");
      /* Gaffe à l'injection SQL : normalement, c'est OK */
      if ( in_array($sortkey, $valid_sortkeys) ) {
        if ( $sortkey == "duration" ) $sortkey = " duration ";
        if ( $sortkey == "arrtime" ) $sortkey = " time ";
        $sortclause = " ORDER BY " . $sortkey ;
      } else {
        printf ("<h1>You should not do that, this sortkey is not accepted : %s</h1>\n", $sortkey); exit;
      }
      $valid_sortorders=array("asc", "desc");
      if ( in_array($sortorder, $valid_sortorders) ) {
        $sortclause .= " " . $sortorder ;
      } else {
        $sortclause .= " ASC";
      }

      // Cette requete est une adaptation de celle utilisée pour l'arrivée 
      // Elle doit donc retourner les mêmes colonnes dans le meme ordre
      /* $query = "SELECT RR.position, RR.duration, RR.idusers idusers, username, 
         color, country, boatname, longitude, latitude, RR.deptime deptime, RR.loch loch */

      $query = "SELECT " . BOAT_STATUS_ARR . " position, WC.time - WC.userdeptime duration, WC.idusers idusers, username, 
                        color, country, boatname, \"n/a\", \"n/a\", WC.userdeptime deptime, 0
                  FROM      waypoint_crossing WC, users US
                  WHERE     WC.idraces=".$this->races->idraces."
                  AND       WC.time > WC.userdeptime 
                  AND       WC.userdeptime > 0 
                  AND       US.idusers = WC.idusers   
                  AND       idwaypoint=" . $WP . " " ;
      $query .= $sortclause;
      //print_r($query);

    }

    $result = wrapper_mysql_db_query_reader($query); // or die ($query);
    if (mysql_num_rows($result)==0) return;  // on s'arrete là si personne n'est concerné !

    switch ($status) {
    case BOAT_STATUS_ARR:
      echo "<h4>".$strings[$lang]["raceresultarr"]."</h4>";
      break;
    case BOAT_STATUS_DNF:
      echo "<h4>".$strings[$lang]["raceresultdnf"]."</h4>";
      break;
    case BOAT_STATUS_ABD:
      echo "<h4>".$strings[$lang]["raceresultabd"]."</h4>";
      break;
    case BOAT_STATUS_HTP:
      echo "<h4>".$strings[$lang]["raceresulthtp"]."</h4>";
      break;
    case BOAT_STATUS_HC:
      echo "<h4>".$strings[$lang]["raceresulthc"]."</h4>";
      break;
    }

    //display table headers
    echo "<table>\n";
    echo "  <thead>\n";
    echo "    <tr class=\"ranking\">\n";
    if ( $status > 0 ) {
      //echo "      <th>". $strings[$lang]["position"]."</th> \n";
      echo "<th>Pos.</th>\n"; // position au classement
    }
    //    echo "      <th>".$strings[$lang]["country"]."</th>\n";
    echo "      <th>". $strings[$lang]["skipper"]."</th>\n";
    // echo "      <th>". $strings[$lang]["boat"]."</th>\n";
    if ( $status == BOAT_STATUS_ARR ) {
      echo "      <th>". $strings[$lang]["departuredate"]." (GMT)</th>\n";
      echo "      <th>". $strings[$lang]["arrived"]." (GMT)</th>\n";
    }
    echo "      <th>". $strings[$lang]["totaltime"]."</th>\n";
    if ( $status == BOAT_STATUS_ARR ) {
      echo "      <th>". $strings[$lang]["ecart"]."</th>\n";
    }
    if ( $status == BOAT_STATUS_DNF ) {
      echo "      <th>". $strings[$lang]["position"]."</th>\n";
    }

    // Dernière colonne : loch
    echo "      <th>". $strings[$lang]["loch"]."</th>\n";
    if ( $status == BOAT_STATUS_ARR ) {
      echo "      <th>". $strings[$lang]["penalite"]."</th>\n";
    }

    echo "    </tr>\n";
    echo "  </thead>\n";
    echo "  <tbody>\n";
    //echo "    <tr><td></td><td></td><td></td><td></td></tr>\n";

    //see the races result table
    $rank = 0; $printed=0;
    while ($row = mysql_fetch_assoc($result))
      {
        // Si on a déjà affiché suffisament de lignes, on rend la main
        if ( $startnum > 0 && $printed >= MAX_BOATS_ON_RANKINGS ) break;

        if ( $row['position'] ==  BOAT_STATUS_ARR ) {
          $duration = duration2string($row['duration'] );
          $arrivaltime = $row['deptime'] + $row['duration'] ;
        }

        $rank++;
        if ( $rank == 1 ) {
          $ref_duration = $row['duration'] ;
          $ref_deptime  = $row['deptime'] ;
          $ref_arrivaltime = $row['deptime'] + $row['duration'] ;
        }
	
        // On saute les "N"(startnum) premiers
        if ( $startnum > 0 && $rank < $startnum ) continue;

        if ( $row['idusers'] == $IDU ) {
          $class="class=\"hilight\"";
        } else if ( $list != "empty" && in_array($row['idusers'], $list) )  {
          $class="class=\"hilightopps\"";
        } else {
          $class="class=\"ranking\"";
        }
        echo "<tr " . $class . ">\n";

        if ( $status > 0 ) echo "      <td>". $rank."</td>\n";
        echo "<td class=\"ranking\">";
        echo $this->htmlFlagImg($row['country']);
        echo "<acronym onmousedown=\"javascript:popup_small('palmares.php?lang=".$lang."&amp;type=palmares&amp;idusers=" . $row['idusers'] . "', 'palmares');\" style=\" border-bottom: solid #" . $row['color'] . "\" " .
          "title=\"". $row['boatname'] . "\">" . 
          " (". $row['idusers'] . ") " . 
          $row['username'] .
          "</acronym>\n";
        echo "</td>\n";

        $longitude=$row['longitude'];
        $latitude=$row['latitude'];

        // Mise en forme longitude/latitude

        // Position
        // Longitude : W ou E
        if ( $longitude > 0 )
          {
            $long_side='E';
          } else {
          $long_side='W';
        }

        // Latitude : N ou S
        if ( $latitude > 0 )
          {
            $lat_side='N';
          } else {
          $lat_side='S';
        }

        if ( $row['position'] == BOAT_STATUS_ARR ) {
          printf("      <td>%s</td>\n", gmdate("Y/m/d H:i:s",$row['deptime']));
          //        printf("      <td>%s</td>\n", gmdate("Y/m/d H:i:s",$this->races->deptime + $row[duration]));
          printf("      <td>%s</td>\n", gmdate("Y/m/d H:i:s",$row['deptime'] + $row['duration']));
          printf("      <td>".$strings[$lang]["days"]."</td>\n",$duration['days'],$duration['hours'],$duration['minutes'],$duration['seconds']);
        } else {
          switch ($row['position']) {
          case BOAT_STATUS_HC:
            printf("      <td>HC</td>\n");
            break;
          case BOAT_STATUS_HTP:
            printf("      <td>HTP</td>\n");
            break;
          case BOAT_STATUS_DNF:
            printf("      <td>DNF</td>\n");
            break;
          case BOAT_STATUS_ABD:
            printf("      <td>ABD</td>\n");
            break;
          }
        }
        // Calcul de l'écart (temps de course dans un cas, heure d'arrivée dans l'autre)
        if ( $row['position'] == BOAT_STATUS_ARR ) {
          if ( $rank == 1 ) {
            printf("<td>%s</td>\n",$strings[$lang]["winner"]);
          } else {
            if ( $this->races->racetype == RACE_TYPE_CLASSIC) { // && $WP == 0 ) { - Cf. ticket #237
              // ARRIVAL DATE IS THE SORTING KEY
              $ecart = duration2string($arrivaltime - $ref_arrivaltime);
              // PCT =      difference de temps de course / temps de course du vainqueur
              $pct=round(($arrivaltime - $ref_arrivaltime)/($ref_arrivaltime-$ref_deptime)*100,2);
              //printf ("AT=%d, RAT=%d\n",$arrivaltime , $ref_arrivaltime);
            } else {
              // RECORD : the shortest racetime is the record 
              $ecart = duration2string($row['duration'] - $ref_duration);
              // PCT =    difference de temps de course  / temps du premier
              $pct=round(($row['duration'] - $ref_duration)/$ref_duration*100,2);
              //printf ("DU=%d, RDU=%d\n",$row[duration] , $ref_duration);
            }
            printf("<td>".$strings[$lang]["days"]."(+%2.2f&#37)</td>\n",$ecart['days'],$ecart['hours'],$ecart['minutes'],$ecart['seconds'],$pct);
          }
        }
        if ( $row['position'] == BOAT_STATUS_DNF ) {
          $mapurl="<a class=\"ranking\" href=\"" . MAP_SERVER_URL . "/mercator.img.php?idraces=" . $this->races->idraces .
            "&amp;age=24"  . 
            "&amp;lat=". ($latitude/1000) .
            "&amp;long=" . ($longitude/1000) .
            "&amp;maparea=10"  .
            "&amp;tracks=on&amp;windtext=off&amp;age=1&amp;list=myboat&amp;boat=" . $row['idusers'] .
            "&amp;x=800&amp;y=600&amp;proj=mercator&amp;text=right&amp;raceover=true\" target=\"_new\">"  ;

          // Affichage de la position
          printf("<td>" . $mapurl . "%3.3f&deg;" . $lat_side . ", %3.3f&deg;" . $long_side . "</a></td>\n", abs($latitude/1000), abs($longitude/1000), $latitude/1000, $longitude/1000);
        }

        // Affichage du loch (ARR, DNF, ABD)
        if ( $row['loch'] != 0 ) {
          printf("<td>%5.2f</td>\n", $row['loch']);
        } else {
          printf("<td>n/a</td>\n");
        }
        if ( $row['position'] == BOAT_STATUS_ARR ) {
          if ( $row['penalty'] == 0 ) {
            printf("<td>n/a</td>\n");
          } else {
            printf("<td>%0d h</td>\n", $row['penalty']/3600);
          }
        }
        echo "    </tr>\n";
        $printed++;
      }
    echo "  </tbody>\n";
    echo "</table>\n";

    return ($rank);
  }

  //================================================//
  //                                                //
  // Function getRacesBoundaries()                  //
  //                                                //
  //------------------------------------------------//
  //                                                //
  //            compute the extreme                 //
  //        points in north, south,east and west    //
  //               return 4 values in an array      //
  //                                                //
  //================================================//

  function getRacesBoundaries()
  {
    $S = min ( $this->races->startlat, $this->races->stop1lat, $this->races->stop2lat )/1000 - 0.5;
    $N = max ( $this->races->startlat, $this->races->stop1lat, $this->races->stop2lat )/1000 + 0.5;
    $W = min ( $this->races->startlong, $this->races->stop1long, $this->races->stop2long )/1000 - 0.5;
    $E = max ( $this->races->startlong, $this->races->stop1long, $this->races->stop2long )/1000 + 0.5;

    return(
           array("north" => $N, 
                 "south" => $S,
                 "east"  => $E,
                 "west"  => $W)
           );

  }

  function raceNumEngaged() {
    $query = "SELECT count(*) FROM users WHERE engaged=" . 
      $this->races->idraces;
    $result = wrapper_mysql_db_query_reader($query);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    return ($row[0])  ;
  }

  function htmlFlagImg($idflag) {
    return "<img src=\"/flagimg.php?idflags=".$idflag. "\" alt=\"Flag_".
      $idflag."\" />";
  }
  
} // End class fullRaces



//****************************************************************************//
//                                                                            //
//                             CLASS racesList                                //
//                             ---------------                                //
//                                                                            //
//****************************************************************************//

class racesList {
  var $records = array();

  function racesList() {
    $query = "SELECT idraces FROM races order by deptime desc";
    //printf ($query . "\n");
    $result = wrapper_mysql_db_query_reader($query);
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $racesFullObj = new fullRaces( $row['idraces'] )  ;
      array_push ($this->records, $racesFullObj);
    }
  }
}


class startedRacesList {
  var $records = array();

  function startedRacesList() {
    $this->records = array();
    $query = "SELECT idraces FROM races WHERE started > 0 ";
    
    $minute = date('i');
    
    if ( $minute % 10 == 0 ) {
      $query .= " and vacfreq in (1,2,5,10) " ;
    } else if ( $minute % 5 == 0 ) {
      $query .= " and vacfreq in (1,5) " ;
    } else if ( $minute % 2 == 0 ) {
      $query .= " and vacfreq in (1,2) " ;
    } else {
      $query .= " and vacfreq = 1 " ;
    }

    $query .= " order by vacfreq ASC, deptime DESC";
    $result = wrapper_mysql_db_query_reader($query);
    
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($this->records , $row['idraces']);
    }
  }
}

?>
