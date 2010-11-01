<?php
/********Functions*********/

include_once("vlmc.php");
require_once('players.class.php');

function wrapper_mysql_map_db_query_reader($cmd) {
  if (defined('MOTEUR') && defined('TRACE_SQL_QUERIES')) {
    global $db_total_time;
    echo "*** DB ACCESS ".$cmd;
    $sql_start_time=microtime(1);
    $res = mysql_query($cmd, $GLOBALS['mapdblink']);
    $sql_end_time=microtime(1);
    echo " : ".($sql_end_time-$sql_start_time)."s\n";
    $db_total_time += ($sql_end_time-$sql_start_time);
    return $res;
  }
  return mysql_query($cmd, $GLOBALS['slavedblink']);
}

function wrapper_mysql_db_query_reader($cmd) {
  if (defined('MOTEUR') && defined('TRACE_SQL_QUERIES')) {
    global $db_total_time;
    echo "*** DB ACCESS ".$cmd;
    $sql_start_time=microtime(1);
    $res = mysql_query($cmd, $GLOBALS['slavedblink']);
    $sql_end_time=microtime(1);
    echo " : ".($sql_end_time-$sql_start_time)."s\n";
    $db_total_time += ($sql_end_time-$sql_start_time);
    return $res;
  }
  return mysql_query($cmd, $GLOBALS['slavedblink']);
}

function wrapper_mysql_db_query_writer($cmd) {
  if (defined('MOTEUR') && defined('TRACE_SQL_QUERIES')) {
    global $db_total_time;
    echo "*** DB ACCESS ".$cmd;
    $sql_start_time=microtime(1);
    $res = mysql_query($cmd, $GLOBALS['masterdblink']);
    $sql_end_time=microtime(1);
    echo " : ".($sql_end_time-$sql_start_time)."s\n";
    $db_total_time += ($sql_end_time-$sql_start_time);
    return $res;
  }
  return mysql_query($cmd, $GLOBALS['masterdblink']);
}

function wrapper_mysql_db_query($cmd) {
  return  wrapper_mysql_db_query_reader($cmd);
}

// Protege la variable
function quote_smart($value) {
  // Stripslashes if required
    static $magic_quotes_gpc = null;
    if ($magic_quotes_gpc === null) {
        $magic_quotes_gpc = get_magic_quotes_gpc();
    }

    if ($magic_quotes_gpc) {
        $value = stripslashes($value);
    }
    return $value;
}

function get_cgi_var($name, $default_value = null) {
    //From phpmyedit.org
    static $magic_quotes_gpc = null;
    if ($magic_quotes_gpc === null) {
        $magic_quotes_gpc = get_magic_quotes_gpc();
    }
    $var = @$_GET[$name];
    if (! isset($var)) {
        $var = @$_POST[$name];
    }
    if (isset($var)) {
        if ($magic_quotes_gpc) {
            if (is_array($var)) {
                foreach (array_keys($var) as $key) {
                    $var[$key] = stripslashes($var[$key]);
                }
            } else {
                $var = stripslashes($var);
            }
        }
    } else {
        $var = @$default_value;
    }
    return $var;
}


//     String $lang NavigatorLanguage()
//
// Return navigator language or en if not supported
//--------------------------------------------

function NavigatorLanguage($allowed = array("fr", "en", "pt", "it", "es")) {
    $lang = getenv("HTTP_ACCEPT_LANGUAGE");
    $lang = substr($lang,0,2);
    if (in_array($lang, $allowed)) {
        return $lang;
    } else  {
        return "en";
    }
}

function getCurrentLang() {
    static $lang = null;
    if (!is_null($lang)) return $lang;
    if (isset($_REQUEST['lang'])) {
        $lang = quote_smart($_REQUEST['lang']);
    } else if (isset($_SESSION['LANG'])) {
        $lang = $_SESSION['LANG'];
    } else {
        $lang = NavigatorLanguage();  
    }
    $_SESSION['LANG'] = $lang;
    return $lang;
}

function getLocalizedString($key, $lg = null) {
    static $stringarray = null;
    static $lang = null;
    if (is_null($stringarray)) {
        include($_SERVER['DOCUMENT_ROOT']."/includes/strings.inc");
        $lang = getCurrentLang();
        if (!array_key_exists($lang, $strings)) $lang = "en";
        $stringarray = $strings;
    }
    if (is_null($key)) {
        //Ugly, isn't it ?
        return array_keys($stringarray);
    }
    if (is_null($lg)) {
        $locallang = $lang;
    } else {
        $locallang = $lg;
    }
    if (array_key_exists($key, $stringarray[$locallang])) {
        return $stringarray[$locallang][$key];
    } else if ($locallang != "en" && array_key_exists($key, $stringarray["en"])) {
        if (DISPLAY_LANG_WARNINGS) {
            return "**'".$stringarray["en"][$key]."' (untranslated string)**";
        } else {
            return $stringarray["en"][$key];
        }
    } else {
        return "**'$key' (unknown string)**";
    }
}

/*return knots*/
function norm($x, $y)
{
  return ((sqrt(pow($x, 2) + pow($y, 2)))*MS2KNT);
}

/*from a cartesian vector (x,y), return the geographic angle between 0 and 359 */
function angle($x, $y)
{
  //$xknt = $x*MS2KNT;
  //$angle_trigo = rad2deg(acos ($xknt / norm ($x, $y)));
  $hyp=sqrt(pow($x, 2) + pow($y, 2));
  // Petite modif pour éviter les divisions/0 sur les zones sans vent.
  if ( $hyp == 0 ) $hyp=0.0001;
  $angle_trigo = rad2deg(acos($x/$hyp));
  if ($y < 0) {
    $angle_trigo = -$angle_trigo;
  }
  //echo "angle_trigo = $angle_trigo norm =".norm($x, $y)." xknt = $xknt angle_geographic =". trigo2geographic($angle_trigo)."\n";

  return trigo2geographic($angle_trigo);
}

/*from a trigonometric angle in degree, return an geographic angle in degree*/
function trigo2geographic($angle)
{
  if ($angle > 90) {
    return (450-$angle);
  }
  return (90-$angle);
}

function geographic2trigo($angle)
{
  $angle = -$angle - 90;
  if ($angle <= 0) $angle += 360;

  return $angle;
}

/*from a geographic angle return a drawing angle (turn same diretion but
  different origin*/
function geographic2drawing($angle)
{
  $angle = -$angle;

  if ($angle <= 0)
    $angle += 360;
  return $angle;
}

function  geographic2drawingforwind($angle)
{
  $angle = fmod($angle +90 , 360);
  return $angle;
}

function geographic2drawingforspeedchart($angle)
{
  $angle = fmod(180-$angle , 360);
  return $angle;
}

function longitudeConstraintDegrees($longitude) {
      while ($longitude <= -180) $longitude+=360;
      while ($longitude > 180)  $longitude-=360;
      return $longitude;
}


/*from an angle (degrees) and a norm, give the normed cartesian cordinates*/
function polar2cartesian($a, $r)
{
  //convert $a from geographic angle to trigonometric angle
  $a_trigo = (90.0-$a)*M_PI/180.0;

  $result[0] = $r*cos($a_trigo);
  $result[1] = $r*sin($a_trigo);
  //echo "\npolar2cartesian angle_trigo = $a_trigo, x = $result[0], y = $result[1] \n";
  return $result;
}

/*same function for the map, angle conversion function not the same, dont know why
  it is used to draw a triangle for the boat position and heading (map.php)*/
function polar2cartesianDrawing($a, $r)
{
  //convert $a from geographic angle to drawing angle
  $a = ($a-90.0)*M_PI/180.0;
  
  $result[0] = $r*cos($a);
  $result[1] = $r*sin($a);
  //echo "\npolar2cartesian angle_trigo = $a_trigo, x = $result[0], y = $result[1] \n";
  return $result;
}


function errorprint($message) {
  printf ("<h1>" . $message . "</h1>" );
}

/*display a string containing the difference between now and the last update*/
function lastUpdate()
{
  if (file_exists(CRONVLMLOCK)) {
    printf (getLocalizedString("processing") );
  } else {
    $query2 = "SELECT UNIX_TIMESTAMP(`time`) AS time,races,boats,duration,update_comment FROM updates ORDER BY `time` DESC LIMIT 1";
    $result2 = wrapper_mysql_db_query_reader($query2) or die("Query [$query2] failed \n");
    $row2 = mysql_fetch_assoc($result2);
    $lastupdate = $row2['time'];
    $races = $row2['races'];
    $boats = $row2['boats'];
    $duration = max($row2['duration'],0.001);
    $update_comment = $row2['update_comment'];
    $interval = time() - $lastupdate;

    $intervalarray = duration2string($interval);
    printf ( getLocalizedString("lastupdate"). " <br />\n",
             gmdate('H:i:s', time() ) . ' GMT', $intervalarray['hours'],$intervalarray['minutes'],$intervalarray['seconds'] );
    printf ("%.2f seconds (<span title=\"%s\">%d race(s)</span>, %d boat(s)), %2.2f boats/sec (<a target=\"_blank\" href=\"status/race-engine-status.php\" rel=\"nofollow\">status page</a>)", $duration, $update_comment, $races, $boats, $boats/$duration);
  }
}

/* Dernière IP de l'utilisateur */
function lastUserAction() {
    if ( isLoggedIn() ) {
        $query2 = "SELECT * FROM user_action WHERE idusers = ".getLoginId()." ORDER BY `time` DESC LIMIT 1";
        $result2 = wrapper_mysql_db_query_reader($query2) or die("Query [$query2] failed \n");
        $row2 = mysql_fetch_assoc($result2);
        return $row2;
    } else {
        return False;
    }
}

