<?php
	       // We must check for multiple subdivisions of the last segment (LastPosisition -> currentPosition)
	       // Because if the boat is fast, it can cross a small island.
	       // Determine sub-vectors
$verbose=0;
	       $segStartLong=$lonAvant;
	       $segStartLat =$latAvant;

	       $segStopLong=$lonApres;
	       $segStopLat =$latApres;

	       // We will consider multiple subvectors : max(speed,distance since last update))
	       // $num_subvectors=NUM_SUBVECTORS;
               // ESSAI : nombre de subvectors = 1/2*vitesse en noeuds. 
               $num_subvectors=ceil( max($fullUsersObj->boatspeed/20, $distVac*10) ) +1;
	       echo "\tChecking for coast crossing (N. svect=$num_subvectors)\n ";

	       $crosses_the_coast=false;

	       /*
               echo "\tBoat positions (before --> now) :";
	       echo "(long=" . $segStartLong/1000 . ", lat=" . $segStartLat/1000 . ")->";
	       echo "(long=" . $segStopLong/1000 . ", lat=" . $segStopLat/1000 . ")\n";
               */

	       // 
	       // This code is heavy, before, we check if ther is a coast near the boat (< 1°)
	       // and we use a temporary table "tmpcoastline" where we only have the local coastline

	       // Choix de la table dans laquelle on va chercher le trait de cote
	       $LONG=180+floor($segStartLong/10000)*10;
	       $LAT=80+floor($segStartLat/10000)*10;
	       $TNAME="CH_".$LONG."_".$LAT . " ";

               $qfill="INSERT INTO tmpcoastline SELECT * from " . DBNAME . "." . $TNAME . " ".
		      " WHERE abs(latitude  - " . $segStartLat / 1000 . ") < " . DISTANCEFROMROCKS  .
		      "   AND abs(longitude - " . $segStartLong / 1000 . ") < " . DISTANCEFROMROCKS ;
	       $res = mysql_db_query('temporary', $qfill);
	       //echo "\n".$qfill;

	       // No Order by in this request
	       // Sans le distinct, on récupère tous les points, donc plusieurs fois le meme idcoast
	       // Avec le distinct + le Limit, on ne récupère que 20 points parmi tous ceux qui répondent
	       // au critère. Ces 20 points ne sont pas forcément les plus proches de chaque bateau.
	       //$qidcoasts="SELECT DISTINCT idcoast " .
	       $qidcoasts="SELECT DISTINCT idcoast " .
	         "  FROM tmpcoastline " .
