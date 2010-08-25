<?php
include_once("vlmc.php");
include_once("functions.php");

// ==================================
//  Do they cross the next waypoint ?
// ==================================
  
// This code verifies using 2 functions if a waypoint is beeing crossed by a boat
// ======== since june/2007, if $fullUsersObj->nwp == 0 , it sees if the start time
//          is reached. Is is used to implement a pre-start for big races.
//          ==> in race 55 (Hi-Five), boats start from a same point one hour before
//              the "start-time" (races:deptime). But they can't cross the waypoint 0 before
//         deptime + 3600 (races:prestart_duration)
//          ==> To give a race without a pre-start, first waypoint must have a wporder > 0
//              ==> Most races should have no pre-start, to be subscribed by "away from the net" people
  
// The code used to implement the pre-start is to check if deptime + prestart-duration < time_of_wp_crossing
//              ==> only if a waypoint is crossed, then if this WP is WP0,

$wp_xingratio = new doublep();
$wp_xinglat   = new doublep();
$wp_xinglong  = new doublep();

// if we crossed the coast, get the crossing point and not the computed
// point (as it can hide a potential waypoint xing)
if ($crosses_the_coast) {
  $latCheck = doublep_value($coast_xinglat);
  $lonCheck = doublep_value($coast_xinglong);
} else {
  $latCheck = $latApres;
  $lonCheck = $lonApres;
}
 
$latPreCheck = $latAvant;
$lonPreCheck = $lonAvant;

