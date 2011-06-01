<?php
include_once("config.php");

header("content-type: text/plain; charset=UTF-8");

function get_output_format() {
  $qjson = 0;
  $sjson = false;
  $qplain = 0.1;
  $splain = false;
  
  if (isset($_REQUEST['forcefmt'])) {
      $fmt = $_REQUEST['forcefmt'];
      if (in_array($fmt, Array('json', 'text'))) {
          return $fmt;
      }
      //else, we still try to autodetect...
  }

  if (preg_match(',application/json(;q=(d?.d+))?,i', 
     $_SERVER['HTTP_ACCEPT'], $res)) {
    $qjson = (isset($res[2]))? $res[2] : 1;
    $sjson = true;
  }
  
  if (preg_match(',text/plain(;q=(d?.d+))?,i', 
    $_SERVER['HTTP_ACCEPT'], $res)) {
    $qplain = (isset($res[2]))? $res[2] : 1;
    $splain = true;
  } else if (preg_match(',text/\*(;q=(d?.d+))?,i', 
      $_SERVER['HTTP_ACCEPT'], $res)) {
    $qplain = (isset($res[2]))? $res[2] : 1;
    $splain = true;
  }
  if ($qplain > $qjson) {
    return "text";
  } else if ($qplain < $qjson) {
    return "json";
  }
  if ( $splain == $sjson) {
    // both there, take the first one :)
    $jsonpos = stripos($_SERVER['HTTP_ACCEPT'], "text/json");
    $plainpos = stripos($_SERVER['HTTP_ACCEPT'], "text/plain");
    if ($plainpos === false) {
      $plainpos = stripos($_SERVER['HTTP_ACCEPT'], "text/*");  
    }
    if ($plainpos > $jsonpos) {
      return "json";
    } 
  }
  return "text";
}

function get_info_array($idu) {
  $info = array();
  $userObj = new fullUsers($idu);

  $info['IDU'] = $userObj->users->idusers;
  $info['IDB'] = $userObj->users->boatname;
  $info['EML'] = $userObj->users->email;
  $info['COL'] = $userObj->users->color;
  $info['CNT'] = $userObj->users->country;
  $info['POL'] = $userObj->users->boattype;

  if ( $userObj->users->engaged == 0 ) {
    // Race is 0
    $info['RAC'] = "0";
  } else {
    $info['RAC'] = $userObj->users->engaged;
    $racesObj = &$userObj->races;
    $info['RAN'] = $racesObj->racename;
    $info['LAT'] = $userObj->lastPositions->lat;
    $info['LON'] = $userObj->lastPositions->long;
    $info['BSP'] = round($userObj->boatspeed, 2);
    $info['HDG'] = round($userObj->users->boatheading, 2);
    $info['NWP'] = $userObj->users->nwp;
    $info['DNM'] = round($userObj->distancefromend, 2);
    $info['ORT'] = round($userObj->orthoangletoend, 2);
    $info['LOX'] = round($userObj->loxoangletoend, 2);
    $info['VMG'] = round($userObj->VMGortho, 2);
    
    if ( $userObj->VMGortho != 0 ) {
      $_timetogo = 3600 * $userObj->distancefromend / $userObj->VMGortho;
      if ( $_timetogo < 0 ) {
  $info['ETA'] = -1;
      } else {
  $info['ETA'] = sprintf(gmdate('Y-m-d\TH:i:s\Z', time() + $_timetogo )); 
      }
    } else {
      $info['ETA'] = -1;
    }
    $twd=fmod($userObj->wheading+3780, 360); 
    $info['TWD'] = round($twd, 2);
    $info['TWS'] = round($userObj->wspeed, 2);
    
    // Calcul du TWA signé
    $twa=round($userObj->boatanglewithwind,2);
    $info['TWA'] = $twa;
    $info['PIM'] = $userObj->users->pilotmode;
    switch ( $userObj->users->pilotmode ) {
    case 1: 
      $info['PIP'] = round($userObj->users->boatheading, 2);
      break;
    case 2:
      $info['PIP'] = round($userObj->users->pilotparameter, 2);
      break;
    case 3:
    case 4:
    case 5:
      if ( $userObj->users->targetlat == 0 && 
     $userObj->users->targetlong == 0 ) {
  $info['PIP'] = sprintf ("%5.4f,%5.4f", 
        $userObj->users->LatNM, 
        $userObj->users->LonNM );
      } else {
  $info['PIP'] = sprintf ("%5.4f,%5.4f@%d", 
        $userObj->users->targetlat, 
        $userObj->users->targetlong, 
        $userObj->users->targetandhdg );
      }
    }
    $info['POS'] = $userObj->getCurrentRanking();
    $info['LOC'] = $userObj->users->loch;
    $info['LUP'] = $userObj->users->lastupdate;
    $info['NUP'] = 10*round($userObj->users->lastupdate + 
          60*$racesObj->vacfreq - time())/10;
    $info['VAC'] =  60*$racesObj->vacfreq;

    $info['AVG'] = round(3600*$userObj->users->loch / 
       (time() - $userObj->users->userdeptime), 2);
    
    $info['WPLAT'] = $userObj->users->targetlat;
    $info['WPLON'] = $userObj->users->targetlong;
    $info['H@WP'] = $userObj->users->targetandhdg;

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
         'mapDrawtextwp' => 'MDT'
         );
    $prefs=listUserPref($idu);
    while ( $pref = current($prefs) ) { 
      $info[$mapvar[key($prefs)]] = $pref;
      next($prefs);
    }
    $info['SRV'] = SERVER_NAME;
    $info['NPD'] = sprintf ("\"%s\"", $userObj->users->blocnote);

    // Pilototo data
    $rc=$userObj->users->pilototoList();
    $numligne=1;
    foreach ($userObj->users->pilototo as $pilototo_row) {
      /*(id,time,PIM,PIP,status)*/
      $p_key = sprintf("PIL%d", $numligne);
      $info[$p_key] = sprintf("%d,%d,%d,%s,%s", $pilototo_row['TID'],
                              $pilototo_row['TTS'], $pilototo_row['PIM'],
                              $pilototo_row['PIP'], $pilototo_row['STS']);
      $numligne++;
    }
    while($numligne<=PILOTOTO_MAX_EVENTS) {
      $p_key = sprintf("PIL%d", $numligne);
      $info[$p_key] = "none";
      $numligne++;
    }

    $info['THM'] = $userObj->users->theme;
    $info['HID'] = $userObj->users->hidepos;

    return $info;
  }
}