/* return the last known update for the local database (or master if master is true) */
function lastUpdateTime($master = false) {
    $query = "SELECT UNIX_TIMESTAMP(time) AS time FROM updates ORDER BY time DESC LIMIT 1";
    if ($master) {
        $result = wrapper_mysql_db_query_writer($query);
    } else {
        $result = wrapper_mysql_db_query_reader($query);
    }
    $row = mysql_fetch_assoc($result);
    return $row['time'];
}

/*display the next supposed update*/
function nextUpdate()
{
  $lastupdate = lastUpdateTime();
  $interval = time() - $lastupdate;
  //echo "interval = $interval et DELAYBETWEENUPDATE ".DELAYBETWEENUPDATE."\n";
  if ($interval > DELAYBETWEENUPDATE) //problems during update
    {
      printf("         ".getLocalizedString("noupdate")."\n");
    }
  else
    {
      $next = duration2string(DELAYBETWEENUPDATE - $interval);
      printf("      ".getLocalizedString("nextupdate"), $next['hours'], $next['minutes'] );
    }
}

/* from two geographics angles in degrees, compute the shortest angle between them */
function angleDifference($a, $b)
{
  while ( $a >= 360 ) $a-=360; 

  $b += 180;
  while ( $b >= 360 ) $b-=360; 
  
  $res = abs($a - $b);
  if ( $res > 180 ) $res=360-$res;

  //echo "AD=" . abs($a - $b);
  return $res;
}

/**
 * @input, lat, long (current pos, next mark), millidegrees
 * @return distance, in nm
 */
function ortho($lat, $long, $latnm, $longnm)  {
  if (($lat == $latnm) && ($long == $longnm)) {
    return 0.0;
  } 
  return VLM_ortho_distance($lat, $long, $latnm, $longnm);
}

/**
 * @input, lat, long (current pos, next mark), millidegrees
 * @return heading, in degrees
 */
function ortho_heading($lat, $long, $latnm, $longnm)  {
  if (($lat == $latnm) && ($long == $longnm)) {
    return 0.0;
  } 
  return VLM_ortho_heading($lat, $long, $latnm, $longnm);
}

/**
 * @input, lat, long (current pos, next mark), millidegrees
 * @return distance, in nm
 */
function loxo($lat, $long, $latnm, $longnm)  {
  if (($lat == $latnm) && ($long == $longnm)) {
    return 0.0;
  } 
  return VLM_loxo_distance($lat, $long, $latnm, $longnm);
}

/**
 * @input, lat, long (current pos, next mark), millidegrees
 * @return heading, in degrees
 */
function loxo_heading($lat, $long, $latnm, $longnm)  {
  if (($lat == $latnm) && ($long == $longnm)) {
    return 0.0;
  } 
  return VLM_loxo_heading($lat, $long, $latnm, $longnm);
}

// For a refpoint (long/lat) and a distance and a heading, give the end point 
// Used in "One point" waypoints
// Used in track projection
function giveEndPointCoordinates( $latitude, $longitude, $distance, $heading  )
{
  $lata = new doublep();
  $longa = new doublep();
  VLM_get_loxo_coord_from_dist_angle($latitude, $longitude, 
                                     $distance, $heading, 
                                     $lata, $longa);
  $EndLat = doublep_value($lata);
  $EndLong = doublep_value($longa);
  
  // We give back an array (Long/Lat)
  //printf ("DEBUG:EP=lat=%d, long=%d<BR>\n",$EndLat, $EndLong);
  return array ( 'latitude' => $EndLat, 'longitude' => $EndLong );
}

// =====================================================
// Function giveWaypointCoordinates(idraces, idwaypoint)
// =====================================================
// returns an array of 2 points (lat1,long1,lat2,long2) 
// beeing the coordinates in millidegrees of a waypoint
// ==> Attention, idwp is not a waypointid as in waypoints, 
//     but a waypointid as "wporder in races_waypoints".
// ========================================================
// Since june/2007 this function takes care of another parameter
// "laisser_au" : heading where the WP should be let
// If "laisser_au" is 90, then boats have to cross a "line"
// by sailing in the west of it (to see the WP at head 90° when "crossing")
// ========================================================
function internalGiveWaypointCoordinates($lat1, $long1, $lat2, $long2, $laisser_au, $wplength = WPLL) {
  // Cas d'un WP : long1=long2 && lat1=lat2
  if ( ( $lat1 == $lat2 ) && ( $long1 == $long2 ) && ( $laisser_au != 999 )  ) {
    // On a uniquement la bouee1
    // On doit calculer la position de la "bouee2" en fonction de long1, lat1, et "laisser_au"
    $gisement_bouee1_bouee2 = ($laisser_au+180)%360;

    $EndPoint=giveEndPointCoordinates($lat1,$long1, $wplength, $gisement_bouee1_bouee2);

    //printf ("WP=%d : Lat=%d, Lon=%d, Laisser=%d/gisement=%d, EPLong=%d, EPLat=%d<BR>\n", $idwp, $lat1, $long1, $laisser_au,$gisement_bouee1_bouee2, $EndPoint[0],$EndPoint[1]);

    return array ('latitude1' => $lat1, 'longitude1' => $long1, 
		  'latitude2' => $EndPoint['latitude'], 'longitude2' => $EndPoint['longitude'], 
		  'wptype' => WPTYPE_WP);

  } else {
    // Cas d'une porte : cas "historique"
    //printf ("PORTE=%d :  %d, %d, %d, %d<BR>\n", $idwp, $lat1, $long1, $lat2, $long2,$laisser_au);
    return array ( 'latitude1' => $lat1, 'longitude1' => $long1, 
		   'latitude2' => $lat2, 'longitude2' => $long2, 
		   'wptype' => WPTYPE_PORTE);
  }
}

function windspeedtocolorbeaufort($wspeed, $im)
{
  // <=F0 : blanc
  if ($wspeed <=  1.0) { return 0xFFFFFF; }
  // <=F1 : bleu clair legerement gris
  if ($wspeed <=  3.0) { return 0x9696E1; }
  // <=F2 : bleu un peu plus soutenu
  if ($wspeed <=  6.0) { return 0x508CCD; }
  // <=F3 : bleu plus foncé 
  if ($wspeed <= 10.0) { return 0x3C64B4; }
  // <=F4 : vert
  if ($wspeed <= 15.0) { return 0x41B464; }
  // <=F5 : jaune légèrement vert
  if ($wspeed <= 21.0) { return 0xB4CD0A; }
  // <=F6 : jaune orangé
  if ($wspeed <= 26.0) { return 0xD2D216; }
  // <=F7 : jaune orangé un peu plus rougeatre
  if ($wspeed <= 33.0) { return 0xE1D220; }
  // <=F8 : orange foncé
  if ($wspeed <= 40.0) { return 0xFFB300; }
  // <=F9 : rouge 
  if ($wspeed <= 47.0) { return 0xFF6F00; }
  // <=F10 rouge / marron
  if ($wspeed <= 55.0) { return 0xFF2B00; }
  // <=F11 marron
  if ($wspeed <= 63.0) { return 0xE60000; }
  // F12  rouge/noir
  return 0x7F0000;
}

function hexcolor($image,$string)
{
  sscanf($string, "%2x%2x%2x", $red, $green, $blue);
  return ImageColorAllocate($image,$red,$green,$blue);
}

function OLDwindspeedtocolorbeaufort($windspeed, $im)
{
  if      ($windspeed == 0) $colo = hexcolor($im, "9600fe"); 
  else if ($windspeed <= 2) $colo = hexcolor($im, "6400fe"); 
  else if ($windspeed <= 4) $colo = hexcolor($im, "3200fe"); 
  else if ($windspeed <= 6) $colo = hexcolor($im, "0032fe"); 
  else if ($windspeed <= 8) $colo = hexcolor($im, "0064fe"); 
  else if ($windspeed <=10) $colo = hexcolor($im, "0096fe"); 
  else if ($windspeed <=12) $colo = hexcolor($im, "00c8fe"); 
  else if ($windspeed <=14) $colo = hexcolor($im, "00e6f0"); 
  else if ($windspeed <=16) $colo = hexcolor($im, "00e6a0"); 
  else if ($windspeed <=18) $colo = hexcolor($im, "00e678"); 
  else if ($windspeed <=20) $colo = hexcolor($im, "00e650"); 
  else if ($windspeed <=22) $colo = hexcolor($im, "00f028"); 
  else if ($windspeed <=24) $colo = hexcolor($im, "00fa00"); 
  else if ($windspeed <=26) $colo = hexcolor($im, "fefe00"); 
  else if ($windspeed <=28) $colo = hexcolor($im, "fee100"); 
  else if ($windspeed <=30) $colo = hexcolor($im, "fec800"); 
  else if ($windspeed <=32) $colo = hexcolor($im, "feaf00"); 
  else if ($windspeed <=34) $colo = hexcolor($im, "fe9600"); 
  else if ($windspeed <=36) $colo = hexcolor($im, "e67d00"); 
  else if ($windspeed <=38) $colo = hexcolor($im, "e66400"); 
  else if ($windspeed <=40) $colo = hexcolor($im, "dc4b1e"); 
  else if ($windspeed <=42) $colo = hexcolor($im, "c8321e"); 
  else if ($windspeed <=44) $colo = hexcolor($im, "b4191e"); 
  else if ($windspeed <=46) $colo = hexcolor($im, "aa001e"); 
  else if ($windspeed <=48) $colo = hexcolor($im, "b40032"); 
  else if ($windspeed <=50) $colo = hexcolor($im, "a00032"); 
  else if ($windspeed <=52) $colo = hexcolor($im, "900032"); 
  else if ($windspeed <=54) $colo = hexcolor($im, "800016"); 
  else if ($windspeed <=56) $colo = hexcolor($im, "600016"); 
  else if ($windspeed <=58) $colo = hexcolor($im, "400016"); 
  else if ($windspeed <=60) $colo = hexcolor($im, "200008"); 
  else                      $colo = hexcolor($im, "000000");

  return $colo;
}