do {
  // 1- find the coordinates of user's next waypoint
  printf ("\n\tNext Waypoint is %d", $fullUsersObj->nwp);
  $nextwaypoint = $fullRacesObj->races->giveWPCoordinates($fullUsersObj->nwp);
  // ==> lat1, long1, lat2, long2

  // 2 - verify if the boat has crossed this waypoint
  $encounterCoordinates = array();
  echo (", checking for WP crossing... ");
  printf ("\n\t\t* WP   : %f, %f <---> %f, %f", 
	  rad2deg($nextwaypoint->latitude1), rad2deg($nextwaypoint->longitude1), 
	  rad2deg($nextwaypoint->latitude2), rad2deg($nextwaypoint->longitude2));
  printf ("\n\t\t* BOAT : %f, %f <---> %f, %f", $latPreCheck/1000, $lonPreCheck/1000, $latCheck/1000, $lonCheck/1000);

  // Test de croisement avec un waypoint
  $waypoint_crossed=false;

  $wp_xed = VLM_check_WP($latPreCheck, $lonPreCheck, $latCheck, $lonCheck,
			 &$nextwaypoint, $wp_xinglat, $wp_xinglong, $wp_xingratio);

  switch ($wp_xed) {
  case -1:
    $waypoint_crossed=true;
    $invalid_crossing=true;
    break;
  case  1:
    $waypoint_crossed=true;
    $invalid_crossing=false;
    break;
  case 0:
  default:
    $waypoint_crossed=false;
  }

  if ($invalid_crossing) {
    echo "\n\t\t*** Yes INVALID ***\n";    
    // we don't really care about the exact crossing date as it is an invalid one
    $fullUsersObj->recordWaypointCrossing(time(), 0);
    // exit the do-while loop now.
    break;
  }

  // FIXME refine test, care in a special way for invalid crossing
  if ($waypoint_crossed == true && $invalid_crossing == false) {
    echo "\n\t\t*** Yes (vlm-c) ***\n";
    if (($nextwaypoint->type & (WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE))!= 0) {
      // we have a clockwise/anti-clockwise check... now verify that we have
      // or not an invalid crossing. If so, delete it (odd/even crossings)
      if ($fullUsersObj->checkInvalidWaypointCrossing() != 0) {
	echo "\n\t\t*** INVALID CROSSING ***\n";
	// yeah we got one! ABORT ABORT! :)
	$fullUsersObj->clearInvalidWaypointCrossing();
	// exit the do-while loop now.
	break;
      }
    }

    $encounterCoordinates = array('latitude' => doublep_value($wp_xinglat), 
				  'longitude' => doublep_value($wp_xinglong)); 
    
    // we update the starting point to avoid crossing two lines in a row in
    // reverse order (bugs #294)
    $latPreCheck = $encounterCoordinates['latitude'];
    $lonPreCheck = $encounterCoordinates['longitude'];
    echo "\t==>Player ".$fullUsersObj->users->idusers . " crossed waypoint " .
      $fullUsersObj->nwp ;
    // If it is a start line (nwp == 0), we have to compute the crossing coordinates
    // Then to compute the crossing time, and compare it to deptime + prestart-duration
          
    // distanceSinceLastUpdate = dist entre derni�re position et ce coint
    $distanceSinceLastUpdate = ortho($latPreCheck, $lonPreCheck,
				     $latAvant, $lonAvant);
          
    // Temps de course (entre d�part et passage de la ligne )
    // ======================================================
    // Calcul exact de duration. 
    // On en prend $fullRacesObj->races-deptime (si racetype =0),
    // mais on prend $fullUsersObj->users->userdeptime si racetype = 1
    if ( $fullRacesObj->races->racetype == RACE_TYPE_CLASSIC ) {
      $deptime = $fullRacesObj->races->deptime  ;
    } else {
      // Cas RACE_TYPE_RECORD
      $deptime = $fullUsersObj->users->userdeptime  ;
      // Au cas o� probl�me de MAJ de userdeptime (cf arriv�e de la 46)
      //$deptime = $fullRacesObj->races->deptime  ;
    }
          
    // Duration c'est la somme de :
    /*     temps de course �coul� jusqu'� la vacatin d'avant
	   + temps �coul� entre la vacation d'avant et le temps de passage de la marque (mesur� maintenant)
    */
    //    $timeSinceLastUpdate = (time() - $timeAvant) * doublep_value($wp_xingratio); (use this if we settle the time for the whole run)
    $timeSinceLastUpdate = ($distanceSinceLastUpdate / $fullUsersObj->boatspeed) * 3600 ;

    $duration = $timeAvant - $deptime + $timeSinceLastUpdate  ;

    $fullUsersObj->recordWaypointCrossing($timeAvant + $timeSinceLastUpdate);
          
    // Is it the last one (aka finish line ?)
    // giveNextWaypoint returns -1 if this was the finish line
    // else it gives the next waypoint
    $nextwaypointid = $fullUsersObj->giveNextWaypoint();
          
    if ( $nextwaypointid != -1 ) {
            
      // It was not the Finish Line, update user's nextwaypoint
      $fullUsersObj->users->nwp = $fullUsersObj->nwp = $nextwaypointid;
      // compute the distance to this waypoint
            
      $fullUsersObj->updateNextWaypoint();
      $is_arrived=false;
            
    } else {
          
      // RACE IS FINISHED FOR THIS BOAT : record result and continue the loop
      $is_arrived=true;
            
      echo "\t==>Course =" . $fullRacesObj->races->idraces . "\n";
      echo "\t==>encounterCoordinates = " . $encounterCoordinates['latitude'] . "/" . $encounterCoordinates['longitude'] . "\n";
      // encounterCoordinates est le point o� la ligne a �t� coup�e
            
      if ($verbose>=0) {
	echo "\t\tBoatspeed : " . $fullUsersObj->boatspeed . ", ";
	echo "distanceSinceLastUpdate = $distanceSinceLastUpdate nm \n"; 
	echo "\t\ttimesincelastupdate = " . $timeSinceLastUpdate . " sec, duration = $duration sec \n";
	echo "\t\tDeptime = " . $deptime . "\n";
      }
            
      //insert score in database (or update if it is a "TYPE_RECORD" race)
      if ( $fullRacesObj->races->racetype == RACE_TYPE_CLASSIC ) {
	$query = "INSERT INTO races_results 
                             ( idraces, idusers, position,  deptime, duration, loch, longitude, latitude)
                             VALUES ("  . $fullRacesObj->races->idraces .
	  " , ". $fullUsersObj->users->idusers .
	  " , ". BOAT_STATUS_ARR .
	  " , ". $deptime .
	  " , ". $duration .
	  " , ". $fullUsersObj->users->loch .
	  " , ". $lonApres .
	  " , ". $latApres   .")";
      } else {
	// Cas RACE_TYPE_RECORD : on MAJ le resultat seulement s'il est meilleur.
              
	// R�cup�ration de l'�ventuel temps de r�f�rence getOldDuration($idraces,$idusers)
	$oldDuration=getOldDuration($fullRacesObj->races->idraces, $fullUsersObj->users->idusers);
              
	// ==> Si 0 : pas de temps de r�f�rence, on REPLACE
	//     ou si $duration est meilleur (<), on REPLACE
	if ( $oldDuration <= 0  OR  $duration < $oldDuration ) {
                
	  $query = "REPLACE INTO races_results 
                                     ( idraces, idusers, position,  deptime, duration,loch ,longitude, latitude)
                                          VALUES ("  . $fullRacesObj->races->idraces .
	    " , ". $fullUsersObj->users->idusers .
	    " , ". BOAT_STATUS_ARR .
	    " , ". $deptime .
	    " , ". $duration .
	    " , ". $fullUsersObj->users->loch .
	    " , ". $lonApres .
	    " , ". $latApres .")";
	} else {
	  // sinon, on oublie cette tentative.      
	  $query = "NOQUERY";
	}
      }
            
      if ( $query != "NOQUERY" ) {
	if ($verbose >0 ) echo $query;
	$result = wrapper_mysql_db_query_writer($query);
	printf ("\t\tBoat arrived...\n");
      } else {
	printf ("Boat arrived, but (%d) is not better (%d)\n", $duration, $oldDuration);
      }
            
      //remove player from race
      $fullUsersObj->removeFromRaces();
            
      // CONTINUE this loop
    } // Test waypoint was the last one
  } // Test a waypoint was crossed
        
  // Now, let's see if the boat crosses the coast.
  else  {
    // Did not Cross the line, nor any waypoint ?
    echo ":\t *** NO ***";
  } // FIN TEST WAYPOINT  CROSSED
} while ( $is_arrived != true && $waypoint_crossed == true );
?>
