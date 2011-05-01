<?php
include_once("wslib.php");
require_once("users.class.php");

header("content-type: text/plain; charset=UTF-8");

function get_output_format() {
  return get_requested_output_format();
}

function get_info_array($idu) {
  $info     = array();
  $userObj  = new fullUsers($idu);
  $ownerId  = intval($userObj->users->getOwnerId());
  $ownerObj = ($ownerId != 0) ? getPlayerObject($ownerId) : NULL;

  $info['IDU'] = $userObj->users->idusers;
  $info['IDP'] = $ownerId;
  $info['IDB'] = $userObj->users->boatname;
  $info['EML'] = $userObj->users->email;
  $info['COL'] = $userObj->users->color;
  $info['CNT'] = $userObj->users->country;
  $info['POL'] = $userObj->users->boattype;

  if ($ownerObj != NULL) {
    $info['OWN'] = $ownerObj->playername;
  }
  
  if ( $userObj->users->engaged == 0 ) {
    // Race is 0
    $info['RAC'] = "0";
  } else {
    $info['RAC'] = $userObj->users->engaged;
    $racesObj = &$userObj->races;
    $info['RAN'] = $racesObj->racename;
    $info['LAT'] = (float)$userObj->lastPositions->lat;
    $info['LON'] = (float)$userObj->lastPositions->long;
    $info['BSP'] = round($userObj->boatspeed, 2);
    $info['HDG'] = round($userObj->users->boatheading, 2);
    $info['NWP'] = $userObj->users->nwp;
    $info['DNM'] = round($userObj->distancefromend, 2);
    $info['ORT'] = round($userObj->orthoangletoend, 2);
    $info['LOX'] = round($userObj->loxoangletoend, 2);
    $info['VMG'] = round($userObj->VMGortho, 2);
    $info['STS'] = (int)$racesObj->started;
    
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
    $info['NOW'] = time();
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
        if ( $userObj->users->targetlat == 0 && $userObj->users->targetlong == 0 ) {
            $info['PIP'] = sprintf ("%5.4f,%5.4f", 
            $userObj->LatNM, 
            $userObj->LongNM );
        } else {
            $info['PIP'] = sprintf ("%5.4f,%5.4f@%d", 
            $userObj->users->targetlat, 
            $userObj->users->targetlong, 
            $userObj->users->targetandhdg );
        }
    }
    $rnkinfo = $userObj->getCurrentUserRanking();
    $info['POS'] = $rnkinfo['rankracing']."/".$rnkinfo['nbu'];
    $info['RNK'] = $rnkinfo['rank'];
    $info['LOC'] = (float)$userObj->users->loch;
    $info['LUP'] = $userObj->users->lastupdate;
    $info['NUP'] = 10*round($userObj->users->lastupdate + 
                   60*$racesObj->vacfreq - time())/10;
    $info['VAC'] =  60*$racesObj->vacfreq;

    $info['AVG'] = round(3600*$userObj->users->loch / 
                   (time() - $userObj->users->userdeptime), 2);
    
    $info['WPLAT'] = $userObj->users->targetlat;
    $info['WPLON'] = $userObj->users->targetlong;
    $info['H@WP'] = $userObj->users->targetandhdg;
    if ( time() > $userObj->users->releasetime ) {
        $info['S&G'] = 0;
    } else {
        $info['S&G'] = intval($userObj->users->releasetime);
    }

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
    $prefs=listUserPref($idu, "map");
    foreach($prefs as $k => $v) {
        $info[$mapvar[$k]] = $v;
    }
    $info['SRV'] = SERVER_NAME;
    $info['NPD'] = sprintf ("\"%s\"", $userObj->users->blocnote);

    $rc=$userObj->users->pilototoList();
    // Pilototo data // old way - should be deleted in v0.14
    $numligne=1;
    foreach ($userObj->users->pilototo as $pilototo_row) {
      /*(id,time,PIM,PIP,status)*/
      $p_key = sprintf("PIL%d", $numligne);
      $info[$p_key] = sprintf("%d,%d,%d,%s,%s", $pilototo_row['TID'],
                              $pilototo_row['TTS'], $pilototo_row['PIM'],
                              $pilototo_row['PIP'], $pilototo_row['STS']);
      $numligne++;
    }
    while($numligne <= PILOTOTO_MAX_EVENTS) {
      $p_key = sprintf("PIL%d", $numligne);
      $info[$p_key] = "none";
      $numligne++;
    }
    
    // Pilototo data // new way
    $info['PIL'] = $userObj->users->pilototo;

    $info['THM'] = $userObj->users->theme;
    $info['HID'] = $userObj->users->hasTrackHidden();
  }
  return $info;
}