/* convert a windspeed to a number in pixels between 1 and 2*base approx. */
function windSpeed2Length($windspeed, $base = 4)
{
  $length = 0;
  if ($windspeed !=0)
    $length = round(1+$base*log($windspeed));
  return $length;
}

/*find boat speed with the angle and the windspeed
  proceed by double linear interpolation : one for the angle, one for the windspeed*/
function findboatspeed ($angledifference, $windspeed, $boattype )
{
  if (defined('MOTEUR')) {
    $boatSpeed = VLM_find_boat_speed($boattype, $windspeed, $angledifference);
  } else {
    $temp_vlmc_context = new vlmc_context();
    shm_lock_sem_construct_polar_context($temp_vlmc_context, 1);  
    $boatSpeed = VLM_find_boat_speed_context($temp_vlmc_context, $boattype, 
					     $windspeed, $angledifference);
    shm_unlock_sem_destroy_polar_context($temp_vlmc_context, 1);
  } 
  return $boatSpeed;
}


/*in the grib file, collect the date (like 04 09 17 00 00 = 23rd sept
  midnight GMT) and the forecast interval (00 for now data, 0c for 12 hours)
  return an unix timestamp
*/
function addforecast2date($grib, $datestart)
{
  //'08/20/2004 00:47'
  //echo bin2hex($grib);
  $string .= "200".hexdec(substr(bin2hex($grib), $datestart, 2)); //year
  $string .= "/";
  $string .= hexdec(substr(bin2hex($grib), $datestart + 2, 2)); //month
  $string .= "/";
  $string .= hexdec(substr(bin2hex($grib), $datestart + 4, 2));//day
  $string .= " ";
  $string .= hexdec(substr(bin2hex($grib), $datestart + 6, 2)); //hour
  $string .= ":00:00"; //minutes and seconds, we dont care
  $timestp = strtotime($string);
  $forecast = hexdec(substr(bin2hex($grib), $datestart + 12, 2));
  $timestp = $timestp + $forecast * 3600;
  return $timestp;
}


/*from two points (4 coordinates),
  compute line equation
  Si (Xa = Xb), alors : m = Xa, 
  Si (Ya = Yb), alors : p = Ya
  Sinon,  pas // à axe des ordonnées (Xa != Xb) , ni des abscisses : y = mx + p (m = meridien, p = parallèle)

  return m and p in a vector*/
function linear($Xa, $Ya, $Xb, $Yb)
{
  //echo "Calling linear with $Xa, $Ya, $Xb, $Yb\n";
  //if ( $Xa == $Xb ) {
  //  $m = $Ya;
  //  $p = 0;
  //} else if ( $Ya == $Yb ) {
  //  
  //  } else {
  $m = ($Yb-$Ya)/($Xb-$Xa);
  //applies in a
  $p = $Ya - $m*$Xa;
  //}
  
  return array($m, $p);
}



/*from a nuber of seconds, return the duration in days, hours, minutes and seconds*/
function duration2string($dur)
{
  $timeneg = ($dur < 0);
  if ($timeneg) {
      $dur = -$dur;
      $prefix = "-";
  } else {
      $prefix = "";
  }
  $days = floor($dur/86400);
  $hours = floor(($dur - $days*86400)/3600);
  $minutes = floor(($dur - $days*86400 - $hours*3600)/60);
  $seconds = $dur - $days*86400 - $hours*3600 - $minutes*60;
  return array('days' => $days, 'hours' => $hours,
	       'minutes' => $minutes, 'seconds' => $seconds, 'isneg' => $timeneg, 'prefix' => $prefix);
}

/*function VMG from a boat position, a boat destination and the speed
  vector, compute the VMG norm projection of the speed vector on the
  destination vector*/
function VMG($long, $lat, $longFin, $latFin, $boatheading, $boatspeed, $verbose)
{
  //echo "caling VMG with$long, $lat, $longFin, $latFin, $boatheading, $boatspeed ";
  // Passage AM vers l'est
  if ( $longFin < 0 and $long > 0 and abs($longFin) > 90000 and abs($long) > 90000 )  $longFin+=360000;

  // Passage AM vers l'ouest
  if ( $longFin > 0 and $long < 0 and abs($longFin) > 90000 and abs($long) > 90000 ) $longFin-=360000;

  $beta = angle($longFin - $long, $latFin - $lat);
  //difference so no need to convert
  $gamma = $boatheading;
  $alpha = $gamma - $beta;
  if ($verbose > 0) {
    echo "beta = $beta, gamma= $gamma, alpha = $alpha\n";
  }
  $__vmg =  $boatspeed * cos(deg2rad($alpha));
  if (abs ( $__vmg ) > 0.0001) {
    return  ( $__vmg );
  }
  return 0;
}

function VMGortho($long, $lat,   $boatheading, $boatspeed, $orthoangletoend)
{
  $beta = $orthoangletoend;
  $gamma = $boatheading;
  $alpha = $gamma - $beta;
  $__vmgortho =  $boatspeed * cos(deg2rad($alpha));
  if (abs ($__vmgortho) > 0.0001) {
    return  ( $__vmgortho );
  }
  return 0;
}

function trianglecoordinates($A, $heading)
{
  //everything in pixels

  $v = polar2cartesianDrawing( $heading, POSITIONSIZE);
  $B = array($A[0] + $v[0], $A[1] + $v[1]);

  $moins90 = $heading - 90;
  if ($moins90 < 0)
    $moins90 += 360;

  $v = polar2cartesianDrawing( $moins90, POSITIONWIDTH/2);
  $C = array($A[0] + $v[0], $A[1] + $v[1]);

  $v = polar2cartesianDrawing( fmod($heading + 90 ,360), POSITIONWIDTH/2);
  $D = array($A[0] + $v[0], $A[1] + $v[1]);

  // die($A[0]." ". $A[1]." ".$C[0]." ". $C[1]." ".$D[0]." ". $D[1]."\n");
  $coord = array($B[0], $B[1],  $C[0], $C[1], $D[0], $D[1],$B[0], $B[1]);
  return $coord;
}

function windtrianglecoordinates($A, $wheading, $wspeed)
{
  //everything in pixels
  $a0 = $A[0];
  $a1 = $A[1];
  $wspdlog = log($wspeed);

  $va = polar2cartesianDrawing( $wheading     , WINDARROW_MINSIZE+$wspdlog*4);
  $vb = polar2cartesianDrawing( $wheading-90.0, WINDARROW_MINWIDTH+$wspdlog);
  $vc = polar2cartesianDrawing( $wheading+90.0, WINDARROW_MINWIDTH+$wspdlog);
  
  $coord = array($a0+$va[0], $a1+$va[1], $a0+$vb[0], $a1+$vb[1], $a0+$vc[0], $a1+$vc[1]);
  return $coord;
}


//this function draw a line from the outer of circle
//in the image $im with a geographic angle $angle
function drawWindVector($im, $color, $length, $angle, $thick)
{
  $center_x =imagesx($im)/2 ;

  drawSubWindVector($im, $color, 1.-2./$center_x, 1.-$length/$center_x, $angle, $thick);

}

//this function draw a line from the center to the outer of circle
//in the image $im with a geographic angle $angle
function drawSubWindVector($im, $color, $ratio_start, $ratio_end, $angle, $thick)
{
  imagesetthickness($im, $thick * 1);
  $center_x =imagesx($im)/2 ;
  $center_y =imagesy($im)/2 ;

  $vector_wind_x = cos(deg2rad($angle));
  $vector_wind_y = sin(deg2rad($angle));
  imageline ( $im,
              $center_x + $vector_wind_x*$center_x*$ratio_end,
              $center_y + $vector_wind_y*$center_y*$ratio_end,
              $center_x + $vector_wind_x*$center_x*$ratio_start,
              $center_y + $vector_wind_y*$center_y*$ratio_start,
              $color );

}

//this function draw the polar line 
//in the image $im with a geographic angle $angle
function drawWindPolar($im, $color, $colormax, $boattype, $windspeed, $thick, $whdg)
{
    imagesetthickness($im, $thick * 1);
    $imx = imagesx($im);
    $imy = imagesy($im);
    $center_x =imagesx($im)/2 ;
    $center_y =imagesy($im)/2 ;
    $dotx = $rdotx = $center_x;
    $doty = $rdoty = $center_y;


    $max = 0.00001;
    for ($a = 30 ; $a <= 180 ; $a = $a + 5) {   
	// on boucle aec un step de 5 pour limiter la conso cpu
        $bs = findboatspeed( abs($a), $windspeed, $boattype);
        if ($bs > $max) {
            $max = $bs;
        }
    }

    //on fixe le max Ã  120% du max trouvÃ© pour esquiver les indications du cadrans
    $radius = 1.2*$max;

    for ($a = 1 ; $a <= 180 ; $a = $a + 2) {
        $bs = findboatspeed( abs($a), $windspeed, $boattype);
                     
        $newx = cos(deg2rad(-$a+90+$whdg))*$center_x*$bs/$radius + $center_x;
        $newy = sin(deg2rad(-$a+90+$whdg))*$center_y*$bs/$radius + $center_y;
        $rnewx = cos(deg2rad($a+90+$whdg))*$center_x*$bs/$radius + $center_x;
        $rnewy = sin(deg2rad($a+90+$whdg))*$center_y*$bs/$radius + $center_y;

        //FIXME : affichage différent du max speed le principe est à affiner
        if ($bs > $max*.99) {
            $c = $colormax;
        } else {
            $c = $color;
        }

        imageline ($im, $dotx, $doty, $newx, $newy, $c);
        imageline ( $im, $rdotx, $rdoty, $rnewx, $rnewy, $c);
            
        $dotx = $newx;
        $doty = $newy;
        $rdotx = $rnewx;
        $rdoty = $rnewy;
     }
}

