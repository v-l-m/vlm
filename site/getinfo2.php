<?php
/*
  if (  round($idu) == 0 ) {
     echo "usage : http://virtual-loup-de-mer.org?idu=X&pseudo=xxxx&password=pppp\n";
     echo "\nX = numero de votre bateau";
     echo "\nxxxxx = votre nom d'utilisateur";
     echo "\nppppp = votre mot de passe";
     exit;
  }
*/
  include_once("config.php");

  $pseudo=quote_smart($_REQUEST['pseudo']);
  $password=quote_smart($_REQUEST['password']);
  $idu = checkAccount($pseudo, $password);
  if ( $idu ==  FALSE ) {
    echo "You should not do that.";
    exit;
  }

  $usersObj = new fullUsers($idu);
  printf ('IDU=%d;', $usersObj->users->idusers) ;
  printf ('IDB=%s;', $usersObj->users->boatname) ;
  if ( $usersObj->users->engaged == 0 ) {

     // Race is 0
     echo "RAC=0;no-data:not engaged on any race";

  } else {

     printf ('RAC=%d;', $usersObj->users->engaged) ;
     $racesObj = new races($usersObj->users->engaged);
     printf ('RAN=%s;', $racesObj->racename) ;

     printf ('LAT=%s;', $usersObj->lastPositions->lat) ;
     printf ('LON=%s;', $usersObj->lastPositions->long) ;
     printf ('BSP=%2.2f;', round($usersObj->boatspeed, 2));
     printf ('HDG=%05.2f;' , $usersObj->users->boatheading );
     printf ('NWP=%02d;' , $usersObj->users->nwp );
     printf ('DNM=%4.2f;', round($usersObj->distancefromend,2)) ;
     printf ('ORT=%03.1f;' , $usersObj->orthoangletoend );
     printf ('LOX=%03.1f;' , $usersObj->loxoangletoend ) ;
     printf ('VMG=%2.2f;', round($usersObj->VMGortho, 2));
     if ( $usersObj->VMGortho != 0 ) {
         $_timetogo=60 * 60 * $usersObj->distancefromend / $usersObj->VMGortho;
         if ( $_timetogo < 0 ) {
            $ETA=-1;
         } else {
            $ETA=sprintf(gmdate('Y-m-d H:i:s', time() + $_timetogo )); 
         }
     } else {
         $ETA=-1;
     }
     printf ("ETA=%s;", $ETA);
     printf ('TWD=%05.2f;' , ($usersObj->wheading + 180)%360 ) ;
     printf ('TWS=%5.2f;' , $usersObj->wspeed ) ;
     printf ('TWA=%5.2f;' , intval($usersObj->boatanglewithwind) );
     printf ('PIM=%d;' , $usersObj->users->pilotmode );
     printf ('PIP=%5.2f;' , $usersObj->users->pilotparameter );
     printf ('POS=%s;' , getCurrentRanking($idu, $usersObj->users->engaged) );
     printf ('LOC=%s;' , $usersObj->users->loch);
     printf ('NUP=%d;' , 10 * round($usersObj->users->lastupdate + DELAYBETWEENUPDATE - time())/10);
     
     // Pilototo data
     $rc=$usersObj->users->pilototoList();
     $numligne=1;
     foreach ($usersObj->users->pilototo as $pilototo_row) { /*(id,time,PIM,PIP,status)*/
          printf("PIL%d=%d,%d,%d,%s,%s;",$numligne,$pilototo_row[0],$pilototo_row[1],$pilototo_row[2],$pilototo_row[3],$pilototo_row[4]);
          $numligne++;
     }
     while($numligne<=5)
     {
          printf("PIL%d=none;",$numligne);
          $numligne++;
     }
  
  }
?>
  
