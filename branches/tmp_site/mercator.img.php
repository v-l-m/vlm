<?php
include_once("config.php");
include_once("mapfunctions.php");
include_once("map.class.php");


//================================================================
// MAIN
//================================================================

$maparea=round(htmlentities($_GET['maparea']),1);
$maille=round(htmlentities($_GET['maille']),1);

// ajustement du niveau de zoom (0.. 20/21)
$maparea=max(exp($maparea/2.5)/10, MAPAREA_MIN);
if ($maparea > MAPAREA_MAX ) $maparea=MAPAREA_MAX;
//echo "MAPAREA=".$maparea; exit;

// La maille
if ( $maille <= 0 || !isset($maille) ) $maille=MAILLE_MIN;
//$maille=max(round(sqrt($maille)*sqrt($maparea-MAPAREA_MIN),1),MAILLE_MIN);
$maille=max(round(sqrt(($maille)/20)+sqrt($maparea/20),1),MAILLE_MIN);

if ( $maille > MAILLE_MAX ) $maille=MAILLE_MAX;
//echo "MAILLE=".$maille; exit;


$age=max(0,htmlentities($_GET['age']));
$estime=max(0,htmlentities($_GET['estime']));

$coasts=trim(htmlentities($_GET['coasts']));

// Taille de la carte
$x=max(100,htmlentities($_GET['x']));
$y=max(100,htmlentities($_GET['y']));
// Limitation de la taille de la carte pour pas péter le serveur 
if ( !idusersIsAdmin(htmlentities($_GET['boat'])) ) {
	if ( $x > MAX_MAP_X ) $x=MAX_MAP_X;
	if ( $y > MAX_MAP_Y ) $y=MAX_MAP_Y;
}

// On reçoit maintenant un point de coordonnées du centre de la carte
$lat=htmlentities($_GET['lat']);
$long=htmlentities($_GET['long']);

// On limite les bornes de la carte (pour les problèmes de coloriage...
$mapCoords=coordCarte($lat,$long,$maparea,$y,$x);

$north=$mapCoords[0];
$south=$mapCoords[1];
$west=$mapCoords[2];
$east=$mapCoords[3];

while ( $west > 360 ) {
	$west -=360;
}
while ( $east > 360 ) {
	$east -=360;
}


///printf ("N=%f, S=%f, W=%f, E=%f\n", $north, $south, $west, $east);
// Gestion des cartes autour de Day Changing Line

//$libmap=sprintf ("Map Center : lat=%4.3f,long=%4.3f, Map Borders : N=%4.3f,S=%4.3f,W=%4.3f,E=%4.3f", $lat,$long,$north,$south,$west,$east);
$libmap=sprintf ("N=%4.3f, S=%4.3f, W=%4.3f, E=%4.3f", $north,$south,$west,$east);
// Maintenant que le libellé est prêt, on va convertir les cordonnées en cas d'antemeridien
if ( $west > 0 && $east <0 ) {
	$east +=360;
}

$proj=htmlentities($_GET['proj']);
if ( $proj == "" ) $proj="mercator";

$idraces=round(htmlentities($_GET['idraces']));

$list=$_REQUEST['list'];
if ( is_numeric($list) ) {
	$list=array($list);
}
$text=htmlentities($_GET['text']);
$save=htmlentities($_GET['save']);
$tracks=htmlentities($_GET['tracks']);
$raceover=htmlentities($_GET['raceover']);
$windtext=htmlentities($_GET['windtext']);
$drawrace=htmlentities($_GET['drawrace']);
$drawgrid=htmlentities($_GET['drawgrid']);
$drawmap=htmlentities($_GET['drawmap']);
$drawwind=htmlentities($_GET['drawwind']);
$windonly=htmlentities($_GET['windonly']);
$drawlogos=htmlentities($_GET['drawlogos']);
$drawscale=htmlentities($_GET['drawscale']);
$drawpositions=htmlentities($_GET['drawpositions']);
$drawortho=htmlentities($_GET['drawortho']);
$drawlibelle=htmlentities($_GET['drawlibelle']);
$fullres=htmlentities($_GET['fullres']);
/*
if ( $maparea > 5 ) {
     $fullres="poly";
}
*/

$timings=htmlentities($_GET['timings']);
$wpnum=floor(htmlentities($_GET['wp']));

// 2 segments + 1 point : pour visualisation des points de croisement de cote
$seg1=htmlentities($_GET['seg1']);
$seg2=htmlentities($_GET['seg2']);
$ec=htmlentities($_GET['ec']);

//warning
//when transferring an array by GET or POST
//if it is empty, the resulting var is NULL and it break everthing
if ($list == "myboat" ) {
     $list = array();
     array_push($list, htmlentities($_GET['boat']));
}
if ( $list == "my5opps" ) {
     $list = array();
     $list = findNearestOpponents($idraces,htmlentities($_GET['boat']),5);
}

if ( $list == "my10opps" ) {
     $list = array();
     $list = findNearestOpponents($idraces,htmlentities($_GET['boat']),10);
}

if ( $list == "meandtop10" ) {
     $list = array();
     $list = findTopUsers($idraces,10);
     array_push ($list, $_GET['boat']);
}

if ( $list == "mylist" ) {
  $list = explode ("," , getUserPref(htmlentities($_GET['boat']),"mapPrefOpponents") ); 
  //print_r($list);
}