// Returns a string : lat / long in deg°min'sec"
// type = img / html  for the ° sign.
function giveDegMinSec($type, $latitude, $longitude)
{
  if ( $type == "img" ) {
    $degsign="\260";
  } else if ( $type == "engine" ) {
    $degsign=".";
  } else { 
    $degsign="&deg;";
  }
  //$lat= "46°55'30\"N";
  $l=abs($latitude);

  $deg=floor($l);
  $reste=($l - $deg) * 60;
  $min=floor($reste);
  $reste=($reste - $min) * 60;
  $sec=round($reste);
  if ($sec == 60 ) { $min++; $sec=0; };
  if ($min == 60 ) { $deg++; $min=0; };

  $lat=sprintf('%03d' . $degsign . '%02d\'%02d"', $deg, $min ,$sec);
  if (  $latitude > 0 )  {
    $lat=$lat.'N';
  } else {
    $lat=$lat.'S';
  }
  //printf ("LAT = %s, Lat = %s\n" , $latitude, $lat);

  //$long="10°38'25\"W";
  if ($longitude < -180) $longitude+=360;
  if ($longitude > 180)  $longitude-=360;
  $l=abs($longitude);
  $deg=floor($l);
  $reste=($l - $deg ) * 60;
  $min=floor($reste);
  $reste=($reste - $min ) * 60;
  $sec=floor($reste);

  $long=sprintf('%03d' . $degsign . '%02d\'%02d"',$deg, $min,$sec);
  if (  $longitude > 0 )  {
    $long=$long.'E';
  } else {
    $long=$long.'W';
  }
  return $lat . "/" . $long;
}


function popupLink( $url, $title)
{?>
<a href="<?php echo $url?>"
class="popUpWin" onkeypress="popUpWin('<?php echo $url?>', 'standard',600,400);"
onclick="popUpWin('<?php echo $url?>', 'standard',600,400);return false;">
<?php echo $title." ";?>
</a>
<?php
}

//that should be a static function of class users
//function that check a user account
//return the idusers if user exists & password is OK
//else return false
function checkAccount($login, $passwd)
{
  //find account
  $query = 'SELECT idusers,password FROM users WHERE username = "'.$login.'"';

  $result = wrapper_mysql_db_query_reader($query)  ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if (!$row) {
    return FALSE;
  } else {
    if ( $row[1] == $passwd ) {
      return $row[0];
    } else {
      return FALSE;
    }
  }
}

function isAdmin($login, $passwd)
{
  //find account
  $query = 'SELECT idusers,password,class FROM users WHERE username = "'.$login.'"';

  $result = wrapper_mysql_db_query_reader($query)  ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if (!$row) {
    return FALSE;
  } else {
    if ( $row[1] == $passwd && $row[2] == CLASS_ADMIN ) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

function isPlayerAdmin($idplayers) {
    $p = getPlayerObject($idplayers);
    return (!is_null($p) && $p->isAdmin());
}

function isAdminLogged() {
    return (isPlayerAdmin(getPlayerId()) || idusersIsAdmin(getLoginId()));
}

function idusersIsAdmin($idusers) {
    $u = getUserObject($idusers);
    return (!is_null($u) && $u->isAdmin());
}

function getNumOpponents($idraces, $check = TRUE) {
  //force type int
  $idraces = intval($idraces);

  // Verification si course existe
  if  ($check && !raceExists($idraces) ) {
      return (array (0,0,0));
  }

  // Nombre de classés / non classés
  $query= "SELECT count(*) FROM races_results WHERE position=". 
    BOAT_STATUS_ARR." AND idraces=$idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_arrived=$row[0];

  // Nombre de bateaux non classés mais sortis de la course
  $query= "SELECT count(*) FROM races_results WHERE position !=". 
    BOAT_STATUS_ARR . " AND idraces=$idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_out=$row[0];
  
  // Nombre de bateaux en course
  $query= "SELECT count(*) FROM races_ranking WHERE idraces=$idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_racing=$row[0];

  //              arrivés     en course       inscrits (arr + out + en course)
  return (array ($num_arrived,$num_racing,$num_arrived + $num_out + $num_racing));
}

function getNumOpponentBatch($racelist) {
  $results=array();
  $first = TRUE;
  $in = "RR.idraces IN (";
  foreach ($racelist as $idr) {
    $results[$idr] = array('num_arrived' => 0, 'num_racing' => 0,
			   'num_engaged' => 0);
    if ($first) {
      $first = FALSE;
      $in.=$idr;
    } else {
      $in.=",".$idr;
    }
  }
  $in.=")";
  $query= "SELECT count(RR.idusers), RR.idraces FROM races_results AS RR, races WHERE ".
    $in." AND races.idraces = RR.idraces AND RR.position=". BOAT_STATUS_ARR.
    " GROUP BY RR.idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  while($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $res = &$results[$row[1]];
    $res['num_arrived']=$row[0];
  }
  
  $query= "SELECT count(RR.idusers), RR.idraces FROM races_ranking AS RR, races WHERE ".
    $in." AND races.idraces = RR.idraces GROUP BY RR.idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  while($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $res = &$results[$row[1]];
    $res['num_racing']=$row[0];
  }

  $query= "SELECT count(RR.idusers), RR.idraces FROM races_results AS RR, races WHERE ".
    $in." AND races.idraces = RR.idraces AND RR.position!=". BOAT_STATUS_ARR.
    " GROUP BY RR.idraces";
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  while($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $res = &$results[$row[1]];
    $res['num_engaged']=$row[0];
  }
  foreach ($racelist as $idr) {
    $res = &$results[$idr];
    $res['num_engaged']+=$res['num_racing']+$res['num_arrived'];
  }
  return $results;
}

function getNumEngaged($idraces, $check = TRUE) {
  //force type int
  $idraces = intval($idraces);

  // Verification si course existe
  if  ($check && !raceExists($idraces) ) {
      return 0;
  }

  // Nombre de classés / non classés
  $query= "SELECT count(*) FROM races_results WHERE idraces=$idraces"; 
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_engaged=$row[0];
  $query= "SELECT count(*) FROM races_ranking WHERE idraces=$idraces"; 
  $result = wrapper_mysql_db_query_reader($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_engaged+=$row[0];
  
  return $num_engaged;
}


function htmlRacesListRow($rowdatas) {

      // Affichage de la course dans le tableau
      // idraces / racename / startdeparture / racenumboats / map
      $html = "";
      $numopps = $rowdatas['num_opps'];
      $num_arrived = $numopps['num_arrived'];
      $num_racing  = $numopps['num_racing'];
      $num_engaged = $numopps['num_engaged'];

      $html .= "<tr>\n";
      $html .= "<td>";
      if ( $rowdatas['racetype'] == RACE_TYPE_RECORD ) {
          $html .= "<img src=\"/images/site/P.png\" alt=\"Permanent\" />";
      }
      $html .= htmlIdracesLink($rowdatas['idraces'])."</td>\n";
      $html .= "<td>";
      $html .= htmlRacenameLink($rowdatas['idraces'], $rowdatas['racename'],
				$rowdatas['started']);
      $html .= "</td>\n";
      $html .= "<td class=\"departurecell\">&nbsp;" ;
      //Affiche une date de départ ou un statut.
      if ( $rowdatas['started'] == 0 ) {
          // Not started
          $html .= "<img src=\"/images/site/greenarrow.gif\" alt=\"not started\" />" ;
          $html .= gmdate("Y/m/d H:i:s",$rowdatas['deptime']);
      } else if ( $rowdatas['started'] == -1 ) {
          // Finished
          $html .= getLocalizedString("finished");
      } else {
          // La course est elle encore ouverte ?
          if ( $rowdatas['closetime'] > time() ) {
              $html .= "<img src=\"/images/site/yellowarrow.gif\" alt=\"open\" />" ;
              $html .= getLocalizedString("already");
          } else {
            $html .= "<img src=\"/images/site/redarrow.gif\" alt\"=closed\" />" ;
            $html .= getLocalizedString("closed");
          }
      }
      $html .= "</td>\n";
      $html .= "<td class=\"racestatscell\">";
      $html .= $num_arrived . " / " . $num_racing . " / " . $num_engaged  . "\n";

      $html .= "</td>\n";
      $html .= "  <td class=\"mapcell\">"; 
      $html .= htmlTinymap($rowdatas['idraces'], $rowdatas['racename']);
      $html .= "</td>\n";
      $html .= " </tr>\n";
      return $html;
}

function dispHtmlCurrentRacesList() {
    dispHtmlRacesList("WHERE started != -1");
}

function dispHtmlRacesList($where = "") {

  echo "<table>\n";
  echo "<thead>\n";
  echo "    <tr>\n";
  echo "    <th>".getLocalizedString("raceid")."</th>\n";
  echo "    <th>".getLocalizedString("racename")."</th>\n";
  echo "    <th>".getLocalizedString("departuredate")." (GMT)</th>\n";
  echo "    <th>". join('<br />', split("/", getLocalizedString("racenumboats")))."</th>\n";
  echo "    <th>".getLocalizedString("map")."</th>\n";
  echo "    </tr>\n";
  echo "   </thead>\n";
  echo "  <tbody>\n";
//  echo "<tr><td></td><td></td><td></td><td></td></tr>";


  // La requete qui donne la liste des courses en cours
  $query= "SELECT idraces, racename, started, deptime, startlong, startlat, ".
    "boattype, closetime, racetype, if(started=-1, 0, deptime) AS deptimesort ".
    "FROM races $where ORDER by started DESC, deptimesort ASC, closetime DESC, ".
    "idraces DESC";

  $result = wrapper_mysql_db_query_reader($query) or die($query);

  $allRacesRows = array();
  $allRacesIds  = array();
  while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    array_push($allRacesRows, $row);
    array_push($allRacesIds, $row['idraces']);      
  }
  $allNumOpponents = getNumOpponentBatch($allRacesIds);

  foreach($allRacesRows as $row) {
    $row['num_opps'] = $allNumOpponents[$row['idraces']];
    echo htmlRacesListRow($row);
  }
  
  echo "</tbody>\n";
  echo "</table>\n  ";
}