function usage() {
  echo "usage : http://virtual-loup-de-mer.org/getinfo.php?idu=X&".
       "pseudo=xxxx&password=pppp\n";
  echo "\nX = numero de votre bateau";
  echo "\nxxxxx = votre nom d'utilisateur";
  echo "\nppppp = votre mot de passe";
  echo "\nVariables = \n
    #* WPL : liste de Waypoints (liste)
    #* RAC : numéro de la course (string)
    #* IDB : nom du bateau (string)
    #* RAN : nom de la course (string)
    #* POS : classement dans la course (string - xxx/yyy)
    #* PIP : pilot parameter (string - doit le rester à causes des WP: x.xx,y.yy
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
    #* MWD : 'mapX' (int), ie taille largeur en pixel
    #* MHT : 'mapY' (int), ie taille hauteur en pixel
    #* MAG : 'mapAge' (int), ie age des trajectoires
    #* MAR : 'maparea' (int), ie taille de la carte
    #* MES : 'mapEstime' (int), ie estime
    #* MGD : 'mapMaille' (int), ie taille de la grid de vent
    #* MDT : 'mapDrawtextwp' (string) on/off
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
}

// now start the real work

$idu=htmlentities(quote_smart($_REQUEST['idu']));
if (round($idu) == 0) {
  header("content-type: text/plain; charset=UTF-8");
  usage();
  exit;
}

$fmt = get_output_format();
switch ($fmt) {
case "json":
  header("content-type: text/plain; charset=UTF-8");
  break;
case "text":
default:
  header("content-type: text/plain; charset=UTF-8");
}

$pseudo=quote_smart($_REQUEST['pseudo']);
$password=quote_smart($_REQUEST['password']);

// Les clients Linux modernes utilisent l'UTF-8, alors que les clients Windows subsistent en IDO88591
// On teste donc checkaccount en conversion ISO8859-1   *ET*  en UTF-8
if ( checkAccount(htmlentities($pseudo,ENT_COMPAT), 
      htmlentities($password, ENT_COMPAT)) != $idu 
     && checkAccount(htmlentities($pseudo,ENT_COMPAT,"UTF-8"),
         htmlentities($password, ENT_COMPAT,"UTF-8")) != $idu  ) {
  switch ($fmt) {
  case "json":
    break;
  case "text":
  default:
    echo "Not allowed\n";
    usage();
  }
  exit;
}


function ia_print($value, $key) {
  echo $key."=".$value."\n";
}

$info_array = get_info_array($idu);

switch ($fmt) {
case "json":
  echo json_encode($info_array);
  break;
case "text":
default:
  array_walk($info_array, 'ia_print');
}

?>
