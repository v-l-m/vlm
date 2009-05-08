<?php
   $now = time();
   $nboatinrace=$nb_boats;
   echo "\n*** " . gmdate("M d Y H:i:s",$now). "\n*** Checking race " . $fullRacesObj->races->idraces ; 
   //check if someone has ended the race

   // is the race fnished ?
   // ==1 > no more opponents and MAXDEPTIME is not passed
   if ( empty($fullRacesObj->opponents) && $fullRacesObj->races->closetime < $now ) {
        // let's close the race
        $fullRacesObj->stopRaces();
   } 
   // ==2 > winner's time * (1 + firstpcttime/100) is not anymore in the future...
   //    Pour les autres courses :
   //            >0 si le premier n'est pas arrivé ou est arrivé il y a peu de temps
   //            <0 lorsque pourcentage en plus du temps du premier est dépassé
   //        Parameter (verbose) added 2008/03/16 (0 non verbose (myboat.php), 1 verbose (here))
   else if ( $fullRacesObj->races->maxTimeRemaining(1) < 0 ) {
        // let's put all the remaining players to status HTP
  echo "\n   This race is finished : time exceeded the Winners's PCT time !\n";
  foreach ( $fullRacesObj->opponents as $usersObj ) {
     echo "   Setting user ".$usersObj->idusers . " HTP... ";
    
           //need to get fullUsers Object
           $fullUsersObj = new fullUsers($usersObj->idusers);
     $fullUsersObj->setHTP();
     echo " done !\n";

  }
        // let's close the race
  $fullRacesObj->stopRaces();
   } 

   // Race not finished, let's do the job for each user
   else {
     foreach ( $fullRacesObj->opponents as $usersObj) {

        if (    ( $USER_NUM !=0 && $usersObj->idusers == $USER_NUM  )
             || ( $USER_NUM == 0)   ) {

             // Check only the race given in first arg if one is given, else check all races
       //echo "\n==>" . $usersObj->idusers;
             
             // Cas d'un joueur VLM
             if ( $usersObj->idusers > 0 ) {
            include "check_user.php";
            $nb_boats++;
             } else {
                  // Cas d'un bateau réel
                  $fullUsersObj = new fullUsers($usersObj->idusers);
                  $fullUsersObj->writeCurrentRanking();
             }

        }

      } // Foreach opponent
   } // If race finished

   $now2 = 1 + time();
   echo "\n*** *** Checking duration for race ". $fullRacesObj->races->idraces . " was " . ($now2 - $now) . " seconds";
   echo "\n*** *** Engine end for this race : " . ($nb_boats - $nboatinrace) . " boats => " .  round(($nb_boats - $nboatinrace )/($now2 - $now),2) . " boats/seconds\n";
   echo "****************************************************************************\n";
?>