function htmlTinymap($idraces, $alt, $where="Left", $width=720) {

      $href="/racemap.php?idraces=".$idraces;
      $status_content = "&lt;img width=&quot;720&quot; src=&quot;$href&quot; alt=&quot;".$idraces."&quot;/&gt;";
      return "<a href=\"$href\" rel=\"lightbox[vlm]\" title=\"".strip_tags($alt)."\">" .
             "<img style=\"width:45px; height:30px;\" src=\"/images/site/cartemarine.png\" />" .
             "</a> ";
}

function htmlFlagImg($idflag) {
    return "<img src=\"/flagimg.php?idflags=".$idflag. "\" alt=\"Flag_". $idflag."\" />";
}

function htmlIdusersUsernameLink($country, $color, $idusers, $boatname, $username) {
    //This function is mapped in the user class
  	return htmlFlagImg($country) .
            "<a class=\"boatpalmares\" href=\"/palmares.php?type=user&amp;idusers=" . $idusers . "\"" .
            " style=\" border-bottom: solid #" . $color . "\" " . "title=\"". $boatname . "\">" .
            " (#". $idusers . ") " . $username . "</a>\n";
}

function htmlIdracesLink($idraces) {
    return sprintf("<a href=\"/ics.php?idraces=%s\">%d</a>" , $idraces, $idraces);
}

function htmlBoattypeLink($boattype) {
    $boattypename = strtoupper(ereg_replace('^.*_', '' ,$boattype));
    return sprintf("<a href=\"/speedchart.php?boattype=%s\" target=\"_speedchart\" rel=\"nofollow\">%s</a>", $boattype, $boattypename);
}

function htmlRacenameLink($idraces, $racename, $started) {
    return sprintf("<a href=\"/races.php?type=%s&amp;idraces=%d\">%s</a>", 
		   ($started == -1) ? "arrived" : "racing",
		   $idraces, $racename);
}

function getFlag($idflags, $force = 'no') {

    $original = DIRECTORY_COUNTRY_FLAGS . "/" . $idflags . ".png";
    
    // Création et mise en cache du flag si il n'existe pas ou est trop vieux
    if ( 
         ( ! file_exists($original) ) 
          ||  ($force == 'yes')
       ) {
    
          $req = "SELECT idflags, flag ".
                 "FROM flags WHERE idflags = '".$idflags."'";
          $ret = wrapper_mysql_db_query_reader ($req) or die (mysql_error ()); // ceci est une erreur "système" / applicative
          $col = mysql_fetch_array ($ret);
          if ( !$col['idflags'] )
          {
              //Ceci est une erreur de données absentes
              die("Not there : \"$idflags\"");
              return False;
          }
          else
          {
              //$img_out  = imagecreatefromstring( $col[1] ) or die("Cannot Initialize new GD image stream");
              // Sauvegarde
              //imagepng($img_out, $original) or die ("Cannot write cached raceflag");
              file_put_contents($original, $col['flag'], FILE_BINARY  | LOCK_EX) or die ("Cannot write cached flag");

          }
    }

    return $original;

}

function getFlagsListCursor($with_customs = True) {

    $req = "SELECT idflags FROM flags"; 

    if (!$with_customs) {
        $req .= " WHERE idflags NOT LIKE 'ZZ%'";
    }
    $req .= " ORDER BY idflags";
    
    $ret = wrapper_mysql_db_query_reader ($req) or die (mysql_error());
    return $ret;
}

function getRacemap($idraces, $force = 'no') {

    $image = "regate".$idraces;
    $original = DIRECTORY_RACEMAPS . "/" . $image . ".jpg";
    
    // Création et mise en cache de la racemap si elle n'existe pas ou est trop vieille
    if ( 
         ( ! file_exists($original) ) 
          ||  ($force == 'yes')
       ) {
    
          $req = "SELECT idraces, racemap ".
                 "FROM racesmap WHERE idraces = '".$idraces."'";
          $ret = wrapper_mysql_db_query_reader ($req) or die (mysql_error ()); // ceci est une erreur "système" / applicative
          $col = mysql_fetch_array ($ret, MYSQL_ASSOC);
          if ( !$col['idraces'] )
          {
              //Ceci est une erreur de données absentes
              return False;
          }
          else
          {
              //$img_out  = imagecreatefromstring( $col['racemap'] ) or die("Cannot Initialize new GD image stream");
              // Sauvegarde
              file_put_contents($original, $col['racemap'], FILE_BINARY  | LOCK_EX) or die ("Cannot write cached racemap");
              //imagejpeg($img_out, $original, 100) 
          }
    }

    return $original;

}

/* Insert a racemap image from content $racemapcontent for race $idraces */
function insertRacemapContent($idraces, $racemapcontent) {
    $req = "REPLACE INTO racesmap ( idraces, racemap ".
             ") VALUES ( ".
             "".$idraces." , ".
             "'".addslashes($racemapcontent)."') ";
    $ret = wrapper_mysql_db_query_writer ($req) or die (mysql_error ());
}

/* Insert a racemap image $racemapfile for race $idraces  */
function insertRacemap($idraces, $racemapfile) {
    if (! file_exists($racemapfile) ) {
        die("ERROR : File $racemapfile doesn't exist.");
        return False;
    } else {
        $img_blob = file_get_contents ($racemapfile);
        insertRacemapContent($idraces, $img_blob);
        return True;
    }
}

/* Insert a flagship image $racemapfile for race $idraces  */
function insertFlag($idflag, $flagfile) {
    if (! file_exists($flagfile) ) {
        return False;
    } else {
        //FIXME : tests sur la taille et le type ?
        $img_blob = file_get_contents ($flagfile);
        $req = "REPLACE INTO flags ( idflags, flag ".
                  ") VALUES ( ".
                  "'".$idflag."' , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query_writer ($req) or die (mysql_error ());
        return True;
    }
}


function raceExists($race) {
    //find a race
    //FIXME : we should select * for better use of mysql query cache
    $query = 'SELECT idraces FROM races WHERE idraces = "'.$race.'"';

    $result = wrapper_mysql_db_query_reader($query)  ;
    if ($result === False) return False;
    
    $row = mysql_fetch_array($result, MYSQL_NUM);
    if (!$row) {
        return False;
    } else {
        return True;
    }
}

