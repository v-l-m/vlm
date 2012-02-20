<?php
/********Functions*********/

//     String $lang NavigatorLanguage()
//
// Return navigator language.
// As VLM just know French and English,
// return is "fr" if French version, and
// "en" for all other versions ......
//--------------------------------------------
include_once("vlmc.php");


function wrapper_mysql_db_query($cmd) {
  if (defined('MOTEUR') && defined('TRACE_SQL_QUERIES')) {
    echo "*** DB ACCESS ".$cmd;
    $sql_start_time=microtime(1);
    $res = mysql_query($cmd);
    $sql_end_time=microtime(1);
    echo " : ".($sql_end_time-$sql_start_time)."s\n";
    return $res;
  }
  return mysql_query($cmd);
}

// Prot�ge la variable
function quote_smart($value) {
  // Stripslashes
  if (get_magic_quotes_gpc()) {
    $value = stripslashes($value);
  }
  return $value;
}

function NavigatorLanguage()
{
  $lang = getenv("HTTP_ACCEPT_LANGUAGE");
  $lang = substr($lang,0,2);
  if (in_array($lang, array("fr", "en", "pt", "it", "es"))) {
      return $lang;
  } else  {
      return "en";
  }
}

function getCurrentLang() {
    //FIXME utiliser NavigatorLanguage pour d�finir le default en combinant avec strings
    if (isset($_REQUEST['lang'])) {
        return quote_smart($_REQUEST['lang']);
    } else {
         return NavigatorLanguage();  
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
  // Petite modif pour �viter les divisions/0 sur les zones sans vent.
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
  // Modulo : real -> int
  // $a_trigo = (360 - $a)%360 + 90;

  // Version FM du 01/03/2008
  //$a_trigo = 360 -$a; 
  //while ( $a_trigo > 360 ) { $a_trigo-=360; };
  //while ( $a_trigo < 0 )   { $a_trigo+=360; };
  //$a_trigo+=90;
  
  // Simplification JFD du 03/01/2008 
  $a_trigo = deg2rad(fmod(360+90-$a, 360));

  // Propositon Maitai du 03/01/2008 (plus couteux en CPU)
  /*
    if ( sin(deg2rad($a)>=0){
    $a_trigo=rad2deg(acos(cos(deg2rad($a + 90))))
    } else {
    $a_trigo=360-rad2deg(acos(cos(deg2rad($a + 90))))
    }
  */

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
  $a = deg2rad(fmod($a-90 ,360)); // FIXME as $a is used only in cos and sin
                                  // there is need to normalize it
  $result[0] = $r*cos($a);
  $result[1] = $r*sin($a);
  //echo "\npolar2cartesian angle_trigo = $a_trigo, x = $result[0], y = $result[1] \n";
  return $result;
}


function errorprint($message) {
  printf ("<h1>" . $message . "</h1>" );
}

/*display a string containing the difference between now and the last update*/
function lastUpdate($strings, $lang)
{
  if (file_exists(CRONVLMLOCK)) {
    printf ($strings[$lang]["processing"] );
  } else {
    $query2 = "SELECT `time`,races,boats,duration,update_comment FROM updates ORDER BY `time` DESC LIMIT 1";
    $result2 = wrapper_mysql_db_query($query2) or die("Query [$query2] failed \n");
    $row2 = mysql_fetch_array($result2, MYSQL_NUM);
    $lastupdate = $row2[0];
    $races = $row2[1];
    $boats = $row2[2];
    $duration = max($row2[3],1);
    $update_comment = $row2[4];
    $interval = time() - $lastupdate;

    $intervalarray = duration2string($interval);
    printf ( $strings[$lang]["lastupdate"]. " <br />\n",
             gmdate('H:i:s', time() ) . ' GMT', $intervalarray['hours'],$intervalarray['minutes'],$intervalarray['seconds'] );
    printf ("%s seconds (<span title=\"%s\">%d race(s)</span>, %d boat(s)), %2.2f boats/sec (<a target=\"_blank\" href=\"status/race-engine-status.php\" rel=\"nofollow\">status page</a>)", $duration, $update_comment, $races, $boats, $boats/$duration);
  }
}

/*display the next supposed update*/
function nextUpdate($strings, $lang)
{
  $query2 = "SELECT `time` FROM updates ORDER BY `time` DESC LIMIT 1";
  $result2 = wrapper_mysql_db_query($query2) or die("Query [$query2] failed \n");
  $row2 = mysql_fetch_array($result2, MYSQL_NUM);
  $lastupdate = $row2[0];
  $interval = time() - $lastupdate;
  //echo "interval = $interval et DELAYBETWEENUPDATE ".DELAYBETWEENUPDATE."\n";
  if ($interval > DELAYBETWEENUPDATE) //problems during update
    {
      printf("         ".$strings[$lang]["noupdate"]."\n");
    }
  else
    {
      $next = duration2string(DELAYBETWEENUPDATE - $interval);
      printf("      ".$strings[$lang]["nextupdate"], $next['hours'], $next['minutes'] );
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
// by sailing in the west of it (to see the WP at head 90� when "crossing")
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


function windspeedtocolorbeaufort($windspeed, $im)
{
  //fromhex("ff0000")
  // <=F0 : blanc
  if ($windspeed <= 1) { $colo = ImageColorAllocate($im, 255, 255, 255); }

  // <=F1 : bleu clair legerement gris
  else if ($windspeed <= 3) { $colo = ImageColorAllocate($im, 150, 150, 225 ); }

  // <=F2 : bleu un peu plus soutenu
  else if ($windspeed <= 6) { $colo = ImageColorAllocate($im, 80, 140, 205); }

  // <=F3 : bleu plus fonc� 
  else if ($windspeed <= 10) { $colo = ImageColorAllocate($im, 60, 100, 180); }

  // <=F4 : vert
  else if ($windspeed <= 15) { $colo = ImageColorAllocate($im, 65, 180, 100); }

  // <=F5 : jaune l�g�rement vert
  else if ($windspeed <= 21) { $colo = ImageColorAllocate($im, 180, 205, 10); }

  // <=F6 : jaune orang�
  else if ($windspeed <= 26) { $colo = ImageColorAllocate($im, 210, 210, 22); }
  //else if ($windspeed <= 26) { $colo = ImageColorAllocate($im, 215, 210, 32); }

  // <=F7 : jaune orang� un peu plus rougeatre
  else if ($windspeed <= 33) { $colo = ImageColorAllocate($im, 225, 210, 32); }

  // <=F8 : orange fonc�
  else if ($windspeed <= 40) { $colo = ImageColorAllocate($im, 255, 179, 0); }

  // <=F9 : rouge 
  else if ($windspeed <= 47) { $colo = ImageColorAllocate($im, 255, 111, 0); }

  // <=F10 rouge / marron
  else if ($windspeed <= 55) { $colo = ImageColorAllocate($im, 255, 43, 0); }

  // <=F11 marron
  else if ($windspeed <= 63) { $colo = ImageColorAllocate($im, 230, 0, 0); }

  // F12  rouge/noir
  else $colo = ImageColorAllocate($im, 127, 0, 0);

  return $colo;
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
  Sinon,  pas // � axe des ordonn�es (Xa != Xb) , ni des abscisses : y = mx + p (m = meridien, p = parall�le)

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
  $days = floor($dur/86400);
  $hours = floor(($dur - $days*86400)/3600);
  $minutes = floor(($dur - $days*86400 - $hours*3600)/60);
  $seconds = $dur - $days*86400 - $hours*3600 - $minutes*60;
  return array('days' => $days, 'hours' => $hours,
	       'minutes' => $minutes, 'seconds' => $seconds);
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
  if ($verbose > 0) {
    echo "beta = $beta, gamma= $gamma, alpha = $alpha\n";
  }
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

  $v = polar2cartesianDrawing( $wheading, WINDARROW_MINSIZE+log($wspeed)*4);
  $B = array($A[0] + $v[0], $A[1] + $v[1]);

  $moins90 = $wheading - 90;
  if ($moins90 < 0)
    $moins90 += 360;

  $v = polar2cartesianDrawing( $moins90, WINDARROW_MINWIDTH+log($wspeed));
  $C = array($A[0] + $v[0], $A[1] + $v[1]);

  $v = polar2cartesianDrawing( fmod($wheading + 90, 360), WINDARROW_MINWIDTH+log($wspeed));
  $D = array($A[0] + $v[0], $A[1] + $v[1]);

  // die($A[0]." ". $A[1]." ".$C[0]." ". $C[1]." ".$D[0]." ". $D[1]."\n");
  $coord = array($B[0], $B[1],  $C[0], $C[1], $D[0], $D[1],$B[0], $B[1]);
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


    $max = 0;
    for ($a = 30 ; $a <= 180 ; $a = $a + 5) {   
	// on boucle aec un step de 5 pour limiter la conso cpu
        $bs = findboatspeed( abs($a),
                       $windspeed,
                       $boattype
                     );
        if ($bs > $max) {
            $max = $bs;
        }
    }

    //on fixe le max à 120% du max trouvé pour esquiver les indications du cadrans
    $radius = 1.2*$max;

    for ($a = 1 ; $a <= 180 ; $a = $a + 2) {
        $bs = findboatspeed( abs($a),
                       $windspeed,
                       $boattype
                     );
                     
        $newx = cos(deg2rad(-$a+90+$whdg))*$center_x*$bs/$radius + $center_x;
        $newy = sin(deg2rad(-$a+90+$whdg))*$center_y*$bs/$radius + $center_y;
        $rnewx = cos(deg2rad($a+90+$whdg))*$center_x*$bs/$radius + $center_x;
        $rnewy = sin(deg2rad($a+90+$whdg))*$center_y*$bs/$radius + $center_y;

        //FIXME : affichage diff�rent du max speed le principe est � affiner
        if ($bs > $max*.99) {
            $c = $colormax;
        } else {
            $c = $color;
        }

        imageline ( $im,
                    $dotx,
                    $doty,
                    $newx,
                    $newy,
                    $c
                  );
        imageline ( $im,
                    $rdotx,
                    $rdoty,
                    $rnewx,
                    $rnewy,
                    $c
                  );
            
        $dotx = $newx;
        $doty = $newy;
        $rdotx = $rnewx;
        $rdoty = $rnewy;
        
     }

}

// Returns a string : lat / long in deg�min'sec"
// type = img / html  for the � sign.
function giveDegMinSec($type, $latitude, $longitude)
{
  if ( $type == "img" ) {
    $degsign="�";
  } else if ( $type == "engine" ) {
    $degsign=".";
  } else { 
    $degsign="&deg;";
  }
  //$lat= "46�55'30\"N";
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

  //$long="10�38'25\"W";
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

  $result = wrapper_mysql_db_query($query)  ;
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

  $result = wrapper_mysql_db_query($query)  ;
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

function idusersIsAdmin($idusers)
{
  if (round($idusers) == 0) return FALSE;
  //find account
  $query = "SELECT class FROM users WHERE idusers = " . $idusers;

  $result = wrapper_mysql_db_query($query)  ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if (!$row) {
    return FALSE;
  } else {
    if ( $row[0] == CLASS_ADMIN ) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

function getNumOpponents($idraces) {
  // Verification si course existe
  $query= "SELECT count(*)
             FROM races
       where idraces = " . round($idraces) . ";";
  $result = wrapper_mysql_db_query($query) ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if  ( $row[0] != 1 ) {
    return (array (0,0,0));
  }

  // Nombre de class�s / non class�s
  $query= "SELECT count(*) 
             FROM races_results 
       where position = " . BOAT_STATUS_ARR . "
       and   idraces = $idraces ;";
  $result = wrapper_mysql_db_query($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_arrived=$row[0];

  // Nombre de bateaux non class�s mais sortis de la course
  $query= "SELECT count(*) 
             FROM races_results 
       where position != " . BOAT_STATUS_ARR . "
       and   idraces = $idraces ;";
  $result = wrapper_mysql_db_query($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_out=$row[0];

  // Nombre de bateaux en course
  $query= "SELECT count(*) 
             FROM races_ranking 
       where idraces = $idraces ;";
  $result = wrapper_mysql_db_query($query) or die($query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $num_racing=$row[0];

  //              arriv�s     en course       inscrits (arr + out + en course)
  return (array ($num_arrived,$num_racing,$num_arrived + $num_out + $num_racing));
}

function dispHtmlRacesList($strings, $lang) {

  echo "<h4>".$strings[$lang]["current_races"]."</h4>";
  echo "<table>\n";
  echo "<thead>\n";
  echo "    <tr>\n";
  echo "    <th>".$strings[$lang]["raceid"]."</th>\n";
  echo "    <th>".$strings[$lang]["racename"]."</th>\n";
  echo "    <th>".$strings[$lang]["departuredate"]." (GMT)</th>\n";
  echo "    <th>".$strings[$lang]["racenumboats"]."</th>\n";
  echo "    <th>".$strings[$lang]["map"]."</th>\n";
  echo "    </tr>\n";
  echo "   </thead>\n";
  echo "  <tbody>\n";
  echo "<tr><td></td><td></td><td></td><td></td></tr>";

  $finished_races_title="<h4>".$strings[$lang]["finished_races"]."</h4>\n";
  $finished_races="";


  // La requete qui donne la liste des courses
  $query= "SELECT idraces, racename, started, deptime, startlong, startlat,
             boattype, closetime, racetype, firstpcttime, depend_on, qualifying_races,
             maxboats
             FROM races ORDER by deptime asc, closetime desc, idraces desc;";

  $result = wrapper_mysql_db_query($query) or die($query);

  while ( $row = mysql_fetch_array($result, MYSQL_NUM)) {

    $idraces = $row[0];
    $racename = $row[1];
    $started = $row[2];
    $deptime = $row[3];
    $startlong = $row[4]; 
    $startlat = $row[5];
    $boattype = $row[6];
    $closetime = $row[7];
    $racetype = $row[8];
    $firstpcttime = $row[9];
    $depend_on = $row[10];
    $qualifying_races = $row[11];
    $maxboats = $row[12];

    // Calcul du nombre de bateaux arriv�s, en course, inscrits
    list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);
    //printf("RACE=%d, %d NA, %d NR, %d NE<BR>", $idraces, $num_arrived , $nom_racing, $num_engaged);

    if ( $started == 0 ) {
      $departure = gmdate("Y/m/d H:i:s",$deptime);

      // Affichage de la course dans le tableau
      echo " <tr>\n";
      echo "<td>";
      if ( $racetype == RACE_TYPE_RECORD ) {
        echo "<img src=\"/images/site/P.png\" alt=\"Permanent\" />";
      }
      echo $idraces."</td>\n";
      echo "<td>";
      echo "<a href=\"races.php?lang=$lang&amp;idraces=".$idraces. "&amp;type=racing" . "\">";
      echo $racename."</a>";
      echo "</td>\n";
      echo "<td>" ;
      echo "<img src=\"/images/site/greenarrow.gif\" alt=\"not started\" />" ;
      echo "$departure</td>\n";
      echo "  <td align=\"center\">" .  $num_engaged ;
      if ( $maxboats != 0 ) {
        echo " (max " . $maxboats . ")";
      }
      echo "</td>\n";
      echo "  <td align=\"center\">"; 

      echo htmlTinymap($idraces, $strings[$lang]["racemap"], "Right");
      echo "</td>\n";
      echo " </tr>\n";


    } else if ( $num_racing == 0 ) {
      //if started and no one is playing status is "finished"
      $departure = $strings[$lang]["finished"];
      $finished_races="<a href=\"races.php?lang=$lang&amp;idraces=".$idraces.'">('.$idraces.") ".$racename."</a><br />\n".$finished_races;

    } else {
      // La course est elle encore ouverte ?
      if ( $closetime > time() ) {
        $departure  = "<img src=\"/images/site/yellowarrow.gif\" alt=\"open\" />" ;
        $departure .= $strings[$lang]["already"];
      } else {
        $departure  = "<img src=\"/images/site/redarrow.gif\" alt\"=closed\" />" ;
        $departure .= $strings[$lang]["closed"];
      }

      // Affichage de la course dans le tableau
      echo " <tr>\n";
      echo "<td>";
      if ( $racetype == RACE_TYPE_RECORD ) {
        echo "<img src=\"/images/site/P.png\" alt=\"Permanent\" />";
      }

      echo "<a href=\"races.php?lang=$lang&amp;idraces=".$idraces. "&amp;type=racing" . "\">";
      echo $idraces."</a>";
      echo "</td>\n";
      echo "<td>";
      echo "<a href=\"races.php?lang=$lang&amp;idraces=".$idraces;
      if ( $num_arrived != 0 ) {
        echo "&amp;type=arrived" ;
      } else { 
        echo "&amp;type=racing" ;
      }
      echo "\">";
      echo $racename."</a></td>\n";
      echo "<td>" ;
      echo "$departure</td>\n";
      echo "  <td align=\"center\">" . $num_arrived . " / " . $num_racing . " / " . $num_engaged  . "</td>\n";
      echo "  <td align=\"center\">"; 

      /*
        $bounds = $fullRacesObj->getRacesBoundaries();
        $longitude=($bounds["east"]-$bounds["west"])/2;
        $latitude=($bounds["north"]-$bounds["south"])/2;
        $maparea=($bounds["north"]-$bounds["south"])*60;
        $href = "mercator.page.php?".
        "maparea=".$maparea.
        "&amp;long=".$longitude.
        "&amp;lat=".$latitude.
        "&amp;list=all".
        "&amp;tracks=on".
        "&amp;windtext=off".
        "&amp;x=800&amp;y=600&amp;proj=mercator&amp;text=left&amp;idraces=".$fullRacesObj->races->idraces;
      */
      // Carte de la course
      echo htmlTinymap($idraces, $strings[$lang]["racemap"], "Right");
      echo "</td>\n";
      echo " </tr>\n";
    }
  }

  echo "</tbody>\n";
  echo "</table>\n  ";
  echo $finished_races_title;
  echo $finished_races;
}

function htmlTinymap($idraces, $alt, $where="Left", $width=720) {

      $href="/racemap.php?idraces=".$idraces;
      $status_content = "&lt;img width=&quot;720&quot; src=&quot;$href&quot; alt=&quot;".$idraces."&quot;/&gt;";
      return "<img style=\"width:45px; height:30px;\" src=\"/images/site/cartemarine.png\" " .
//FIXME : on doit pouvoir faire la taille de la popup en dynamique en js
          " onmouseover=\"showDiv$where('infobulle','$status_content', 0, 0);\" " .
          " onmouseout=\"hideDiv('infobulle');\" " .
          " alt=\"" .$alt. "\"/>";
}

function getFlag($idflags, $force = 'no') {

    $original = DIRECTORY_COUNTRY_FLAGS . "/" . $idflags . ".png";
    
    // Cr�ation et mise en cache de la racemap si elle n'existe pas ou est trop vieille
    if ( 
         ( ! file_exists($original) ) 
          ||  ($force == 'yes')
       ) {
    
          $req = "SELECT idflags, flag ".
                 "FROM flags WHERE idflags = '".$idflags."'";
          $ret = wrapper_mysql_db_query ($req) or die (mysql_error ()); // ceci est une erreur "syst�me" / applicative
          $col = mysql_fetch_array ($ret);
          if ( !$col['idflags'] )
          {
              //Ceci est une erreur de donn�es absentes
              die("Not there : \"$idflags\"");
              return False;
          }
          else
          {
              //$img_out  = imagecreatefromstring( $col[1] ) or die("Cannot Initialize new GD image stream");
              // Sauvegarde
              //imagepng($img_out, $original) or die ("Cannot write cached racemap");
              file_put_contents($original, $col['flag'], FILE_BINARY  | LOCK_EX) or die ("Cannot write cached racemap");

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
    
    $ret = wrapper_mysql_db_query ($req) or die (mysql_error());
    return $ret;
}

function getRacemap($idraces, $force = 'no') {

    $image = "regate".$idraces;
    $original = DIRECTORY_RACEMAPS . "/" . $image . ".jpg";
    
    // Cr�ation et mise en cache de la racemap si elle n'existe pas ou est trop vieille
    if ( 
         ( ! file_exists($original) ) 
          ||  ($force == 'yes')
       ) {
    
          $req = "SELECT idraces, racemap ".
                 "FROM racesmap WHERE idraces = '".$idraces."'";
          $ret = wrapper_mysql_db_query ($req) or die (mysql_error ()); // ceci est une erreur "syst�me" / applicative
          $col = mysql_fetch_array ($ret, MYSQL_ASSOC);
          if ( !$col['idraces'] )
          {
              //Ceci est une erreur de donn�es absentes
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

/* Insert a racemap image $racemapfile for race $idraces  */
function insertRacemap($idraces, $racemapfile) {
    if (! file_exists($racemapfile) ) {
        return False;
    } else {
        $img_blob = file_get_contents ($racemapfile);
        $req = "REPLACE INTO racesmap ( idraces, racemap ".
                  ") VALUES ( ".
                  "".$idraces." , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query ($req) or die (mysql_error ());
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
        $ret = wrapper_mysql_db_query ($req) or die (mysql_error ());
        return True;
    }
}


function raceExists($race)
{
  //find a race
  $query = 'SELECT idraces FROM races WHERE idraces = "'.$race.'"';

  $result = wrapper_mysql_db_query($query)  ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if (!$row) {
    return FALSE;
  } else {
    return TRUE;
  }
}

/*return true if login already exist*/
function boatExists($boat)
{
  //find a boat
  $query = 'SELECT idusers FROM users WHERE idusers = "'.$boat.'"';

  $result = wrapper_mysql_db_query($query)  ;
  $row = mysql_fetch_array($result, MYSQL_NUM);
  if (!$row) {
    return FALSE;
  } else {
    return TRUE;
  }
}

/*return true if login already exist*/
function checkLoginExists($login)
{
  $query2 = 'SELECT idusers FROM users WHERE username = "'.$login.'"';
  $result2 = wrapper_mysql_db_query($query2);

  return ($row2=mysql_fetch_array($result2, MYSQL_NUM));
}

/*create a new account with default values and return idusers*/
function createAccount($log, $pass, $mail, $country)
{
  $query3 = "INSERT INTO `users` ( `boattype` , `username` , `password` , `email`,"
    ."`boatname`, `color`, `boatheading`, `pilotmode`, `engaged` )"
    ."VALUES ( 'boat_imoca60', '$log', '$pass', '$mail', 'boat', '000000', '0', '1', '0')";
  $result3 = wrapper_mysql_db_query($query3);// or die("Query [$query3] failed \n");

  //is there another solution than reread from db?
  $query4 = "SELECT idusers FROM users WHERE username = \"$log\" ";
  $result4 = wrapper_mysql_db_query($query4);// or die($query4);
  $row4 = mysql_fetch_array($result4, MYSQL_NUM);
  return ($row4[0]);
}


function login($idus, $pseudo)
{
  //echo "calling login with $idus and $pseudo\n";
  //if (!isset($_SESSION['idusers']))
  {
    session_start();
    $_SESSION['idu'] = $idus;
    $_SESSION['loggedin'] = 1;
    $_SESSION['login'] = $pseudo;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $IP = $_SERVER['HTTP_X_FORWARDED_FOR']; 
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
      $IP = $_SERVER['HTTP_CLIENT_IP'];   
    } else {
      $IP = $_SERVER['REMOTE_ADDR'];  
    }
    // affiche l'IP
    $_SESSION['IP']=$IP;
  }
}

function logout()
{

  $_SESSION = array();
  if (isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time()-42000, '/');
  session_destroy();

}

function isLoggedIn()
{
  return (isset($_SESSION['idu']));
}

function getLoginName()
{
  return ($_SESSION['login']);
}

function getLoginId()
{
  return ($_SESSION['idu']);
}

function getTheme()
{
   if (isLoggedIn() ) {
      //Connect�
      if ( isset($_SESSION['theme']) ) {
          //On utilise la session
          return ($_SESSION['theme']);
      } else {
          //La premi�re fois, la session ne contient pas le theme
          $users = new users(getLoginId());
          if ( $users->engaged != 0 ) {
              //Le joueur est engag� dans une course
              $race = new races($users->engaged);
              if ( !is_null($race->theme) and (strlen($race->theme) > 1)) {
                  //La course poss�de un th�me, on l'utilise
                  return ( $race->theme);
              }
          }
      }
      // Dans tous les autres cas ou on est identifi�, on renvoie le th�me de l'utilisateur (�ventuellement 'default')
      return ( $users->theme );
   }
   //Non connect�, on utilise le th�me par defaut
   return ( "default" );

}

function setUserPref($idusers,$pref_name,$pref_value, $save=true)
{
  if ($idusers != "" and $save) {
    $query_pref = "REPLACE into user_prefs (idusers, pref_name, pref_value) " . 
      " VALUES ( " . $idusers . 
      ", " .     " '" . $pref_name .  "', '" . $pref_value . "')" ;
    $result_pref = wrapper_mysql_db_query($query_pref) or die($query_pref);
    return (0);
  }
}

function getUserPref($idusers,$pref_name)
{
  //printf("IDU=%s, PN=%s\n", $idusers,$pref_name);
  if ($idusers != "") {
    $query_pref = "SELECT pref_value FROM user_prefs WHERE idusers = $idusers AND pref_name = '$pref_name'";
    $result_pref = wrapper_mysql_db_query($query_pref) or die($query_pref);
    if ( $row_pref = mysql_fetch_array($result_pref, MYSQL_NUM) ) {
      $pref_value = $row_pref[0];
    } else {
      $pref_value = NOTSET;
    }
    //printf("Q=%s\n", $query_pref);
    return ($pref_value);
  }
}

function listUserPref($idusers)
{
  if ($idusers != "") {
    $prefs=array();
    $query_pref = "SELECT pref_name, pref_value FROM user_prefs WHERE idusers = $idusers ORDER BY pref_name";
    $result_pref = wrapper_mysql_db_query($query_pref) or die($query_pref);
    while ( $row = mysql_fetch_array($result_pref, MYSQL_NUM) ) {
      $prefs[$row[0]]=$row[1];
    }
    return($prefs);
  }
}

function getBoatPopularity($idusers, $idraces=0)
{
  $pop=0;
  if ($idusers != "") {
    $query = "select pref_value from user_prefs ";
    $query .= " where pref_name='mapPrefOpponents'";
    if ( $idraces != 0 ) {
      $query .= " and idusers in (select idusers from users where engaged = $idraces)";
    }
    $result = wrapper_mysql_db_query($query) or die($query);
    while ( $row = mysql_fetch_array($result, MYSQL_NUM) ) {
      $arr=explode(',' , $row[0]);
      if ( in_array($idusers, $arr) ) $pop++;
    }
    return($pop);
  }
}

function getOldDuration($idraces,$idusers)
{
  $query_duration = "SELECT duration FROM races_results WHERE idusers = $idusers AND idraces = $idraces";
  $result_duration = wrapper_mysql_db_query($query_duration); // or die($query_duration);
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
  $result_winner = wrapper_mysql_db_query($query_winner); // or die($query_winner);

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
    "   AND idusers    = $idusers " ;
  //echo $query_wptime;

  $result_wptime = wrapper_mysql_db_query($query_wptime); // or die($query_wptime);

  if ( $row_wptime = mysql_fetch_array($result_wptime, MYSQL_NUM) ) {
    $wptime = $row_wptime[0];
  } else {
    $wptime = -1;
  }
  return ($wptime);
}


function getWaypointBestTime($idraces,$idwaypoint)
{
  // Recherche temps de passage du meilleur � un waypoint
  $query_wptime = "SELECT idusers, `time` - `userdeptime`" . 
    "  FROM waypoint_crossing " .
    " WHERE idraces = $idraces " .
    "   AND idwaypoint = $idwaypoint " .
    " ORDER by `time` - `userdeptime` ASC limit 1";


  //echo $query_wptime;

  $result_wptime = wrapper_mysql_db_query($query_wptime) ; //or die($query_wptime);

  if ( $row_wptime = mysql_fetch_array($result_wptime, MYSQL_NUM) ) {
    $wptime = array($row_wptime[0],$row_wptime[1]);
  } else {
    $wptime = array("N/A","N/A");
  }
  return ($wptime);
}

// For a finished race, to give the Palmares
function getRaceRanking($idusers, $idraces) {
  // search for old races for this player
  $query = "SELECT idusers,position from races_results where idraces = " . $idraces . " order by position DESC, duration ASC" ;
  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
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
    if ( $position = BOAT_STATUS_HC ) $commentaire = " (HC)";
    if ( $position = BOAT_STATUS_HTP ) $commentaire = " (HTP)";
    if ( $position = BOAT_STATUS_DNF ) $commentaire = " (DNF)";
    if ( $position = BOAT_STATUS_ABD ) $commentaire = " (ABD)";
    return ($rank . $commentaire );
  } else {
    return ($rank . "/" . $nbu . $commentaire );
  }
}

// Race is up for some boats, we want to display the boat position
function getCurrentRanking($idusers, $idraces) {

  $rank_ar = getCurrentUserRanking($idusers, $idraces);

  return ($rank_ar[0] . "/" . $rank_ar[1]);
}

function getCurrentUserRanking($idusers, $idraces) {
  // search for old races for this player
  $query = "SELECT idusers from races_ranking where idusers >0 and idraces = " . $idraces . " order by nwp DESC, dnm ASC" ;
  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
  $nbu=0;
  while ($row = mysql_fetch_array($result, MYSQL_NUM) ) {
    if( $row[0] == $idusers ) $rank=$nbu+1;
    //printf ("IDU=%d, RANK=%d<BR>\n", $row[0], $nbu);
    $nbu++;
  }
  // we do add num_arrived boats to each counters
  $query = "SELECT count(*) from races_results where position = " . BOAT_STATUS_ARR . 
    " AND idraces = " . $idraces;
  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
  $row = mysql_fetch_array($result, MYSQL_NUM);
  $nb_arr= $row[0];
  $nbu+=$nb_arr;
  $rank+=$nb_arr;

  return array ($rank, $nbu);
}

function findNearestOpponents($idraces,$idusers,$num) {

  $ret_array=array();
  // search for nwp and dnm of this player
  $query = "SELECT nwp, dnm from races_ranking where idraces = $idraces and idusers=$idusers;";
  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
  if ( $row = mysql_fetch_array($result, MYSQL_NUM)) {

    $nwp=$row[0];
    $dnm=$row[1];
    $query = "SELECT idusers from races_ranking 
             where idraces=$idraces 
         and nwp=$nwp 
       order by abs($dnm - dnm) asc 
       limit " . $num .";"    ;

    $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
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
  
  $query = "SELECT idusers from races_ranking 
             where idraces=$idraces 
                     and idusers >1
       order by nwp desc, dnm asc 
       limit " . $num .";"    ;

  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

    array_push ($ret_array, $row[0]);

  }

  return($ret_array);
}

function displayPalmares($idusers) {

  // search for old races for this player
  $query = "SELECT idraces from races_results where idusers = " . $idusers ;
  $result = wrapper_mysql_db_query($query) or die("Query failed : " . mysql_error." ".$query);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $racesObj= new races($row[0]);
    printf ("%d: %s, Classement = %s<br />", $row[0],$racesObj->racename,getRaceRanking($idusers,$row[0]));
    // Le classement

  }
  printf ("<br />");
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
  $result = wrapper_mysql_db_query($query);
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
  $result = wrapper_mysql_db_query($query);
  while($row = mysql_fetch_array($result, MYSQL_NUM)) {
    
    //$racesObj = new races( $row[0] )  ;
    //if ( $racesObj->depend_on == 0  or  userFinishedThisRace($idusers, $racesObj->depend_on) ) {
    //if ( $row[1] == 0 or userFinishedThisRace($idusers, $row[1]) ) {

    // Max inscrits ?
    list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($row[0]);
    if ( $row[3] != 0 && $num_engaged >= $row[3] ) {
      continue;
    }

    // si pas de course de qualification, on ajoute
    if ( $row[2] == "" ) {
      array_push ($records, $row[0]);
    } else {
      // Sinon, on v�rifie que le bateau est qualifi� (a fini une courses de qualif)
      $qualraces = explode(' ', $row[2]);
      foreach ($qualraces as $qr) {
        if ( userFinishedThisRace($idusers, $qr ) ) {
          array_push ($records, $row[0]);
          break;
        }
      }
    }
  }
  
  return ($records);
}

function checkMapArea($value) {
  if (isset($_COOKIE['maparea']) && $_COOKIE['maparea']==$value ) {
    printf("checked");
  }
}

function logUserEvent($idusers, $ipaddr, $idraces, $action ) {
    //tracking...
    $query_user_event = "insert into user_action (time, idusers, ipaddr, idraces, action) " .
                        " values (" . time() . "," . $idusers . ", '" . $ipaddr . "' ," . $idraces .
                        ",'" . addslashes($action) . "')";
    $result = wrapper_mysql_db_query($query_user_event) or die("Query [$query_user_event] failed \n");
}

function htmlQuery($sql) {
    $result = wrapper_mysql_db_query($sql) or die("<h3 class=\"admin-error\">Query".query_user_event."] failed</h1>");
    
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
        $oddeven = $oddeven % 2;
        echo "<tr class=\"admin-query-$oddeven\">";
        for($i=0;$i<count($row); $i++) {
            echo "<td class=\"admin-query\">";
            echo $row[$i];
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";    

}

?>