function usage() {
  $usage = "usage : http://virtual-loup-de-mer.org/ws/boatinfo.php\n";
  $usage .= "l'acces utilise l'authentification HTTP";
  $usage .= "\nlogin = votre nom d'utilisateur";
  $usage .= "\npassword = votre mot de passe";
  $usage .= "\n\nLe mode txt est conservé uniquement pour des raisons historiques ";
  $usage .= "et pourra disparaitre dans les version ultérieures. Merci d'utiliser ";
  $usage .= "le format json, via Accept: application/json ou via ?forcefmt=json";
  $usage .= "\n\nVariables = \n
    #* AVG : vitesse moyenne (float)
    #* BSP : vitesse du bateau (Boat SPeed) (float)
    #* CNT : Country 
    #* COL : Color
    #* DNM : Distance to next mark (float)
    #* EML : EMail
    #* ETA : Date estimée d'arrivé, seulement si pas de wp perso (string)
    #* HDG : direction (HeaDinG)
    #* HID: trace cachée (1) ou visible (0)
    #* H@WP : mode Heading@WP, (float, degré)
    #* IDB : nom du bateau (string)
    #* IDP : Id player
    #* IDU : numéro de bateau (int)
    #* LAT : latitude (float, degré)
    #* LOC : loch (float)
    #* LON : longitude (float, degré)
    #* LOX : Cap loxo to next mark (float)
    #* LUP: date de la vacation pour ce boat
    #* MAG : 'mapAge' (int), ie age des trajectoires
    #* MAR : 'maparea' (int), ie taille de la carte
    #* MCR : 'mapCenter' (string), ie centre de la carte
    #* MDT : 'mapDrawtextwp' (string) on/off
    #* MES : 'mapEstime' (int), ie estime
    #* MGD : 'mapMaille' (int), ie taille de la grid de vent
    #* MHT : 'mapY' (int), ie taille hauteur en pixel
    #* MLY : 'mapLayers' (string), ie type de layers
    #* MOP : 'mapOpponents' (string), ie type d'affichage des concurrents
    #* MPO : 'mapPrefOpponents' (liste), ie concurrents à suivre
    #* MTL : 'mapTools' (string), ie 
    #* MWD : 'mapX' (int), ie taille largeur en pixel
    #* NBS : Number of Boat subscribed (int)
    #* NPD : Notepad (blocnote)
    #* NUP : nombre de secondes jusqu'à la prochaine VAC (int)
    #* NWP : numéro du prochain waypoing (int)
    #* ORT : Cap ortho to next mark (float)
    #* OWN : Owner (Playername)
    #* PIL1: Pilototo instruction 1 (id,time,PIM,PIP,status)
    #* PIL2: Pilototo instruction 2 (id,time,PIM,PIP,status)
    #* PIL3: Pilototo instruction 3 (id,time,PIM,PIP,status)
    #* PIL4: Pilototo instruction 4 (id,time,PIM,PIP,status)
    #* PIL5: Pilototo instruction 5 (id,time,PIM,PIP,status)
    #* PIL : List of Piloto instruction (json only)
    #* PIM : Pilot mode (int)
    #* PIP : pilot parameter (string - doit le rester à causes des WP: x.xx,y.yy
    #* POL : nom de la polaire (sans boat_) (string)
    #* POS : classement dans la course (string - xxx/yyy)
    #* RAC : numéro de la course (string)
    #* RAN : nom de la course (string)
    #* RNK : Rank : classement dans la course (int)
    #* SRV : Servername 
    #* TFS : Time From Start (int)
    #* THM: nom du theme
    #* TUP : Time to Update (à partir de NUP) (int)
    #* TWA : Wind angle - Allure (float)
    #* TWD : Wind direction (float)
    #* TWS : Wind speed (float)
    #* VAC: durée de la vacation (en secondes)
    #* VMG : VMG (float)
    #* WPLAT : latitude du wp perso (float, en degré)
    #* WPL : liste de Waypoints (liste)
    #* WPLON : longitude du wp perso (float, en degré)
    ";
    return $usage;
}

function ia_print($value, $key) {
    $substkey = Array('error' => "ERR", "warning" => "WRN");
    if (is_array($value) ) {
        $value = "There is no support for Array() in text mode";
    }
    if (array_key_exists($key, $substkey)) $key = $substkey[$key];
    echo $key."=".$value."\n";
}

// now start the real work
login_if_not(usage());

$fmt = get_output_format();
//This should be wrapped in helper funcs
if (isPlayerLoggedIn() && is_null(get_cgi_var('select_idu'))) {
    //FIXME normalize error message (like in boatsetup)
    $info_array = Array('error' => 'select_idu is required as a GET parameter when using player login type');
} else {
    $info_array = get_info_array($_SESSION['idu']);
    if (!isPlayerLoggedIn()) {
        //FIXME : normalize warn message
        $info_array['warning'] = 'This type of authentification is deprecated and is not garanteed in the next version of VLM';
    }
}

switch ($fmt) {
case "json":
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($info_array);
    break;
case "text":
default:
    header("Content-Type: text/plain; charset=UTF-8");
    array_walk($info_array, 'ia_print');
}

?>