/*return true if login already exist*/
function boatExists($idboat) {
    //find a boat
    $query = 'SELECT idusers FROM users WHERE idusers = "'.$idboat.'"';

    $result = wrapper_mysql_db_query_reader($query)  ;
    $row = mysql_fetch_array($result, MYSQL_NUM);
    if (!$row) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/*return true if login already exist*/
function checkLoginExists($login) {
    $query2 = 'SELECT idusers FROM users WHERE username = "'.$login.'"';
    $result2 = wrapper_mysql_db_query_reader($query2);

    return ($row2=mysql_fetch_array($result2, MYSQL_NUM));
}

/*create a new account with default values and return idusers*/
function createBoat($log, $pass, $mail, $boatname = 'boat') {
  $query3 = "INSERT INTO `users` ( `boattype` , `username` , `password` , `email`,"
    ."`boatname`, `color`, `boatheading`, `pilotmode`, `engaged` )"
    ."VALUES ( 'boat_imoca60', '".mysql_real_escape_string($log)."', '$pass', '$mail', '".mysql_real_escape_string($boatname)."', '000000', '0', '1', '0')";
  $result3 = wrapper_mysql_db_query_writer($query3);//or die("Query [$query3] failed \n");

  //is there another solution than reread from db?
  $query4 = "SELECT idusers FROM users WHERE username = \"$log\" ";
  $result4 = wrapper_mysql_db_query_writer($query4);// or die($query4);
  $row4 = mysql_fetch_array($result4, MYSQL_NUM);
  return ($row4[0]);
}

function mailInformation($who, $title, $message = null) {
    if (is_null($message)) $message = $title;
    $title = "[".MAIL_PREFIX."] ".$title;
    $message .= "\n";
    $message .= getLocalizedString("See you soon on VLM !")."\n";
    $message .= WWW_SERVER_URL."\n";
    $headers  = 'From: Virtual Loup-de-Mer <'.EMAIL_NOTIFY_VLM. ">\n" .
                'Sender: '.EMAIL_NOTIFY_VLM. "\n" .
                'Reply-To:  Virtual Loup-de-Mer <'.EMAIL_COMITE_VLM. ">\n" .
                'X-Mailer: PHP/' . phpversion()."\n".
                'Content-Type: text/plain; charset="UTF-8"\n'.
                "Content-Type: text/plain\n";
    if (SERVER_IS_SENDING_EMAIL) {
        $res = mail($who , $title, $message, $headers);
        return $res;
    } else {
        echo nl2br("<h3>$title</h3>\n$message");
        return True;
    }
}


function validip($ip) {
    if (!empty($ip) && ip2long($ip)!=-1) {
 
        $reserved_ips = array (
            array('0.0.0.0','2.255.255.255'),
            array('10.0.0.0','10.255.255.255'),
            array('127.0.0.0','127.255.255.255'),
            array('169.254.0.0','169.254.255.255'),
            array('172.16.0.0','172.31.255.255'),
            array('192.0.2.0','192.0.2.255'),
            array('192.168.0.0','192.168.255.255'),
            array('255.255.255.0','255.255.255.255')
        );
 
        foreach ($reserved_ips as $r) { 
            $min = ip2long($r[0]);
            $max = ip2long($r[1]);
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
        }
        return true;
    } 
    return false;
}

function getip() {
    if (isset($_SESSION['activeproxy'])
        && $_SESSION['activeproxy'] == 1
        && isset($_SERVER['HTTP_VLM_CLIENT_IP'])
        && validip($_SERVER['HTTP_VLM_CLIENT_IP'])
        ) {
        return $_SERVER['HTTP_VLM_CLIENT_IP'];
    }

    if (isset($_SERVER["HTTP_CLIENT_IP"]) && validip($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }

    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
            if (validip(trim($ip))) {
                return $ip;
            }
        }
    }
     
    if (isset($_SERVER["HTTP_X_FORWARDED"]) && validip($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]) && validip($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"]) && validip($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function getfullip() {
    $ipvars = Array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_FORWARDED", "REMOTE_ADDR");
    
    $ipstr = "";
    foreach($ipvars as $varname) {
        if (isset($_SERVER[$varname])) $ipstr = $ipstr . $varname . "=" . $_SERVER[$varname] . ", ";
    }
    if (isset($_SESSION['activeproxy'])
        && $_SESSION['activeproxy'] == 1
        && isset($_SERVER['HTTP_VLM_CLIENT_FULLIP'])) {
        $ipstr = $ipstr . $_SERVER['HTTP_VLM_CLIENT_FULLIP'];
    }

    return $ipstr;
}

function login($idus, $pseudo)
{
  //echo "calling login with $idus and $pseudo\n";
  //if (!isset($_SESSION['idusers']))
  {
    if (!isset($_SESSION)) session_start();
    if (isset($_SESSION['theme'])) unset($_SESSION['theme']);
    if ($idus > 0) {
        $_SESSION['idu'] = $idus;
        $_SESSION['loggedin'] = 1;
        $_SESSION['login'] = $pseudo;
    }
    if (isset($_SERVER['HTTP_VLM_PROXY_AGENT']) && $_SERVER['HTTP_VLM_PROXY_PASS']==PROXY_AGENT_PASS) $_SESSION['activeproxy'] = 1;

    // IP memorise "toutes les" adresses qu'on peut memoriser
    // ==> Faire la difference entre 2 PCs derriere un meme proxy
    //     et dans le cas d'un proxy, noter aussi son adresse, 
    //     pas seulement celle des machines dans son LAN
    //     ==> UPGRADE BDD : V0.13, ipaddr => varchar(255)
    $_SESSION['FULLIP'] = getfullip();
    $_SESSION['IP'] = getip();
    $_SESSION['LANG'] = NavigatorLanguage();
  }
}

function loginPlayer($idus, $pseudo, $idp, $playername) {
    login($idus, $pseudo);
    $_SESSION['idp'] = $idp;
    $_SESSION['playername'] = $playername;
}

function logout()
{
    if (isset($_COOKIE[session_name()]))
        setcookie(session_name(), '', time()-42000, '/');
    if (isset($_SESSION)) {
        $_SESSION = array();
        session_destroy();
    }
}

function getSessionValue($key) {
    if (isset($_SESSION[$key])) {
        return ($_SESSION[$key]);
    } else {
        return null;
    }
}

function isPlayerLoggedIn() {
    return (isset($_SESSION['idp']));
}

function isLoggedIn() {
    return (isset($_SESSION['idu']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1);
}

function getAdminName() {
    if (isPlayerLoggedIn()) {
        return getPlayername();
    } else {
        return getLoginName();
    }
}

function getLoginName() {
    return getSessionValue('login');
}

function getPlayername() {
    return getSessionValue('playername');
}

function getLoginId() {
    return getSessionValue('idu');
}

function getPlayerId() {
    return getSessionValue('idp');
}

function getLoggedPlayerObject() {
    static $player = null;
    if (!is_null($player)) return $player;
    $player = getPlayerObject(getPlayerId());
    return $player;
}

function getPlayerObject($id) {
    static $pobjects = Array();
    $id = intval($id);
    if (array_key_exists($id, $pobjects)) return $pobjects[$id];
    $p = new players($id);
    if ($p->idplayers == $id) {
        $pobjects[$id] = $p;
        return $p;
    } else {
        return null;
    }
}

function getLoggedUserObject() {
    static $user = null;
    if (!is_null($user)) return $user;
    $user = getUserObject(getLoginId());
    return $user;
}


function getUserObject($id, $initrow = NULL) {
  static $uobjects = Array();
  $id = intval($id);

  //TO PROTECT FROM POTENTIAL SIDE EFFECTS (?)
  if (!defined('MOTEUR') && array_key_exists($id, $uobjects)) {
    return $uobjects[$id];
  }
    
  if (is_null($initrow)) {
    $u = new users($id);
  } else {
    $u = new users($id, FALSE);
    $u->initFromArray($initrow);
  }

  if ($u->idusers == $id and $id > 0) {
    if (!defined('MOTEUR')) {
      $uobjects[$id] = $u;
    }
    return $u;
  } else {
    return NULL;
  }
}

function getPlayerList($where = null) {
    $list = array();
    $query = "SELECT idplayers, playername FROM players";
    if (!is_null($where)) {
        $query .= " WHERE ".$where;
    }
    $query .= " ORDER BY playername";
    $res = wrapper_mysql_db_query_reader($query) or die($query);
    while ($row = mysql_fetch_assoc($res)) {
        $list[$row['idplayers']] = $row;
    }
    return $list;
}

function getTheme()
{
   if (isLoggedIn() ) {
      //Connecté
      if ( isset($_SESSION['theme']) ) {
          //On utilise la session
          return ($_SESSION['theme']);
      }

      //La première fois, la session ne contient pas le theme
      $users = getLoggedUserObject();
      if ( $users->engaged != 0 ) {
          //Le joueur est engagé dans une course
          //FIXME : il faudrait cacher les races comme on a caché les users
          $race = new races($users->engaged);
          if ( !is_null($race->theme) and (strlen($race->theme) > 1) and ($race->theme != "default")) {
              //La course possède un thème, on l'utilise
              $_SESSION['theme'] = $race->theme;
              return ($_SESSION['theme']);
          }
      }

      // Dans tous les autres cas ou on est identifié, on renvoie le thème de l'utilisateur (éventuellement 'default')
      if (!is_null($users->theme) and (strlen($users->theme) > 1) ) {
          $_SESSION['theme'] = $users->theme;
      } else {
          $_SESSION['theme'] = "default";
      }
      return ($_SESSION['theme']);
   }
   //Non connecté, on utilise le thème par defaut
   return ( "default" );
}

function setUserPref($idusers,$pref_name,$pref_value, $save=true) {
    //FIXME : this is duplicated in users.class
    if ($idusers != "" and $save) {
        $query_pref = "REPLACE into user_prefs (idusers, pref_name, pref_value) " . 
          " VALUES ( " . $idusers . 
          ", " .     " '" . $pref_name .  "', '" . $pref_value . "')" ;
        if($result_pref = wrapper_mysql_db_query_writer($query_pref)) {
            return True;
        }
    }
    return False;
}

function getUserPref($idusers,$pref_name) {

    if ($idusers != "") {
        $query_pref = "SELECT `pref_value` FROM `user_prefs` WHERE `idusers` = $idusers AND `pref_name` = '$pref_name'";
        $result_pref = wrapper_mysql_db_query_reader($query_pref) or die($query_pref);
        if ( $row_pref = mysql_fetch_array($result_pref, MYSQL_NUM) ) {
            $pref_value = $row_pref[0];
        } else {
            $pref_value = NOTSET;
        }

        return ($pref_value);
    }
}

function listUserPref($idusers, $prefix = null) {
    if ($idusers != "") {
        $prefs=array();
        $query_pref = "SELECT `pref_name`, `pref_value` FROM `user_prefs` WHERE `idusers` = $idusers";
        if (!is_null($prefix)) $query_pref .= " AND `pref_name` LIKE '".$prefix."%'";
        $query_pref .= " ORDER BY `pref_name`";
        $result_pref = wrapper_mysql_db_query_reader($query_pref) or die($query_pref);
        while ( $row = mysql_fetch_array($result_pref, MYSQL_ASSOC) ) {
            $prefs[$row["pref_name"]]=$row["pref_value"];
        }
        return($prefs);
    }
}

function getBoatPopularity($idusers, $idraces=0) {
    $pop=0;
    if ($idusers != "") {
        $query = "SELECT `pref_value` FROM `user_prefs` ";
        $query .= " WHERE `pref_name`='mapPrefOpponents'";
        if ( $idraces != 0 ) {
            $query .= " AND `idusers` IN ( SELECT `idusers` FROM `users` WHERE `engaged` = $idraces)";
        }
        $result = wrapper_mysql_db_query_reader($query) or die($query);
        while ( $row = mysql_fetch_array($result, MYSQL_NUM) ) {
            $arr=explode(',' , $row[0]);
            if ( in_array($idusers, $arr) ) $pop++;
        }
        return($pop);
    }
}

function getBoatArrayFromIdList($list) {
    $barray = Array();
    foreach ($list as $idb) {
        $idb = intval($idb);
        $u = getUserObject($idb);
        if (!is_null($u)) {
            $b = Array();
            $b['idu'] = intval($u->idusers);
            $b['boatpseudo'] = $u->username;
            $b['boatname'] = $u->boatname;
            $b['engaged'] = intval($u->engaged);
            $barray[$b['idu']] = $b;
        }
    }
    return $barray;
}

function getOldDuration($idraces,$idusers)
{
  $query_duration = "SELECT duration FROM races_results WHERE idusers = $idusers AND idraces = $idraces";
  $result_duration = wrapper_mysql_db_query_reader($query_duration); // or die($query_duration);
  if ( $row_duration = mysql_fetch_array($result_duration, MYSQL_NUM) ) {
    $duration = $row_duration[0];
  } else {
    $duration = 0;
  }
  return ($duration);
}

function getRaceWinnerInfos($idraces) {
  $query_winner = "SELECT idusers,deptime,duration " .
    "  FROM races_results " . 
    " WHERE idraces= " . $idraces . 
    "   AND position= " . BOAT_STATUS_ARR . 
    " ORDER by duration limit 1;";
  //echo $query_winner;
  $result_winner = wrapper_mysql_db_query_reader($query_winner); // or die($query_winner);

  if ( $row_winner = mysql_fetch_array($result_winner, MYSQL_NUM) ) {
    return ($row_winner);
  } else {
    return (0);
  }
}

function getWaypointCrossingTime($idraces,$idwaypoint, $idusers)
{
  // Recherche temps de passage 
  $query_wptime = "SELECT `time`" . 
    "  FROM waypoint_crossing " .
    " WHERE idraces = $idraces " .
    "   AND idwaypoint = $idwaypoint " .
    "   AND idusers    = $idusers ".
    "   AND validity   = 1";
  //echo $query_wptime;

  $result_wptime = wrapper_mysql_db_query_reader($query_wptime); // or die($query_wptime);

  if ( $row_wptime = mysql_fetch_array($result_wptime, MYSQL_NUM) ) {
    $wptime = $row_wptime[0];
  } else {
    $wptime = -1;
  }
  return ($wptime);
}


function getWaypointBestTime($idraces,$idwaypoint)
{
  // Recherche temps de passage du meilleur à un waypoint
  $query_wptime = "SELECT idusers, `time`-`userdeptime` " . 
    "FROM waypoint_crossing " .
    "WHERE idraces = $idraces " .
    "AND idwaypoint = $idwaypoint " .
    "AND validity = 1 ".
    "ORDER by `time` - `userdeptime` ASC limit 1";

  //echo $query_wptime;

  $result_wptime = wrapper_mysql_db_query_reader($query_wptime) ; //or die($query_wptime);

  if ( $row_wptime = mysql_fetch_array($result_wptime, MYSQL_NUM) ) {
    $wptime = array($row_wptime[0],$row_wptime[1]);
  } else {
    $wptime = array("N/A","N/A");
  }
  return ($wptime);
}

function getWaypointHTMLSymbols($wpformat) {
  $wpsymbols = "";
  switch ($wpformat & (WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE)) {
  case WP_CROSS_ANTI_CLOCKWISE:
    $wpsymbols .= "&#x21BA; ";
    break;
  case WP_CROSS_CLOCKWISE:
    $wpsymbols .= "&#x21BB; ";
    break;
  default:
  }
  if (($wpformat & WP_CROSS_ONCE) == WP_CROSS_ONCE) {
    $wpsymbols .= "&#x2285; ";
  } 
  
  switch ($wpformat & (WP_ICE_GATE_N|WP_ICE_GATE_S)) {
  case WP_ICE_GATE_S:
    $wpsymbols .= "&#x27F0;";
    break;
  case WP_ICE_GATE_N:
    $wpsymbols .= "&#x27F1;";
  default:
  }
  return trim($wpsymbols);
}

function getWaypointHTMLSymbolsDescription($wpformat) {
  $wpdesc = "";
  switch ($wpformat & (WP_CROSS_CLOCKWISE|WP_CROSS_ANTI_CLOCKWISE)) {
  case WP_CROSS_ANTI_CLOCKWISE:
    $wpdesc .= getLocalizedString("Anti-clockwise")." ";
    break;
  case WP_CROSS_CLOCKWISE:
    $wpdesc .= getLocalizedString("Clockwise")." ";
    break;
  default:
  }
  if (($wpformat & WP_CROSS_ONCE) == WP_CROSS_ONCE) {
    $wpdesc .= getLocalizedString("Only once");
  } 
  
  switch ($wpformat & (WP_ICE_GATE_N|WP_ICE_GATE_S)) {
  case WP_ICE_GATE_S:
    $wpdesc .= getLocalizedString("Ice gate")."(".getLocalizedString("South").") ";
    break;
  case WP_ICE_GATE_N:
    $wpdesc .= getLocalizedString("Ice gate")."(".getLocalizedString("North").") ";
  default:
  }
  if ($wpdesc != "") $wpdesc = getLocalizedString("Crossing")." : ".$wpdesc;
  return trim($wpdesc);
}


// For a finished race, to give the Palmares
function getRaceRanking($idusers, $idraces) {
  // search for old races for this player
  $query = "SELECT idusers,position FROM races_results WHERE idraces=". 
    $idraces . " ORDER BY position DESC, duration ASC" ;
  $result = wrapper_mysql_db_query_reader($query) or die("Query failed : " . mysql_error." ".$query);
  $nbu=0;
  while ($row = mysql_fetch_array($result, MYSQL_NUM) ) {
    if( $row[0] == $idusers ) {
      if ( $row[1] > 0 ) {
        $rank=$nbu+1;
      } else {
        $rank=9999;
      }
    }
    //printf ("IDU=%d, RANK=%d<BR>\n", $row[0], $nbu);
    $nbu++;
  }
  // Si dernier, trouver la raison
  if ( $rank == 9999 ) {
    //if ( $position = BOAT_STATUS_ARR ) $commentaire = " (ARR)";
    $commentaire = "UNK"; // TO TRACK strange statuses
    if ( $position = BOAT_STATUS_HC ) $commentaire = " (HC)";
    if ( $position = BOAT_STATUS_HTP ) $commentaire = " (HTP)";
    if ( $position = BOAT_STATUS_DNF ) $commentaire = " (DNF)";
    if ( $position = BOAT_STATUS_ABD ) $commentaire = " (ABD)";
    return ($rank . $commentaire );
  } else {
    return ($rank . "/" . $nbu);
  }
}

function findNearestOpponents($idraces,$idusers,$num) {

  $ret_array=array();
  // search for nwp and dnm of this player
  $query = "SELECT nwp, dnm FROM races_ranking WHERE idraces=$idraces AND idusers=$idusers;";
  $result = wrapper_mysql_db_query_reader($query) or die("Query failed : " . mysql_error." ".$query);
  if ( $row = mysql_fetch_array($result, MYSQL_NUM)) {

    $nwp=$row[0];
    $dnm=$row[1];
    $query = "SELECT idusers FROM races_ranking WHERE idraces=$idraces AND ".
      "nwp=$nwp AND dnm IS NOT NULL ORDER BY abs($dnm - dnm) ASC LIMIT $num;";
    // FIXME dnm!=0 ORDER BY abs($nwp-nwp), abs($dnm - dnm) ASC LIMIT $num ?
    $result = wrapper_mysql_db_query_reader($query) or die("Query failed : " . mysql_error." ".$query);
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

      array_push ($ret_array, $row[0]);

    }

    // Sinon, souci, le bateau n'est pas en course on ne met que lui dans la liste
  } else {
    array_push($ret_array, $idusers);
  }

  return($ret_array);
}

function findTopUsers($idraces,$num) {

  $ret_array=array();
  // search for nwp and dnm of this player
  
  $query = "SELECT idusers FROM races_ranking WHERE idraces=$idraces AND ".
    "dnm IS NOT NULL AND idusers>1 ORDER BY nwp DESC, dnm ASC LIMIT $num;";
  
  $result = wrapper_mysql_db_query_reader($query) or die("Query failed : " . mysql_error." ".$query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    array_push ($ret_array, $row[0]);
  }

  return($ret_array);
}

function displayPalmares($idusers) {

  // search for old races for this player
  $query = "SELECT idraces from races_results where idusers = " . $idusers ;
  $result = wrapper_mysql_db_query_reader($query) or die("Query failed : " . mysql_error." ".$query);

  echo "<table>\n";
  echo "<thead>\n";
  echo "    <tr>\n";
  echo "    <th>".getLocalizedString("raceid")."</th>\n";
  echo "    <th>".getLocalizedString("racename")."</th>\n";
  echo "    <th>".getLocalizedString("arrived")."</th>\n";
  echo "    </tr>\n";
  echo "</thead>\n";
  echo "<tbody>\n";

  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $racesObj= new races($row[0]);
    printf ("<tr><td>%s</td><td>%s</td><td>%s</td></tr>", 
	    htmlIdracesLink($row[0]),
	    htmlRacenameLink($row[0], $racesObj->racename, -1), //link to arr.
	    getRaceRanking($idusers,$row[0])); // Le classement
  }
  printf ("</tbody></table>");
  return(0);
}

