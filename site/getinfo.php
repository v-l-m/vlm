<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");


  $idu=htmlentities(quote_smart($_REQUEST['idu']));
  if (  round($idu) == 0 ) {
     echo "usage : http://virtual-loup-de-mer.org/getinfo.php?idu=X&pseudo=xxxx&password=pppp\n";
     echo "\nX = numero de votre bateau";
     echo "\nxxxxx = votre nom d'utilisateur";
     echo "\nppppp = votre mot de passe";
     echo "\nVariables = \n
    #* WPL : liste de Waypoints (liste)
    #* RAC : numéro de la course (string)
    #* IDB : nom du bateau (string)
    #* RAN : nom de la course (string)
    #* POS : classement dans la course (string - xxx/yyy)
    #* PIP : pilot parameter (string - doit le rester à causes des WP : x.xx,y.yy
    #* POL : nom de la polaire (sans boat_) (string)
    #* MCR : 'mapCenter' (string), ie centre de la carte
    #* MLY : 'mapLayers' (string), ie type de layers
    #* MOP : 'mapOpponents' (string), ie type d'affichage des concurrents
    #* MTL : 'mapTools' (string), ie 
    #* MPO : 'mapPrefOpponents' (liste), ie concurrents à suivre
    #* ETA : Date estimée d'arrivé, seulement si pas de wp perso (string)
    #* IDU : numéro de bateau (int)
    #* NWP : numéro du prochain waypoing (int)
    #* PIM : Pilot mode (int)
    #* NUP : nombre de secondes jusqu'à la prochaine VAC (int)
    #* VAC : nombre de secondes entre deux vacations (int)
    #* MWD : 'mapX' (int), ie taille largeur en pixel
    #* MHT : 'mapY' (int), ie taille hauteur en pixel
    #* MAG : 'mapAge' (int), ie age des trajectoires
    #* MAR : 'maparea' (int), ie taille de la carte
    #* MES : 'mapEstime' (int), ie estime
    #* MGD : 'mapMaille' (int), ie taille de la grid de vent
    #* BSP : vitesse du bateau (Boat SPeed) (float)
    #* HDG : direction (HeaDinG)
    #* DNM : Distance to next mark (float)
    #* ORT : Cap ortho to next mark (float)
    #* LOX : Cap loxo to next mark (float)
    #* VMG : VMG (float)
    #* TWD : Wind direction (float)
    #* TWS : Wind speed (float)
    #* TWA : Wind angle - Allure (float)
    #* LOC : loch (float)
    #* AVG : vitesse moyenne (float)
    #* WPLAT : latitude du wp perso (float, en degré)
    #* WPLON : longitude du wp perso (float, en degré)
    #* H@WP : mode Heading@WP, (float, degré)
    #* LAT : latitude (float, degré)
    #* LON : longitude (float, degré)
    #* TUP : Time to Update (à partir de NUP) (int)
    #* TFS : Time From Start (int)
    #* RNK : Rank : classement dans la course (int)
    #* NBS : Number of Boat subscribed (int)
    #* NPD : Notepad (blocnote)
    #* EML : EMail
    #* COL : Color
    #* CNT : Country 
    #* SRV : Servername 
    #* PIL1: Pilototo instruction 1 (id,time,PIM,PIP,status)
    #* PIL2: Pilototo instruction 2 (id,time,PIM,PIP,status)
    #* PIL3: Pilototo instruction 3 (id,time,PIM,PIP,status)
    #* PIL4: Pilototo instruction 4 (id,time,PIM,PIP,status)
    #* PIL5: Pilototo instruction 5 (id,time,PIM,PIP,status)
    #* THM: nom du theme
    #* HID: trace cachée (1) ou visible (0)
    #* VAC: durée de la vacation (en secondes)
    ";
    exit;
  }

  $pseudo=quote_smart($_REQUEST['pseudo']);
  $password=quote_smart($_REQUEST['password']);

  // Les clients Linux modernes utilisent l'UTF-8, alors que les clients Windows subsistent en IDO88591
  // On teste donc checkaccount en conversion ISO8859-1   *ET*  en UTF-8
  if ( checkAccount(htmlentities($pseudo,ENT_COMPAT)        , htmlentities($password, ENT_COMPAT)) != $idu 
    && checkAccount(htmlentities($pseudo,ENT_COMPAT,"UTF-8"), htmlentities($password, ENT_COMPAT,"UTF-8")) != $idu  ) {
    echo "You should not do that.";
    exit;
  }

  $usersObj = new fullUsers($idu);
  printf ("IDU=%d\n", $usersObj->users->idusers) ;
  printf ("IDB=%s\n", $usersObj->users->boatname) ;
  printf ("EML=%s\n", $usersObj->users->email) ;
  printf ("COL=%s\n", $usersObj->users->color) ;
  printf ("CNT=%s\n", $usersObj->users->country) ;
  printf ("POL=%s\n", $usersObj->users->boattype) ;

  if ( $usersObj->users->engaged == 0 ) {

     // Race is 0
     echo "RAC=0\nno-data:not engaged on any race";

  } else {

     printf ("RAC=%d\n", $usersObj->users->engaged) ;
     $racesObj = new races($usersObj->users->engaged);
     printf ("RAN=%s\n", $racesObj->racename) ;
     printf ("LAT=%s\n", $usersObj->lastPositions->lat) ;
     printf ("LON=%s\n", $usersObj->lastPositions->long) ;
     printf ("BSP=%2.2f\n", round($usersObj->boatspeed, 2));
     printf ("HDG=%05.2f\n" , $usersObj->users->boatheading );
     printf ("NWP=%02d\n" , $usersObj->users->nwp );
     printf ("DNM=%4.2f\n", round($usersObj->distancefromend,2)) ;
     printf ("ORT=%03.1f\n" , $usersObj->orthoangletoend );
     printf ("LOX=%03.1f\n" , $usersObj->loxoangletoend ) ;
     printf ("VMG=%2.2f\n", round($usersObj->VMGortho, 2));

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
     printf ("ETA=%s\n", $ETA);
     $twd=$usersObj->wheading + 180; 
     while ( $twd > 360 ) { $twd-=360; }
     printf ("TWD=%05.2f\n" , round($twd,2) ) ;

     printf ("TWS=%5.2f\n" , $usersObj->wspeed ) ;

     // Calcul du TWA signé
     $twa=round($usersObj->boatanglewithwind,2);
     printf ("TWA=%5.2f\n" , $twa );

     printf ("PIM=%d\n" , $usersObj->users->pilotmode );
     switch ( $usersObj->users->pilotmode ) {
        case 1: 
          printf ("PIP=%5.2f\n", $usersObj->users->boatheading );
          break;
        case 2:
          printf ("PIP=%5.2f\n", $usersObj->users->pilotparameter );
          break;
        case 3:
        case 4:
          if ( $usersObj->users->targetlat == 0 && $usersObj->users->targetlong == 0 ) {
              printf ("PIP=%5.4f,%5.4f\n", $usersObj->users->LatNM, $usersObj->users->LonNM );
          } else {
              printf ("PIP=%5.4f,%5.4f@%d\n", $usersObj->users->targetlat, $usersObj->users->targetlong, $usersObj->users->targetandhdg );
          }
     }
     printf ("POS=%s\n" , getCurrentRanking($idu, $usersObj->users->engaged) );
     printf ("LOC=%s\n" , $usersObj->users->loch);
     printf ("LUP=%d\n" , $usersObj->users->lastupdate ) ;
     printf ("NUP=%d\n" , 10 * round($usersObj->users->lastupdate + 60*$racesObj->vacfreq - time())/10);
     printf ("AVG=%02.1f\n", 3600*$usersObj->users->loch/(time() - $usersObj->users->userdeptime));
     printf ("WPLAT=%s\n", $usersObj->users->targetlat) ;
     printf ("WPLON=%s\n", $usersObj->users->targetlong) ;
     printf ("H@WP=%s\n", $usersObj->users->targetandhdg) ;

     // Map Preferences
     $mapvar = array (
            'mapAge' => 'MAG',
            'maparea' => 'MAR',
            'mapCenter' => 'MCR',
            'mapEstime' => 'MES',
            'mapLayers' => 'MLY',
            'mapMaille' => 'MGD',
            'mapOpponents' => 'MOP',
            'mapPrefOpponents' => 'MPO',
            'mapTools' => 'MTL',
            'mapX' => 'MWD',
            'mapY' => 'MHT',
     );
     $prefs=listUserPref($idu);
     while ( $pref = current($prefs)  ) { 
  echo $mapvar[key($prefs)] . "=" . $pref . "\n"; 
        next($prefs);
     }
     
     printf ("SRV=%s\n", SERVER_NAME ) ;



     printf ("NPD=\"%s\"\n", $usersObj->users->blocnote) ; //encloser pour le blocnote

     // Pilototo data
     $rc=$usersObj->users->pilototoList();
     $numligne=1;
     foreach ($usersObj->users->pilototo as $pilototo_row) { /*(id,time,PIM,PIP,status)*/
          printf("PIL%d=%d,%d,%d,%s,%s\n",$numligne,$pilototo_row[0],$pilototo_row[1],$pilototo_row[2],$pilototo_row[3],$pilototo_row[4]);
          $numligne++;
     }
     while($numligne<=5)
     {
          printf("PIL%d=none\n",$numligne);
          $numligne++;
     }
     printf ("THM=%s\n", $usersObj->users->theme) ;
     printf ("HID=%d\n", $usersObj->users->hidepos) ;
     // vacfreq est en minutes dans la base, mais affiché en secondes
     printf ("VAC=%d\n" , 60 * $racesObj->vacfreq );
  }
?>
  
