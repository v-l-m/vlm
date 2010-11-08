<?php

function define_if_not($k, $v) {
    if (!defined($k)) define($k, $v);
}

/************** restricted pages ******/
$restrictedPages = array("/modify.php", "/myboat.php", "/mappref.php", "/mercator1.php", "/subscribe_race.php", "/pilototo.php");

/************** localDBPages pages : HAS TO BE SET BEFORE INCLUSION OF PARAM.PHP ******/

$localDBPages = array(
                "/index.php",
                "/mercator.php",
                "/races.php",
                "/getinfo.php",
                "/gps.php",
                "/minimap.php",
                "/racemap.php",
                "/about.php",
                "/getraceinfo.php",
                "/ics.php",
                "/speedchart.php",
                "/gettrack.php",
                "/faq.php",
                "/races.php",
                "/flagimg.php",
                "/getwind.php",
                "/palmares.php",
                "/getinfo2.php"
);

// Constant defined in param.php have precedence over constant below.
include_once("param.php");

// The full path of the gshhs coast file in use (the "full" version)
define("GSHHS_FILENAME", "".GSHHS_DIRECTORY."/gshhs_f.b");
// The full path of the polar definition filename  (the "full" version)
define("POLAR_DEFINITION_FILENAME", "".POLARS_DIRECTORY."/polars.list");

/********session params***********/
#ini_set('session.use_only_cookies', 1);
#ini_set('session.use_trans_sid', 1);
ini_set('arg_separator.output', "&amp;");

/*********db_connect****************/
define_if_not("DBSLAVESERVER", DBMASTERSERVER); // define if not defined in param.php
define_if_not("DBSLAVEUSER", DBMASTERUSER); // define if not defined in param.php
define_if_not("DBSLAVEPASSWORD", DBMASTERPASSWORD); // define if not defined in param.php
define_if_not("DBMAPSERVER", DBSLAVESERVER); // define if not defined in param.php
define_if_not("DBMAPUSER", DBSLAVEUSER); // define if not defined in param.php
define_if_not("DBMAPPASSWORD", DBSLAVEPASSWORD); // define if not defined in param.php

