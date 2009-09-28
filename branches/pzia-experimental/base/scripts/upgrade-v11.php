<?php

include("config.php");
    $ids = Array();
    $dir = "../../medias/images/racemaps/";
    $dh  = opendir($dir);
    $select_list="";
    while (false !== ($filename = readdir($dh))) {
        if ( ! is_dir($filename) && substr($filename,0,1) != "." && substr($filename, 0, 6) == "regate") {
            $idraces = intval(substr(basename($filename,".jpg"), 6));
            if ($idraces < 1 ) continue;
            echo "ID : $idraces";
            $img_blob = file_get_contents ($dir.$filename);
            $req = "REPLACE INTO racesmap ( idraces, racemap ".
                  ") VALUES ( ".
                  "".$idraces." , ".
                  "'".addslashes($img_blob)."') ";
            $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
 
        }
    }

    
    

?>