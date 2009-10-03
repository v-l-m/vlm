<?php
    include_once("param.php");
    #on le fait "à la main" car il y a discordance (pour l'instant ;)) entre les branches
    $link = mysql_connect(DBSERVER, DBUSER, DBPASSWORD) or 
          die("Could not connect : " . mysql_error());
    mysql_select_db(DBNAME, $link) or die("Could not select database");
    
    include_once("../../lib/phpcommon/functions.php");
    
    $ids = Array();
    $dir = "../../medias/images/racemaps/";
    $dh  = opendir($dir);
    $select_list="";
    $c = 0;
    while (false !== ($filename = readdir($dh))) {
        if ( ! is_dir($filename) && substr($filename,0,1) != "." && substr($filename, 0, 6) == "regate") {
            $idraces = intval(substr(basename($filename,".jpg"), 6));
            if ($idraces < 1 ) continue;
            //echo "ID : $idraces\n";
            insertRacemap($idraces, $dir.$filename);
            $c++;
        } else {
            echo "ERROR: $filename not well formed (should be \"regate\d+.jpg\".\n";
        }
    }
    echo "\n--- $c racemaps inserted ---\n\n";

    $ids = Array();
    $dir = "../../medias/images/pavillons/";
    $dh  = opendir($dir);
    $select_list="";
    $c = 0;
    while (false !== ($filename = readdir($dh))) {
        if ( ! is_dir($filename) && substr($filename,0,1) != ".") {
            $idflag = basename($filename,".png");
            //echo "ID : $idflag\n";
            insertFlag($idflag, $dir.$filename);
            $c++;
        } else {
            echo "ERROR: $filename not well formed (should be \"*.png\".\n";
        }
    }

    echo "\n--- $c flags inserted ---\n\n";


?>
