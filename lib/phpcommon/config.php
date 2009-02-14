<?php

/************** restricted pages ******/
$restrictedPages = array("/modify.php", "/myboat.php", "/mappref.php", "/mercator1.php", "/subscribe_race.php", "/pilototo.php");

/************** localDBPages pages : HAS TO BE SET BEFORE INSLUSION OF PARAM.PHP ******/
$localDBPages = array("/mercator.php", "/races.php");
$engineOnlyScripts = array("new-update-wind.php", "undate-wind.php");


include_once("param.php");

// The full path of the gshhs coast file in use (the "full" version)
define("GSHHS_FILENAME", "".GSHHS_DIRECTORY."/gshhs_f.b");

/********session params***********/
#ini_set('session.use_only_cookies', 1);
#ini_set('session.use_trans_sid', 1);
ini_set('arg_separator.output', "&amp;");


/*********db_connect****************/
$link = mysql_connect(DBSERVER, DBUSER, DBPASSWORD)
   or die("Could not connect : " . mysql_error());
   
mysql_select_db(DBNAME) or die("Could not select database");

// EMAIL COMITE
define("EMAIL_COMITE_VLM", "vlm@virtual-winds.com");

/******** MAP_SERVER_URL A VERIFIER AVANT MISE EN PROD *****************/
//define("VMG_SERVER_URL", "http://www.virtual-loup-de-mer.org/vmg/vmg_vlm.php");
define("VMG_SERVER_URL", "/vmg/vmg_vlm.php");
define("MAP_SERVER_URL", "");
//define("MAP_SERVER_URL", "http://map.virtual-loup-de-mer.org");
define("CHAT_SERVER_URL", "http://chat.virtual-loup-de-mer.org");
define("GRIB_SERVER_URL", "http://grib.virtual-loup-de-mer.org");
define("GRIB_TOOLS_URL", "http://wiki.virtual-loup-de-mer.org/index.php/Outils_m%C3%A9t%C3%A9os");
// Max position age (engine speedup on long races) => 1 week since whe have "histpos" table
define("DEFAULT_POSITION_AGE", 1 * 3600);
define("MAX_POSITION_AGE", 1 * 604800);
// Max number of boats on each map
define("MAX_BOATS_ON_RANKINGS", 100);
define("MAX_BOATS_ON_MAPS", 31);
define("MAX_MAP_X", 2000);
define("MAX_MAP_Y", 2000);
define("WP_THICKNESS", 3);
define("MAILLE_MIN", 0.25);
define("MAILLE_MAX", 9);
define("MAPAREA_MIN", 0.1);
define("MAPAREA_MAX", 300);
define("DEFAULT_SEA_COLOR", "e0e0f0");
define("ALTERNATE_SEA_COLOR", "4040f0");
define("TRANSPARENT_SEA_COLOR", "fefefe");

define("MAX_SPEED_FOR_RANKING", 40);

/********Constants*************/
define("CRONVLMLOCK", "".VLMTEMP."/cronvlm.lock");
define("IMAGE_SITE_PATH", "images/site/");
define("BOAT_IMAGE", "deck-small.png");
define ("COMPASS_IMAGE", "compass-small-complete.png");
#define ("COMPASS_IMAGE", "compass-small-complete.gif");
define ("BUOY_N", "buoy_north.png");
define ("BUOY_S", "buoy_south.png");
define ("BUOY_W", "buoy_west.png");
define ("BUOY_E", "buoy_south.png");

define ("DIRECTORY_COUNTRY_FLAGS","images/pavillons");
define ("DIRECTORY_THEMES","style");

define("MS2KNT" , 3600/1852); //factor from ms to knots
define("MILDEGREE2NAUTICS", 1000/60);

// Distance entre bouée1 et bouée2 imaginaire sur les wayoints
// 2000 milles pour DO_THEY_CROSS
// mercator.img.php s'intéresse au type de WP et ne prend
// que 1/100 de la longueur WPLL
// le type de WP est positionné dans la structure "waypoints" par "races.class".
define("WPLL", 2000);
define("WP_NUMSEGMENTS", 1000);
// long1!=long2 ou lat1!=lat2
define("WPTYPE_PORTE", 1);
// long1=long2 et lat1=lat2
define("WPTYPE_WP", 2);
// ==> Dans la carto, toute porte de plus de 100 milles de large n'est représentée que par sa première bouée