// Le pas de temps du vent
if ( $drawwind == "no" ) {
	$drawwind=-1;
}
if ( $drawwind >= 0 ) {
	$drawwind=min($drawwind,96)*3600;
}



// Age (si list = all alors max age = 1 jour)
if ( $age == 0 OR $list == "all" ) {
    $age = min(24*3600,$age*3600);
} else {
    $age = $age*3600;
}

if ( $list=="all" ) {
  $list = array();
  $fullRacesObj = new fullRaces($idraces); 
  if ( $raceover == "true") 
  {
  	foreach ($fullRacesObj->excluded as $excl)
  		array_push($list, $excl->idusers);
  } else {
  	foreach ($fullRacesObj->opponents as $opp)
		array_push($list, $opp->idusers);
  }
  //print_r($list);
}


//COOKIES
//all the values submitted are stored in a cookie
/*
if ($save == "on")
{
  setcookie("north", $north, time()+3600*24*365); //expire in one year
  setcookie("south", $south, time()+3600*24*365);
  setcookie("east", $east, time()+3600*24*365);
  setcookie("west", $west, time()+3600*24*365);
  setcookie("x", $x, time()+3600*24*365);
  setcookie("y", $y, time()+3600*24*365);
  //setcookie("list", implode(",", $list), time()+3600*24*365);
  //setcookie("list", implode(",", $list), time()+3600*24*365);
  setcookie("proj", $proj, time()+3600*24*365);
  setcookie("text", $text ,time()+3600*24*365);
  setcookie("tracks", $tracks, time()+3600*24*365);
}
*/

$north*=1000;
$south*=1000;
$east*=1000;
$west*=1000;


$time_start = time();
$mapObj = new map($list, $proj, $text, $tracks, $north, $south, $east, $west, $idraces, $x, $y, $windtext, $maille, $drawwind, $timings);
$time_stop = time();

if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 20, "Time new map = " . ($time_stop - $time_start) . "s", $mapObj->colorText);

if ( $drawrace != "no" && $windonly != "true" ) {
    if ( $wpnum != 0 ) {
    	$mapObj->wp_only = $wpnum;
    }
    $time_start = time();
    $mapObj->drawRaces($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 70, "Time drawRaces = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}

if ( $drawgrid != "no" && $windonly != "true" ) {
    $time_start = time();
    $mapObj->drawGrid($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 40, "Time drawGrid = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}

if ( $drawmap != "no" && $windonly != "true" ) {
    $time_start = time();
    $mapObj->drawMap($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coasts, $fullres, $maparea);
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 30, "Time drawMap = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}

if ( $drawwind >= 0 ) {
    $time_start = time();
    $mapObj->drawWind($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $drawwind);
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 50, "Time drawWind = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}


if ( $drawscale != "no" && $windonly != "true" ) {
    $time_start = time();
    $mapObj->drawScale($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 60, "Time drawScale = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}


if ( $drawpositions != "no" && $windonly != "true" ) {
    $time_start = time();
    $mapObj->drawRealBoatPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
    if ( $raceover == "true") {
	    $mapObj->drawExcludedPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $idraces, $_GET['boat'], $age, $estime);
    } else {
	    $mapObj->drawPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $age, $estime);
	    if ( $drawortho == "yes" ) {
	        $mapObj->drawOrtho($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $estime);
	    }
    }
    $time_stop = time();
    if ( $timings == "true" ) imagestring($mapObj->mapImage, 2, 30, 80, "Time Positions = ". ($time_stop - $time_start) . "s", $mapObj->colorText);
}

//echo "OK" ; exit;

if ( $drawlogos != "no" && $windonly != "true" ) {
    // Quelques pubs.
    if ( $idraces == 55 ) {
        $logo = @imagecreatefromgif("images/site/banniere_hi5.gif");
        imagecopymerge ( $mapObj->mapImage, $logo, 1 , $mapObj->ySize-60-1, 35, 0, 240, 60, 60 );
    }

    // Logo VLM en haut à droite des cartes.
    $logo = @imagecreatefromjpeg("images/site/banniere_vlm.jpg");
    imagecopymerge ( $mapObj->mapImage, $logo, $mapObj->xSize-320 , 0, 0, 0, 320, 55, 30 );
}

if ( $drawlibelle != "no" && $windonly != "true" ) {
    imagestring($mapObj->mapImage, 5, 10, $y-20, gmdate("Y/m/d H:i:s",time()) . " GMT", $mapObj->colorText);
    imagestring($mapObj->mapImage, 3, $x-200 , 15  ,  "Map Borders" ,$mapObj->colorText);
    imagestring($mapObj->mapImage, 3, $x-300 , 25  ,  $libmap ,$mapObj->colorText);
}

// Dessin d'une croix pour "seg1"
if ( preg_match ("/^.*,.*:.*,.*$/",$seg1) ) {
     $coords_seg1=preg_split('/[,:]/',$seg1);
     $mapObj->drawSegment($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coords_seg1, $mapObj->colorCC, true);
}

if ( preg_match ("/^.*,.*:.*,.*$/",$seg2) ) {
     $coords_seg2=preg_split('/[,:]/',$seg2);
     $mapObj->drawSegment($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $coords_seg2, $mapObj->colorBlack, false);
}

// Display
$noHeader=htmlentities($_GET['noHeader']);
if ($noHeader !=1) {
  header("Content-type: image/png");
}

$mapObj->display();
?>
 


