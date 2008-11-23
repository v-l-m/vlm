<?php
// Nouvelle fonction de cartographie

// Plus de coordonnées N, S, W, E, ni x et y, mais 
//	- un couple latitude/longitude (centre de la carte)
//	- une taille en largeur : x   ==> la hauteur y est déduite à partir de X (16/10)
//	- un niveau de zoom : (en fait un nombre de degrés à couvrir en longitude)
//	      ==> l'amplitude nord-sud est calculée pour être égale celle west-est/2
//		 ==> + tant qu'on dépasse 80°nord ou 80° sud, zoom--
// projection : proj=mercator, proj=lambert, proj=carre

// PARAMETRES :
//   longitude,latitude,width,zoom,proj   + ceux qu'on avait déjà pour .

include_once("config.php");
include_once("map.class.php");

// Doit on poster un header HTTP ou pas
$noHeader=htmlentities($_GET['noHeader']);
if ($noHeader !=1) {
  header("Content-type: image/png");
}
// Type de projection
$proj=htmlentities($_GET['proj']);

// Taille de la carte 
$x=htmlentities($_GET['x']);
// Limitation de la taille de la carte pour pas péter le serveur 
if ( $x > MAP_MAXWIDTH ) $x=MAP_MAXWIDTH;
// On déduit Y à partir de X:  16/10 (voir si nécessaire passage en 4/3)
$y=$x / (MAP_RATIO) ;

// Quelle course, quels bateaux ?
$idraces=htmlentities($_GET['idraces']);
$list=$_GET['list'];

// Si course finie, prendre les bateaux sans s'intéresser à la course
// à laquelle ils sont actuellement inscrits
$raceover=htmlentities($_GET['raceover']);

// Libellé des vents ou pas
$windtext=htmlentities($_GET['windtext']);

// Pour le tracé des trajectoires
$text=htmlentities($_GET['text']);
$tracks=htmlentities($_GET['tracks']);
$age=htmlentities($_GET['age']);
if ( $age == 0 ) $age = 86400;

// Position du centre de la carte, et niveau de zoom ==> Donc bornes
$latitude= htmlentities($_GET['latitude'] );
$longitude=htmlentities($_GET['longitude']);
$zoom=htmlentities($_GET['zoom']);


//all the values submitted are stored in a cookie
$save=htmlentities($_GET['save']);
if ($save == "on")
{
  setcookie("north", $north, time()+3600*24*365); //expire in one year
  setcookie("south", $south, time()+3600*24*365);
  setcookie("east", $east, time()+3600*24*365);
  setcookie("west", $west, time()+3600*24*365);
  setcookie("x", $x, time()+3600*24*365);
  setcookie("y", $y, time()+3600*24*365);
  if ( $list != "" ) {
  //	setcookie("list", implode(",", $list), time()+3600*24*365);
  } 
  setcookie("proj", $proj, time()+3600*24*365);
  setcookie("text", $text ,time()+3600*24*365);
  setcookie("tracks", $tracks, time()+3600*24*365);
}

// latitude, longitude, et zoom sont convertis en millidegrés, maintenant qu'on a écrit le cookie
$latitude *=1000;
$longitude *=1000;
$zoom *=1000;

//  ==> + tant qu'on dépasse 80° nord ou 80° sud, on réduit le ZOOM
//            NORTH			     SOUTH
while ( $latitude+$zoom > MAX_NORTH || $latitude-$zoom < MAX_SOUTH ) {
	$zoom-=1;
}
// On peut établir les bornes de la carte
//  ==> l'amplitude WEST-EST est calculée pour être égale à MAP_RATIO * (NORD - SUD)
$north=$latitude+$zoom;
$south=$latitude-$zoom;
$west=$longitude-($zoom*MAP_RATIO);
$east=$longitude+($zoom*MAP_RATIO);


//warning
//when transferring an array by GET or POST
//if it is empty, the resulting var is NULL and it break everthing
if ($list == NULL) {
     $list = array();
     array_push($list, htmlentities($_GET['boat']));
}


if ($list=="all")
{
  $list = array();
  $fullRacesObj = new fullRaces($idraces); 
  if ( $raceover == "true") {
  	foreach ($fullRacesObj->excluded as $excl)
  		array_push($list, $excl->idusers);
  } else {
  	foreach ($fullRacesObj->opponents as $opp)
		array_push($list, $opp->idusers);
  }
  //print_r($list);
}

// Main ()
$mapObj = new map($list, $proj, $text, $tracks, $north, $south, $east, $west, $idraces, $x, $y, $windtext);
$mapObj->drawMap($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
$mapObj->drawWind($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
$mapObj->drawRaces($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y');
if ( $raceover == "true") 
{
	$mapObj->drawExcludedPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $idraces);
} else {
	$mapObj->drawPositions($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', $age);
}
$mapObj->display();

?>
