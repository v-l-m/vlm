<?php
    require_once('config-funcs.php');
    include_once('param.php');

    /************** restricted pages ******/
    $restrictedPages = array("/modify.php", "/myboat.php", "/mappref.php", "/mercator1.php", "/subscribe_race.php", "/pilototo.php", "/edit_boatprefs.php", "/modify_password.php");

    // The full path of the gshhs coast file in use (the "full" version)
    define("GSHHS_FILENAME", "".GSHHS_DIRECTORY."/gshhs_f.b");
    // the full path of the gshhs clipped file (for tiles)
    define("GSHHS_CLIPPED_FILENAME", GSHHS_DIRECTORY."/poly-f-1.dat");
    define("GSHHS_CLIPPED_RIVER_FILENAME", GSHHS_DIRECTORY."/rivers-f-1.dat");
    define("GSHHS_CLIPPED_TOPO_FILENAME", GSHHS_DIRECTORY."/ETOPO1_Ice.dat");

    // The full path of the polar definition filename  (the "full" version)
    define("POLAR_DEFINITION_FILENAME", "".POLARS_DIRECTORY."/polars.list");

    /********session params***********/
    #ini_set('session.use_only_cookies', 1);
    #ini_set('session.use_trans_sid', 1);
    ini_set('arg_separator.output', "&amp;");

    //PROXY AGENTS AUTH - please overide in param.php
    define("PROXY_AGENT_PASS", "PROXYPASS");

    // EMAIL COMITE
    define_if_not("EMAIL_COMITE_VLM", "vlm@virtual-winds.org");
    define_if_not("MAIL_PREFIX", "VLM");

    //EMAIL NOTIFY
    define_if_not("EMAIL_NOTIFY_VLM", "noreply@v-l-m.org");

    /******** MAP_SERVER_URL A VERIFIER AVANT MISE EN PROD *****************/
    //define("VMG_SERVER_URL", "http://www.v-l-m.org/vmg/vmg_vlm.php");
    define_if_not("WWW_SERVER_URL", "http://www.v-l-m.org");
    define("VMG_SERVER_URL", "/vmg/vmg_vlm.php");
    define("MAP_SERVER_URL", "");
    //define("MAP_SERVER_URL", "http://map.v-l-m.org");
    define("GRIB_SERVER_URL", "http://grib.v-l-m.org");
    define("DOC_SERVER_URL", "http://wiki.v-l-m.org/index.php/");
    define("DEV_SERVER_URL", "http://v-l-m.github.io/vlm/");
    define("TOOLS_SERVER_URL", DOC_SERVER_URL."Les_accessoires_et_outils_autour_de_VLM");
    define("GRIB_TOOLS_URL", DOC_SERVER_URL."Outils_m%C3%A9t%C3%A9os");
    //define("DOC_SERVER_URL_BO", DOC_SERVER_URL."Vocabulaire_%26_Jargon#BlackOut_.28BO.29");
    define("MOBILE_SERVER_URL", "http://mobiles.v-l-m.org");
    define("FORUM_SERVER_URL", "http://www.virtual-winds.org/forum/index.php?/forum/276-virtual-loup-de-mer/");

    // Max position age (engine speedup on long races) => 2 days since we have "histpos" table
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
    define("MAX_SPEED_FOR_RANKING", 60);

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

    //Those are relatives to the document root
    define ("DIRECTORY_GSHHSTILES", "cache/gshhstiles"); #see VLMCACHE parameter in your conf script
    define ("DIRECTORY_GRIBTILES", "cache/gribtiles"); #see VLMCACHE parameter in your conf script
    define ("DIRECTORY_COUNTRY_FLAGS","cache/flags");
    define ("DIRECTORY_RACEMAPS","cache/racemaps");
    define ("DIRECTORY_MINIMAPS","cache/minimaps");
    define ("DIRECTORY_TINYMAPS","cache/tinymaps");
    define ("DIRECTORY_TRACKS", $_SERVER['DOCUMENT_ROOT']."/cache/tracks");
    define ("DIRECTORY_THEMES","style");
    define ("DIRECTORY_POLARS","Polaires");
    define ("DIRECTORY_JSCALENDAR","externals/jscalendar");
    
    // PATH for Grib files from windserver
    define ("DIRECTORY_GRIBFILES","/home/vlm/vlmdatas/gribs");


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
    define("UPDATEDURATION", 10); //Temps considéré comme maximum pour que le moteur tourne - a ajuster suivant le nombre de joueur de vlm et les perfs du moteur.
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
    $pilotmodeList = Array(1 => "autopilotengaged", 2 => "constantengaged", 3 => "orthoengaged", 4 => "bestvmgengaged", 5 => "vbvmgengaged"); //, 6 => "bestspeed");

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

    // Bitmask pour spécifier les types de course
    define ("RACE_TYPE_CLASSIC",0); //For the record
    define ("RACE_TYPE_RECORD", 1);
    define ("RACE_TYPE_OMORMB", 2); // Deactivate OMOROB (MB = Many boats)

    // Race codes
    define ("RACE_ENDED"  , -1);
    define ("RACE_PENDING",  0);
    define ("RACE_STARTED",  1);

    //Tracks parameters
    define ("RACE_EXPORT_DURATION", 180*86400); // ~6 monthes

    //Player mode
    define ("MAX_BOATS_OWNED_PER_PLAYER", 12); //mesure anti-abus

    //constant for speedchart
    //knots between two graduations
    define("STEP", 2); 
    //pixels between two graduations
    define("STEPSIZE", 12);

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
    define("MAX_NORTH", 85000);
    define("MAX_SOUTH", -85000);

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
    define("USER_PREF_ALLOWED", "maparea,mapMaille,mapX,mapY,mapAge,mapEstime,mapDrawtextwp,mapOpponents,mapLayers,mapCenter,mapTools,mapPrefOpponents,mobiVlmDatas,blocnote,color,theme,country,boatname,frogDatas,qtvlmDatas,sbsrouteurDatas");
    //USERS PREFS that can be larger than 255 chars.
    define_if_not("LARGE_USER_PREF_ALLOWED", "frogDatas,qtvlmDatas,sbsrouteurDatas");    
    define("PLAYER_PREF_ALLOWED", "lang_ihm,lang_communication,contact_email,contact_jabber,contact_taverne,contact_fmv,contact_revatua,contact_twitter,contact_facebook,contact_msn,contact_googleplus");

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

    //TESTING FLAG
    define_if_not("DISPLAY_LANG_WARNINGS", False);
    define_if_not("ALLOW_ACCOUNT_CREATION", True);

    //WIND management
    define_if_not('MAX_WIND_POINTS', 2048);

    //VLM NOTIFY
    define_if_not("VLM_NOTIFY_FACEBOOK_URL", "https://graph.facebook.com/201999359896107/feed");
    define_if_not("VLM_NOTIFY_FACEBOOK_ACCESSTOKEN", "big_hashed_string");

    define_if_not("VLM_NOTIFY_TWITTER_CONSUMER_KEY", "big_hashed_string");
    define_if_not("VLM_NOTIFY_TWITTER_CONSUMER_SECRET", "big_hashed_string");
    define_if_not("VLM_NOTIFY_TWITTER_OAUTH_TOKEN", "big_hashed_string");
    define_if_not("VLM_NOTIFY_TWITTER_OAUTH_TOKEN_SECRET", "big_hashed_string");

    define_if_not("VLM_NOTIFY_LIST", "test");
    define_if_not("VLM_NOTIFY_NEWS_MAX_AGE", 14*24*3600); #2 weeks
    define_if_not("VLM_NOTIFY_MAIL", EMAIL_COMITE_VLM);
    define_if_not("VLM_NOTIFY_IRC_SERVER", "irc.epiknet.org");
    define_if_not("VLM_NOTIFY_IRC_CHAN", "#vlm");
    define_if_not("VLM_NOTIFY_IRC_USER", "vlm[POSTMAN]");
    define_if_not("VLM_NOTIFY_JABBER_USER", "arsenal");
    define_if_not("VLM_NOTIFY_JABBER_PASS", "pass");
    define_if_not("VLM_NOTIFY_JABBER_MAIN", "capitainerie@vhf.iridium.v-l-m.org");
    define_if_not("VLM_NOTIFY_JABBER_ADMINS", "comite@vhf.iridium.v-l-m.org");


    define_if_not("WS_DEFAULT_CACHE_DURATION", 0); //Default is no cache, but it's overiden on a by service basis (look the code, luke)
    define_if_not("WS_MAX_MAXAGE", 2592000); // Default is no max (can be overriden to "cap" globally the cache setup by ws)
    define_if_not("WS_MIN_MAXAGE", 0); // Default is no minimum (can be overriden to lighten the server load)
    define_if_not("WS_NSZ_CACHE_DURATION", 24*3600);
    define_if_not("WS_PLAYER_LIST_CACHE_DURATION", 3600);

    //Jabber
    define_if_not("VLM_XMPP_ON", false);
    define_if_not("VLM_XMPP_HOST", "iridium.v-l-m.org");
    define_if_not("VLM_XMPP_HTTP_BIND_PATH", "/http-bind/");
    //define_if-not('$_SERVER["HTTP_HOST"]'),"v-l-m.org");
    if(isset($_SERVER["HTTP_HOST"]))
    {
        define_if_not("VLM_XMPP_HTTP_BIND_URL", "http://".$_SERVER["HTTP_HOST"].VLM_XMPP_HTTP_BIND_PATH);
    }
    
?>