// diametre des bouées des WP et Portes
define("WP_BUOY_SIZE", 6);
// diametre des bateaux réels
define("BOAT_SIZE", 12);

//size of the point on the map
define("POSITIONSIZE", 15);
define("POSITIONWIDTH", 6);

//size of the wind arrows
define("WINDARROW_MINSIZE", 4);
define("WINDARROW_MINWIDTH", 0);

//time in seconds between two updates. It is just to compute the next update
//"real" update delay is in the crontab 
//define("DELAYBETWEENUPDATE", 3 * 3600);
define("DELAYBETWEENUPDATE", 300);
//define("DELAYBETWEENUPDATE", 300);
// Distance around the boat for which we select coast points in the engine
// when evaluating if a boat crosses the coast. (in degrees) 
// (0.1 deg = 6 milles / 0.05deg = 3 milles => OK meme a 30 noeuds avec MAJ toutes les 5(2.5nm) / 10(5nm) minutes )
// Si MAJ moins fréquente que toutes les 10 minutes, passer à 0.2 (sinon DTC aura du mal)
define("DISTANCEFROMROCKS", 0.1);
// Next one is in nautical milles
define("DISTANCEFROMTARGETWP", 1);
// See check_coast_crossing
//define("NUM_SUBVECTORS", 15); // N'est plus utilisé si on tient compte de la vitesse uniquement.
define("NUM_REF_POINTS",  7);
define("NUM_NEAR_POINTS", 2);

// Let it to one hour for this time
define("MAX_DURATION", 315360000);
define("MAX_STOPTIME", 3*86400);

// Define the 3 pilotmodes
define("PILOTMODE_HEADING", 1);
define("PILOTMODE_WINDANGLE", 2);
define("PILOTMODE_ORTHODROMIC", 3);
define("PILOTMODE_BESTVMG", 4);
define("PILOTMODE_BESTSPEED", 5);

// Define the boat status (ARRIVE, HORS TEMPS, DNF, ABANDON)
define("BOAT_STATUS_ARR", 1);
define("BOAT_STATUS_HTP", -1);
define("BOAT_STATUS_DNF", -2);
define("BOAT_STATUS_ABD", -3);
define("BOAT_STATUS_HC", -4);

define ("PILOTOTO_PENDING", "pending");
define ("PILOTOTO_DONE", "done");
// Pilototo tasks conservation
define ("PILOTOTO_KEEP", 7*86400);
define ("PILOTOTO_MAX_EVENTS", 5);

// Type de courses
define ("RACE_TYPE_CLASSIC",0);
define ("RACE_TYPE_RECORD", 1);

//constant for speedchart
//knots between two graduations
define("STEP", 2); 
//pixels between two graduations
define("STEPSIZE", 12);

// NOTSET (for user_prefs)
define("NOTSET", "NULL");

// CLASS_ADMIN
define("CLASS_ADMIN","admin");

//constant for orthodromic route calculation
//TODO check if deprecated
define("ORTHOSTEP", 2);
define("ORTHOMAX", 50);

//default position
define("DEFAULT_LONG", -30000);
define("DEFAULT_LAT", 50000);

// default limits
define("MAX_WEST", -180000);
define("MAX_EAST", 180000);
define("MAX_NORTH", 80000);
define("MAX_SOUTH", -80000);

//IC FLAGS
define("IC_FLAG_VISIBLE") = 1;
define("IC_FLAG_CONSOLE") = 2;

// Par défaut ( Atlantique nord seulement )
//define("COASTLINE_DATAFILE", "N50E15S10W80@1_5000000.dat");
//define("ISLANDS_DATAFILE", "mapFillCoordinates.dat");

// Globe entier 1/5000000
//define("COASTLINE_DATAFILE", "fullglobe-coastline.dat");

// Plus détaillé, mais ingérable en l'état
//define("COASTLINE_DATAFILE", "shoreline.dat");

include_once("functions.php");
include_once("f_windAtPosition.php");
include_once("users.class.php");
include_once("races.class.php");
include_once("positions.class.php");
?>