// This function returns true if user arg1 has finished race arg2
function userFinishedThisRace($idusers, $idraces)
{
  //echo "UFTR : idu=$idusers , idr=$idraces\n";
  if ( $idraces == 0 ) return(false);
  $query = "SELECT 1 FROM races_results 
              WHERE idusers=$idusers and idraces=$idraces and position=" .  BOAT_STATUS_ARR  ;
  //printf ("Query : %s\n", $query);
  $result = wrapper_mysql_db_query_reader($query);
  if ( mysql_fetch_array($result, MYSQL_NUM) )  return(true);
  
  return (false);
}

// This function gives the available races for a boat
function availableRaces($idusers = 0)
{
  $records = array();
  if ( $idusers == 0 ) return ($records);

  $timestamp = time();
  $query = "SELECT idraces,depend_on,qualifying_races,maxboats FROM races 
               WHERE started = 0 OR ( closetime > $timestamp OR closetime=0
                        ) ORDER BY deptime ASC;";
  //printf ("Query : %s\n", $query);
  $result = wrapper_mysql_db_query_reader($query);
  
  $allRacesRows = array();
  $allRacesIds  = array();
  while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    array_push($allRacesRows, $row);
    array_push($allRacesIds, $row['idraces']);      
  }
  $allNumOpponents = getNumOpponentBatch($allRacesIds);

  foreach($allRacesRows as $row) {
    $idraces     = $row['idraces'];
    $numopps     = $allNumOpponents[$idraces];
    $num_arrived = $numopps['num_arrived'];
    $num_racing  = $numopps['num_racing'];
    $num_engaged = $numopps['num_engaged'];

    if ( $row['maxboats'] != 0 && $num_engaged >= $row['maxboats'] ) {
      continue;
    }

    // si pas de course de qualification, on ajoute
    if ( $row['qualifying_races'] == "" ) {
      array_push ($records, $idraces);
    } else {
      // Sinon, on vérifie que le bateau est qualifié (a fini une courses de qualif)
      $qualraces = explode(' ', $row['qualifying_races']);
      foreach ($qualraces as $qr) {
        if ( userFinishedThisRace($idusers, $qr ) ) {
          array_push ($records, $idraces);
          break;
        }
      }
    }
  }
  return ($records);
}

