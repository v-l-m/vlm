<?php
if ($verbose >= 0) {
  echo "\n(".$nb_boats.") Checking user ".$usersObj->idusers . ": (race " . $usersObj->engaged . ") ";
}
$is_arrived = false;

//need to get fullUsers Object (if it is possible, and only for virtual users)
if ( $usersObj->engaged != 0 ) {
  // Check Auto Pilote : if there is a command in the spool, then execute it
  $flag_pilototo = $usersObj->pilototoCheck();

  $fullUsersObj = new fullUsers($usersObj->idusers, $usersObj, $fullRacesObj); // use the copy mode

  if ( $flag_pilototo == true ) {
    $fullUsersObj->updateAngles();
  }

  // If boat is mooring (pilotmode=PILOTMODE_WINDANGLE & pilotparameter=0)
  $timestamp=time();
  if ( ( $fullUsersObj->users->pilotmode == PILOTMODE_WINDANGLE
	 &&  abs( $fullUsersObj->users->pilotparameter ) <= 1 )
       ||  
       ($fullUsersObj->users->releasetime > $timestamp) )  {

    printf ("=== Player %d is mooring : ",$fullUsersObj->users->idusers);
    // Compare (last_change timestamp + MAX_STOPTIME) and now
    //printf ("Lastchange = %d, MAXSTOP=%d, time=%d, ", $fullUsersObj->users->lastchange, MAX_STOPTIME, $timestamp);

    // Si le bateau est au mouillage et n'est pas parti, c'est qu'il attend le d�part
    // cas des d�parts au pilototo, pour partir en groupe , apr�s le d�part officiel,
    // dans les courses permanentes
    // ==> 2007/09 : handle auto-pilot start (userdeptime =-1 if boat has not started)
    // (test this and write a new position only if boat already started)
    if ( $fullUsersObj->users->userdeptime == -1 ) {

      // She has not started, if race is closed, set this boat to ABD
      if ( $fullRacesObj->races->closetime < $now ) {
	$fullUsersObj->setDNF();
	printf ("========= Player %d set to DNF (Late Start) ========\n",$fullUsersObj->users->idusers);
      } else {
	printf ("========= Player %d did not start yet ========\n",$fullUsersObj->users->idusers);
      }

    } else {

      // If the boat has been mooring for too long, set it DNF
      if ( $fullUsersObj->users->lastchange + MAX_STOPTIME < $timestamp ) {

	$fullUsersObj->setDNF();
	printf ("========= Player %d set to DNF =========\n",$fullUsersObj->users->idusers);

        // Else write a new position at the same place, boat won't move this time
      } else {
	$fullUsersObj->lastPositions->writePositions();
	$fullUsersObj->writeCurrentRanking();
      }
    }
    echo "\t** DONE ** " ;

  } else {
    // userdeptime est mis � -1 dans subscribeToRaces
    // Donc s'il vaut -1 ici, c'est que le joueur prend le d�part d'une course
    if ( $fullUsersObj->users->userdeptime == -1 ) {
      // update userdeptime in table users 
      // ($now est positionn� dans check_race.php pour mettre tout le monde � �galit�)
      $fullUsersObj->updateDepTime($now);
    }
  
    echo "PIM=" . $fullUsersObj->users->pilotmode . "/"  ;
    if ( $fullUsersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
      echo "PIP=" . $fullUsersObj->users->pilotparameter . "/"  ;
    }
    echo "Heading=". $fullUsersObj->users->boatheading ;

    if ( $fullUsersObj->users->pilotmode == PILOTMODE_ORTHODROMIC
	   or $fullUsersObj->users->pilotmode == PILOTMODE_BESTVMG
	   or $fullUsersObj->users->pilotmode == PILOTMODE_VBVMG ) {

      echo ", Reaching position=" . giveDegMinSec("engine",$fullUsersObj->LatNM/1000, $fullUsersObj->LongNM/1000);
      if ( $fullUsersObj->users->targetlong == $fullUsersObj->LongNM/1000 
	   && $fullUsersObj->users->targetlat == $fullUsersObj->LatNM/1000   ) {
	echo " MyWP=(" . $fullUsersObj->users->targetlat . "," . $fullUsersObj->users->targetlong . ")" ;
	if ( $fullUsersObj->users->targetandhdg != -1 ) {
	  printf(" @WHP=%d", $fullUsersObj->users->targetandhdg);
	} else {
	  echo " NO WPH ";
	}
      } else {
	echo " bestWayToWP = (" . $fullUsersObj->LatNM/1000 . "," . $fullUsersObj->LongNM/1000 . ")" ;
      }

      $dist=ortho($fullUsersObj->lastPositions->lat, $fullUsersObj->lastPositions->long, 
		  $fullUsersObj->LatNM, $fullUsersObj->LongNM);
      echo ", dist=".round($dist,3)."nm" ;
    }
    echo "\n";

    $lonAvant=$fullUsersObj->lastPositions->long;
    $latAvant=$fullUsersObj->lastPositions->lat;
    $timeAvant=$fullUsersObj->lastPositions->time;

    // Updating positions
    $fullUsersObj->lastPositions->addDistance2Positions(
			         $fullUsersObj->boatspeed*$fullUsersObj->hours,
				 round($fullUsersObj->users->boatheading,2));

    $lonApres=$fullUsersObj->lastPositions->long;
    $latApres=$fullUsersObj->lastPositions->lat;
    $timeApres=$fullUsersObj->lastPositions->time;

    $distVac=round($fullUsersObj->boatspeed*$fullUsersObj->hours,3);

    echo "\tPosition update (WS=" . round($fullUsersObj->wspeed,1) . 
      ", WH=".round($fullUsersObj->wheading,1). 
      ", Hours=".round($fullUsersObj->hours,4). 
      ", ANG=".round($fullUsersObj->boatanglewithwind,2). 
      ", Hdg=" . $fullUsersObj->users->boatheading. 
      ", BS=" . round($fullUsersObj->boatspeed,3). 
      ", Dist=". $distVac . "nm".
      ") ...";

    // Writing positions
    $fullUsersObj->lastPositions->writePositions(); //important, will write a new position
    echo " done";

    // ==========================
    //  Do they cross the coast ?
    // ==========================
    include "check_coast_crossing.php";
    // ==========================
    // Does he cross a waypoint
    // ==========================
    include "check_waypoint_crossing.php";
    $fullUsersObj->writeCurrentRanking();

    if ($crosses_the_coast && ! $is_arrived) {
      $fullUsersObj->setSTOPPED(); // sets the boat mooring
      $fullUsersObj->users->lockBoat($fullRacesObj->races->coastpenalty); // Boat is locked
      
      $fullUsersObj->lastPositions->lat=$latApres;
      $fullUsersObj->lastPositions->long=$lonApres;
      $fullUsersObj->lastPositions->writePositions(); //important, will write a new position at this place
      
      // set wind angle to 0
      $fullUsersObj->users->pilotmode=2;
      $fullUsersObj->users->pilotparameter=0;
    }

    // =========================================================================
    // Check if boat uses its own WP and is close to it (only if PIM != 1 or 2 )
    // If yes, we de-activate this WP (and boat reaches the "next race waypoint"

    //      ** THIS TEST IS OPTIMIZED for PERF (>=PILOTMODE_ORTHODROMIC)            **
    //    should test "pim is 3 or pim is 4 in real life because of a future pim=5" ..
    // ===============================================================================
    if (  $fullUsersObj->users->pilotmode >= PILOTMODE_ORTHODROMIC 
	  && ( $fullUsersObj->users->targetlong != 0 || $fullUsersObj->users->targetlat != 0 ) ) {

      $distAvant=ortho($latAvant, $lonAvant,
		       $fullUsersObj->users->targetlat*1000, $fullUsersObj->users->targetlong*1000);
      $distApres=ortho($latApres, $lonApres,
		       $fullUsersObj->users->targetlat*1000, $fullUsersObj->users->targetlong*1000);
     
      // On lache le WP perso si il est plus pr�s que la distance parcourue � la derni�re VAC.
      if ( $distAvant < $fullUsersObj->boatspeed*$fullUsersObj->hours 
	   || $distApres < $fullUsersObj->boatspeed*$fullUsersObj->hours ) {

	printf("\n\t** BOAT POSITION (Lon=%f, Lat=%f) **\n", 
               $lonApres/1000, $latApres/1000);
	printf("\t** USER WP (Lon=%f, Lat=%f) reached (dist=%f), deactivating it **\n", 
	       $fullUsersObj->users->targetlong, 
	       $fullUsersObj->users->targetlat, 
	       $dist);

	// ABANDON DU WP PERSO => 
	// 4eme argument "abandonWP" pour diff�rencier abandon de WP(true) de la saisie de WP(false)
	$fullUsersObj->updateTarget(0,0,0, true);

      }
    }

    // ==========================
    //  Now all is done.
    // ==========================
    echo "\t** Pilotmode=" . $fullUsersObj->users->pilotmode ;
    if ( $fullUsersObj->users->pilotmode == PILOTMODE_WINDANGLE ) {
      echo "/" .$fullUsersObj->users->pilotparameter ;
    }

    // ===================================================================
    // MAJ du cap du bateau, pour la prochaine VAC si r�gulateur d'allure.
    // * Pour mettre le bateau bout au vent si il tape la cote
    // * Pour MAJ du pilote orthodromique si on a pass� un WP
    // ===================================================================
    if ( $fullUsersObj->users->pilotmode == PILOTMODE_WINDANGLE 
	 OR $fullUsersObj->users->pilotmode == PILOTMODE_ORTHODROMIC 
	 OR $fullUsersObj->users->pilotmode == PILOTMODE_BESTVMG 
	 OR $fullUsersObj->users->pilotmode == PILOTMODE_VBVMG )  {

      $fullUsersObj->updateAngles();
      echo ", Angle updated";

    }
    echo ", Heading = " . $fullUsersObj->users->boatheading;
    echo "\n\t** DONE ** ";
    //sleep (2);
    // } // not arrived
  } // player is not sleeping
} 
?>