if (defined('MOTEUR')) {
  $link = mysql_pconnect(DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
          die("Could not connect : " . mysql_error());
  $GLOBALS['masterdblink']=$link;
  $GLOBALS['mapdblink']   =$link; // unused in 'MOTEUR' mode
  $GLOBALS['slavedblink'] =$link;
  mysql_select_db(DBNAME, $link) or die("Could not select database");
} else {
  $link = mysql_connect(DBMASTERSERVER, DBMASTERUSER, DBMASTERPASSWORD) or 
    die("Could not connect : " . mysql_error());
  $GLOBALS['masterdblink']=$link;
  mysql_select_db(DBNAME, $link) or die("Could not select database");
  $link = mysql_connect(DBMAPSERVER, DBMAPUSER, DBMAPPASSWORD) or 
    die("Could not connect : " . mysql_error());
  $GLOBALS['mapdblink']=$link;
  mysql_select_db(DBNAME, $link) or die("Could not select database");
  $link = mysql_connect(DBSLAVESERVER, DBSLAVEUSER, DBSLAVEPASSWORD) or 
    die("Could not connect : " . mysql_error());
  $GLOBALS['slavedblink']=$link;
  mysql_select_db(DBNAME, $link) or die("Could not select database");
} 
   
mysql_select_db(DBNAME, $link) or die("Could not select database");

//PROXY AGENTS AUTH - please overide in param.php
define("PROXY_AGENT_PASS", "PROXYPASS");

// EMAIL COMITE
define_if_not("EMAIL_COMITE_VLM", "vlm@virtual-winds.com");
define_if_not("MAIL_PREFIX", "VLM");

//EMAIL NOTIFY
define_if_not("EMAIL_NOTIFY_VLM", "noreply@virtual-loup-de-mer.org");

/******** MAP_SERVER_URL A VERIFIER AVANT MISE EN PROD *****************/
//define("VMG_SERVER_URL", "http://www.virtual-loup-de-mer.org/vmg/vmg_vlm.php");
define_if_not("WWW_SERVER_URL", "http://www.virtual-loup-de-mer.org");
define("VMG_SERVER_URL", "/vmg/vmg_vlm.php");
define("MAP_SERVER_URL", "");
//define("MAP_SERVER_URL", "http://map.virtual-loup-de-mer.org");
define("CHAT_SERVER_URL", "http://chat.virtual-loup-de-mer.org");
define("GRIB_SERVER_URL", "http://grib.virtual-loup-de-mer.org");
define("DOC_SERVER_URL", "http://wiki.virtual-loup-de-mer.org/index.php/");
define("DEV_SERVER_URL", "http://dev.virtual-loup-de-mer.org/vlm/");
define("TOOLS_SERVER_URL", DOC_SERVER_URL."Les_accessoires_et_outils_autour_de_VLM");
define("GRIB_TOOLS_URL", DOC_SERVER_URL."Outils_m%C3%A9t%C3%A9os");
define("MOBILE_SERVER_URL", "http://mobiles.virtual-loup-de-mer.org");

// Max position age (engine speedup on long races) => 1 week since we have "histpos" table
define("DEFAULT_POSITION_AGE", 1 * 3600);
define("MAX_POSITION_AGE", 1 * 172800);
// Max number of boats on each map
define_if_not("MAX_BOATS_ON_RANKINGS", 100);
define("MAX_BOATS_ON_MAPS", 31);
define("MAX_MAP_X", 2000);
define("MAX_MAP_Y", 2000);
define("WP_THICKNESS", 2);
define("MAILLE_MIN", 0.25);
define("MAILLE_MAX", 9);
define("MAPAREA_MIN", 0.1);
define("MAPAREA_MAX", 300);
define("MAP_POLYLINE_MODE", "polyline");
define("MAP_POLYLINE_FULL_MODE", "poly");
define("MAP_LINE_MODE", "nopoly");
define_if_not("MAP_FULLRES_MODE", MAP_POLYLINE_MODE);
define("DEFAULT_SEA_COLOR", "e0e0f0");
define("ALTERNATE_SEA_COLOR", "4040f0");
define("TRANSPARENT_SEA_COLOR", "fefefe");
define("MAX_SPEED_FOR_RANKING", 40);

//Log parameters
define("MAX_LOG_USER_ACTION_AGE", 168*3600); #1 week
define("MAX_LOG_USER_ACTION_VIEW", 50); #nb actions viewable by the user
define_if_not("SERVER_NAME", "UNDEFINED_VLM_SERVER"); //should be defined in param.php


/********Constants*************/
define("CRONVLMLOCK", "".VLMTEMP."/cronvlm.lock");
define("IMAGE_SITE_PATH", "images/site/");
define("BOAT_IMAGE", "deck-small.png");
define ("COMPASS_IMAGE", "compass-small-complete.png");
define ("BUOY_N", "buoy_north.png");
define ("BUOY_S", "buoy_south.png");
define ("BUOY_W", "buoy_west.png");
define ("BUOY_E", "buoy_south.png");

define ("DIRECTORY_COUNTRY_FLAGS","images/pavillons");
define ("DIRECTORY_RACEMAPS","images/racemaps");
define ("DIRECTORY_THEMES","style");
define ("DIRECTORY_POLARS","Polaires");
define ("DIRECTORY_JSCALENDAR","externals/jscalendar");

//define ("PROFILE_PLAYER_URL", "/palmares.php?type=player&idplayers=");

define("MS2KNT" , 3600/1852); //factor from ms to knots
define("MILDEGREE2NAUTICS", 1000/60);

// Distance entre bouée1 et bouée2 imaginaire sur les wayoints
// 2000 milles pour DO_THEY_CROSS
// mercator.img.php s'intéresse au type de WP et ne prend
// que 1/100 de la longueur WPLL
// le type de WP est positionné dans la structure "waypoints" par "races.class".
define("WPLL", 2000);
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

// Define the 6 pilotmodes
define("MAX_PILOTMODE", 6);
define("PILOTMODE_HEADING", 1);
define("PILOTMODE_WINDANGLE", 2);
define("PILOTMODE_ORTHODROMIC", 3);
define("PILOTMODE_BESTVMG", 4);
define("PILOTMODE_VBVMG", 5);
define("PILOTMODE_BESTSPEED", 6);

// Define strings used (see strings.inc) to describe the pilot mode
$pilotmodeList = Array(1 => "autopilotengaged", 2 => "constantengaged", 3 => "orthoengaged", 4 => "bestvmgengaged", 5 => "vbvmgengaged", 6 => "bestspeed");

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

//Player mode
define ("MAX_BOATS_OWNED_PER_PLAYER", 12); //mesure anti-abus

//constant for speedchart
//knots between two graduations
define("STEP", 2); 
//pixels between two graduations
define("STEPSIZE", 12);

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
define("IC_FLAG_VISIBLE", 1);
define("IC_FLAG_CONSOLE", 2);
define("IC_FLAG_RACELIST", 4);
define("IC_FLAG_LINKFORUM", 8);
define("IC_FLAG_HIDEONICS", 16);

//LINKTYPE FLAG (between players & users)
define("PU_FLAG_BOATSIT", 2);
define("PU_FLAG_OWNER", 1);

//Options du mode players
define_if_not("SERVER_IS_SENDING_EMAIL", True);

//PREFS
// NOTSET (for user_prefs)
define("NOTSET", "NULL");
define("USER_PREF_ALLOWED", "maparea,mapMaille,mapX,mapY,mapAge,mapEstime,mapDrawtextwp,mapOpponents,mapLayers,mapCenter,mapTools,mapPrefOpponents,mobiVlmDatas,blocnote,color,theme,country,boatname");

// WAYPOINTS
define("WP_TWO_BUOYS", 0);
define("WP_ONE_BUOY", 1);
// leave space for 0-15 types of buoys
// next is bitmasks
define("WP_ICE_GATE_N", 16);
define("WP_ICE_GATE_S", 32);
define("WP_ICE_GATE_E", 64);
define("WP_ICE_GATE_W", 128);
// allow crossing in one direction only
define("WP_CROSS_CLOCKWISE", 256);
define("WP_CROSS_ANTI_CLOCKWISE", 512);
// for future releases
define("WP_CROSS_ONCE", 1024);

//TRANSLATIONS
define_if_not("DISPLAY_LANG_WARNINGS", False);

include_once("functions.php");
include_once("f_windAtPosition.php");
include_once("users.class.php");
include_once("races.class.php");
include_once("positions.class.php");
?>