// on n'a que ces données là dans la table tmpcoastline
//		 " WHERE abs(latitude  - " . $segStartLat / 1000 . ") < " . DISTANCEFROMROCKS .
//		 "   AND abs(longitude - " . $segStartLong / 1000 . ") < " . DISTANCEFROMROCKS .
		 " ORDER BY sqrt( power(latitude  - " . $segStartLat / 1000 . ",2) + power(longitude - " . $segStartLong / 1000 . ",2)) ASC" .
		 ";";
		 //" LIMIT 20;" ;


	       $ridcoasts = mysql_db_query('temporary', $qidcoasts);
	       //echo "\n".$qidcoasts;
	       // For each coast in this area ?
	       //$last_idcoast=0;
	       while ( ($crosses_the_coast==false) && $row = mysql_fetch_array($ridcoasts, MYSQL_NUM) ) {

		  // On n'analyse pas plusieurs fois le meme idcoast
		  //if ( $row[0] == $last_idcoast ) continue;

		  printf ( "\t\tCoast %6d, table %s..." , $row[0] , $TNAME);
		  $last_idcoast = $row[0];

	          // Do we have a coast... in these subvectors
	          $segLongIncrement=($segStopLong - $segStartLong)/$num_subvectors;
	          $segLatIncrement=($segStopLat - $segStartLat)/$num_subvectors;

	          // Conditions d'arrêt :
	          //	crosses_the_coast=true
	          //
	          $subvector=1;
	          while ( $crosses_the_coast==false && $subvector <= $num_subvectors ) {
	       	       $long1=$segStartLong;
	       	       $long2=$segStartLong+$segLongIncrement;

		       $lat1=$segStartLat;
		       $lat2=$segStartLat+$segLatIncrement;

		       // Test antemeridien : si 2 nb sont opposés, leur produit est négatif
		       //                     si une des 2 longitudes n'est pas 0.x ni -0.x, l'autre non plus..
		       if ( ($long1 * $long2 < 0) && floor(abs($long1)) >0 ) {
		          // Ce segment est autour de l'ante-meridien
			  $test_am=true;
			  printf ("** TEST-Ante-meridien activé ** ");
		       } else {
			  $test_am=false;
		       }

		       if ( $verbose >= 1 ) {
		         echo "\t";
		         echo "Subvector = " . $subvector ;
		         echo "  long1=" . $long1;
		         echo "  long2=" . $long2;
		         echo "  lat1=" . $lat1;
		         echo "  lat2=" . $lat2;
		         echo "\n";
		       }

		       // At the end of the loop, $subvector++, $segStartLong=long2, $segStartLat=lat2

		       // We have to find the nearest point (nearest one from the boat)
		       // We only inspect point within the same coast
		       // Our positions are in millidegree
		       // We just use Pythagore to find the nearest points.
		       $qcoastpoint1="SELECT idcoast, idpoint, latitude , longitude " .
		              "  FROM tmpcoastline " .
			      " WHERE idcoast = " . $row[0] . 
//			      " WHERE abs(latitude  - " . $lat1 / 1000 . ") < " . DISTANCEFROMROCKS  .
//			      "   AND abs(longitude - " . $long1 / 1000 . ") < " . DISTANCEFROMROCKS .
			      " ORDER by sqrt(  power((latitude  - " . $lat1  / 1000 . " ),2)" .
			      "               + power((longitude - " . $long1 / 1000 . " ),2)" .
			      "              ) ASC " .
			      "limit " . NUM_REF_POINTS ;

		       $rcoastpoint1 = mysql_db_query('temporary', $qcoastpoint1);
		       //echo ("Query  : " . mysql_error." ".$qcoastpoint1."\n");

		       // Do we have a points in this area ? If yes, let's find the seconde point
		       // This "second point" is the nearest from the first one, from the same coast, but
		       // it can not be the first one
		       while ($crosses_the_coast==false && $row1 = mysql_fetch_array($rcoastpoint1, MYSQL_NUM)) {
		         $point1_idcoast = $row1[0];   

		         $point1_idpoint = $row1[1];
                         /*
                         $inf = $point1_idpoint - NUM_NEAR_POINTS; 
                         $sup = $point1_idpoint + NUM_NEAR_POINTS;
                         */
		         $point1_lat     = $row1[2]*1000;
		         $point1_long    = $row1[3]*1000;

		         $qcoastpoint2="SELECT idcoast, idpoint, latitude , longitude " .
		                "  FROM tmpcoastline " .
			        " WHERE idcoast  = " . $point1_idcoast .
                                "   AND idpoint != " . $point1_idpoint .
                                "   AND abs(  idpoint - $point1_idpoint ) <= " . NUM_NEAR_POINTS . " " .
//                                "   AND idpoint between $inf and $sup " .
/* 

*** MODIF du 29/06/2008 : on ne prend pas les points "les plus proches, mais ceux qui encadrent le plus proche"
*/
/*
                                "   AND round(latitude,8)  != " . round($row1[2],8) .
                                "   AND round(longitude,8) != " . round($row1[3],8) .
*/
/*
			        " ORDER by sqrt(  power((latitude  - " . $row1[2] . " ),2)" .
			        "               + power((longitude - " . $row1[3] . " ),2)" .
			        "              ) " .
*/
                                " ORDER by abs(  idpoint - $point1_idpoint ) " .
                                " limit " . NUM_NEAR_POINTS ;
                                " ;";

		         $rcoastpoint2 = mysql_db_query('temporary', $qcoastpoint2) or die ("FAILED " . $qcoastpoint2);
                         //echo ("Query  : " . mysql_error." ".$qcoastpoint2."\n");

			 // Do we have a second point
		         while ( $crosses_the_coast==false && $row2 = mysql_fetch_array($rcoastpoint2, MYSQL_NUM)) {
                           $point2_idcoast = $row2[0];
                           $point2_idpoint = $row2[1];
		           $point2_lat     = $row2[2]*1000;
		           $point2_long    = $row2[3]*1000;

			   // Si test_am est activé (trajectoire bateau coupe l'AM), il faut aussi s'assurer 
			   // que le segment de cote considéré coupe aussi l'A/M avant d'appeler do_they_cross
			   if ( ( $test_am == true && $point1_long * $point2_long < 0 ) || $test_am == false )  {

//$verbose=1;
			      if ( $verbose >= 1 ) {
			        //echo ("\n\tChecking if player ". $fullUsersObj->users->idusers. " crossed the coast " . $point1_idcoast . " : " );
			 	echo ("\n\tP1=" . $point1_idpoint . ", Lat:" . $point1_lat . ", Long:" . $point1_long . "");
			 	echo (" <--> P2=" . $point2_idpoint . ", Lat:" . $point2_lat . ", Long:" . $point2_long . "");
			      }
//$verbose=0;

	       	       	       // Seems that there is a coast point in the same area as the boat
		               // Let's see if the boat crossed the line between this two points and walks on the ground

		               $encounterCoordinates = array();

			       // Si Dotheycross n'y arrive pas, on teste avec Dotheycross2
		               if ($fullUsersObj->dotheycross2(
					$point1_long, $point1_lat,
					$point2_long, $point2_lat,
					$long1, $lat1,
					$long2, $lat2,
					$encounterCoordinates,
					$verbose
					)
	                         )
		               // This opponent thinks his boat has legs, he leaves us.
		               {
			          echo "\t*** YES player " . $fullUsersObj->users->idusers . " CROSSED (DTC2), ";
	 		          $crosses_the_coast=true;
			       }

			       // Test avec dtc2 seulement si DTC1 n'a rien vu
			       if ($crosses_the_coast != true and $fullUsersObj->dotheycross( 
			    		$point1_long, $point1_lat, 
					$point2_long, $point2_lat, 
					$long1, $lat1, 
					$long2, $lat2, 
					$encounterCoordinates, 
					$verbose
					)
				  )
			       {
			          echo "\n\t*** YES player " . $fullUsersObj->users->idusers . " CROSSED (DTC1), ";
	 		          $crosses_the_coast=true;
		               }
			    }

			    if ( $crosses_the_coast == true ) {
                               echo "\n\t\tThis segments are crossing : \n\t\t\t" ; 
                               printf ("COAST: %f,%f <----> %f,%f\n\t\t\t",
					$point1_lat/1000,$point1_long/1000, $point2_lat/1000,$point2_long/1000);
                               printf ("BOAT : %f,%f <----> %f,%f",
					$lat1/1000,$long1/1000 , $lat2/1000,$long2/1000);

		               echo "\n\t\t\tEncounterCoordinates " . 
                                    $encounterCoordinates[1]/1000 . ", " . $encounterCoordinates[0]/1000 . 
                                    "\n\nGoogleMap http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
                                    $encounterCoordinates[1]/1000 . "," . $encounterCoordinates[0]/1000 .
                                    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";


                               echo "\nVLMMAP http://s9.virtual-loup-de-mer.org/mercator.img.php?idraces=" . $fullUsersObj->users->engaged ;
                               echo "&lat=" . $latAvant/1000;  
                               echo "&long=" .$lonAvant/1000;
                               echo "&maparea=18&tracks=on&age=6";
                               echo "&list=" . $fullUsersObj->users->idusers ;
                               echo "&x=1000&y=600&proj=mercator&text=right";
                               echo "&seg1=".$lat1/1000 . "," . $long1/1000 . ":" . $lat2/1000 . "," . $long2/1000;
                               echo "&seg2=".$point1_lat/1000 . "," . $point1_long/1000 . ":" . $point2_lat/1000 . "," . $point2_long/1000;
                               echo "\n\n";
                               /*
		               echo "\n\t ==> Position Avant " . 
                                    $latAvant/1000 . ", " . $lonAvant/1000 . 
                                    "\n http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
                                    $latAvant/1000 . "," . $lonAvant/1000 .
                                    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";

		               echo "\n\t ==> Position Apres " . 
                                    $latApres/1000 . ", " . $lonApres/1000 . 
                                    "\n http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
                                    $latApres/1000 . "," . $lonApres/1000 .
                                    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";
                               */

		               // Stop the Player at this point
		               $fullUsersObj->setSTOPPED(); // sets the boat mooring
		               $fullUsersObj->users->lockBoat($fullRacesObj->races->coastpenalty); // Boat is locked

			       $fullUsersObj->lastPositions->lat=$segStartLat;
			       $fullUsersObj->lastPositions->long=$segStartLong;
		               $fullUsersObj->lastPositions->writePositions(); //important, will write a new position at this place

			       
		               $fullUsersObj->users->pilotmode=2;
		               $fullUsersObj->users->pilotparameter=0;

			     }


		          } // for each second point
		       } // Do we have a point in this area
			 // Else nothing, we are far away from any coast
		         // Because we cannot find a coastline point in our area

		       // At the end of the loop, $subvector++, $segStartLong=long2, $segStartLat=lat2
		       $segStartLong=$long2;
		       $segStartLat=$lat2;
		       $subvector++;

		    } // While
	 	    if ( ! $crosses_the_coast ) {
		       echo "\t*** NO *** \n";
		    }
	          $segStartLong=$lonAvant;
	          $segStartLat =$latAvant;
	          $segStopLong=$lonApres;
	          $segStopLat =$latApres;


		}
		$query="DELETE from temporary.tmpcoastline;" ; $result = mysql_db_query('temporary',$query);
?>