function queryRacesBatch($where = NULL) {
  $query="SELECT idraces,racename,started,deptime,startlong,startlat,".
    "boattype,closetime,racetype,firstpcttime,depend_on,qualifying_races,".
    "idchallenge,coastpenalty,bobegin,boend,maxboats,theme,vacfreq,".
    "updated FROM races ".(($where==NULL)?"":$where);
  
  $result=wrapper_mysql_db_query_reader($query) or die("Query [$query] failed \n");
  return $result;
}

function checkMapArea($value) {
  if (isset($_COOKIE['maparea']) && $_COOKIE['maparea']==$value ) {
    printf("checked");
  }
}

function centerDualCoordMilli($mlat1, $mlon1, $mlat2, $mlon2) {
    $wp = centerDualCoord($mlat1/1000., $mlon1/1000., $mlat2/1000., $mlon2/1000.);
    return Array('mlat' => $wp['lat']*1000., 'mlon' => $wp['lon']*1000.);
}

function centerDualCoord($lat1, $lon1, $lat2, $lon2) {
    $latwp=($lat1 + $lat2)/2;
    $longwp=($lon1 + $lon2)/2;
    if ( abs($lon1 - $lon2 ) > 180 ) {
      //on inverse le centre si ça fait plus de 180°
      $longwp += 180;
    }
    return array("lat" => $latwp, "lon" => $longwp);
}

function logUserEvent($idusers, $idraces, $action) {
    if (isPlayerLoggedIn()) {
        $idplayers = getPlayerId();
    } else {
        $idplayers = -1;
    }
    logPlayerEvent($idplayers, $idusers, $idraces, $action);
}

function logPlayerEvent($idplayers, $idusers, $idraces, $action) {
    //tracking...
    if (isset($_SERVER["HTTP_VLM_PROXY_AGENT"])) {
        $ua = $_SERVER["HTTP_VLM_PROXY_AGENT"];
    } else {
        $ua = $_SERVER["HTTP_USER_AGENT"];
    }
    $idusers = is_null($idusers) ? -1 : $idusers;
    $idplayers = is_null($idplayers) ? -1 : $idplayers;
    $idraces = is_null($idraces) ? -1 : $idraces;
    if (isset($_SESSION['IP']) && isset($_SESSION['FULLIP'])) {
        $ip = $_SESSION['IP'];
        $fullip = $_SESSION['FULLIP'];
    } else {
        $fullip = getfullip();
        $ip = getip();
    }

    $query_user_event = "INSERT INTO `user_action` (`idplayers`, `idusers`, `ipaddr`, `fullipaddr`, `idraces`, `action`, `useragent`, `actionserver`) " .
                        " values (" . $idplayers . ", " . $idusers . ", '" . $ip . "' , '" . $fullip . "' ," . $idraces .
                        ",'" . addslashes($action) . "', '". addslashes($ua) ."' , '".SERVER_NAME."' )";
    $result = wrapper_mysql_db_query_writer($query_user_event) or die("Query [$query_user_event] failed \n");
}

function htmlAbandonButton($idusers, $idraces) {

    return "<form id=\"abandonform\" name=\"abandon\" action=\"subscribe.php\">
            <input type=\"hidden\" name=\"idusers\" value=\"$idusers\" />
            <input type=\"hidden\" name=\"idraces_unsubscribe\" value=\"".$idraces."\" />     
            <input type=\"hidden\" name=\"type\" value=\"unsubscribe\" />
            <input type=\"button\" onclick=\"confirmation_abandon('".getLocalizedString("unsubscribe").". Confirmation ?');\"
                       value=\"".getLocalizedString("unsubscribe")."\" />
            </form>";
}

function htmlQuery($sql) {
    $result = wrapper_mysql_db_query_writer($sql) or die("<h3 class=\"admin-error\">Query [".$sql."] failed</h3>");
    
    if (!$result or !mysql_num_rows($result)) {
        echo "<h3 class=\"admin-infos\">Nothing to display</h3>";
        return False;
    }

    echo "<table class=\"admin-query\">";
    echo "<tr class=\"admin-query\">";
    
    $i = 0;
    while ($i < mysql_num_fields($result)) {
        $meta = mysql_fetch_field($result, $i);
        echo "<th>";
        if ($meta) {
            echo $meta->name;
        }
        echo "</th>";
        $i++;
    }
    echo "</tr>";
 
    $oddeven = 0;
    while ( $row = mysql_fetch_array($result, MYSQL_NUM)) {
        $oddeven = ($oddeven+1) % 2;
        echo "<tr class=\"admin-query-$oddeven\">";
        for($i=0;$i<count($row); $i++) {
            echo "<td class=\"admin-query\">";
            echo nl2br($row[$i]);
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

function insertAdminChangelog($argarray) {
    if (!is_array($argarray)) die("Not an array for insertAdminChangelog");

    $arglist = Array("operation", "tab", "rowkey", "col", "oldval", "newval");
    $values = "";
    foreach ($arglist as $varname) {
      if (!array_key_exists($varname, $argarray) or is_null($argarray[$varname])) {
	$values .= ", NULL ";
      } else {
	$values .= ", '".addslashes($argarray[$varname])."'";
      }
    }
    $query = sprintf("INSERT INTO admin_changelog (user, host, operation, tab, rowkey, col, oldval, newval) VALUES ('%s', '%s' %s )",
		     getAdminName(), getip(), $values);
    wrapper_mysql_db_query_writer($query);
}

function htmlShouldNotDoThat() {
    return "<h3>".getLocalizedString("You should not do that.").getLocalizedString("Your IP has been logged")."</h3>";
}

function generatePassword($strseed) {
    //un peu basique, mais pour l'usage ça ira
    return substr(base_convert(md5(mt_rand()+crc32($strseed)), 16, 36), mt_rand(0, 16), 8);
}

function getLinktypeString($linktype) {
    switch($linktype) {
        case PU_FLAG_OWNER : return getLocalizedString("owner");
        case PU_FLAG_BOATSIT : return getLocalizedString("Boatsitter");
        default : return getLocalizedString("Unknow link's type");
    }
}

function requireLoggedPlayer($msg = null) {
    if (!isPlayerLoggedIn()) {
        if (is_null($msg)) $msg = "You are not logged in !";
        printErrorAndDie("Forbidden", $msg);
    }
}

function printErrorAndDie($title, $msg = null, $link = null) {
    echo "<div class=\"error\">";
    echo "<h1 class=\"error\">";
    echo getLocalizedString($title);
    echo "</h1>";
    if (!is_null($msg)) {
        echo "<p>";
        echo getLocalizedString($msg);
        echo "</p>";
    }
    if (!is_null($link)) {
        echo "<p>";
        echo "<a href=\"$link\">".getLocalizedString("Click here")."</a>";
        echo "</p>";
    }

    echo "</div>";
    include('includes/footer.inc');
    exit();
}

?>
