<?php
include_once ("config.php");
include_once ("functions.php");

if (!isset($_REQUEST["idraces"])) {
    die();
}
$idraces=($_REQUEST["idraces"]);

$image="regate".$idraces;
$thumb="images/minimaps/" . $image . ".png";
$original="images/racemaps/" . $image . ".jpg";

// Création et mise en cache de la miniature si elle n'existe pas ou est trop vieille
if ( 
     ( ! file_exists($original) ) 
      ||  (filemtime($original) < filemtime(__FILE__) )
   ) {

      $req = "SELECT idraces, racemap ".
             "FROM racesmap WHERE idraces = '".$idraces."'";
      $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
      $col = mysql_fetch_row ($ret);
      if ( !$col[0] )
      {
            echo "Idraces inconnu";
            print_r($col);
      }
      else
      {
          $img_out  = imagecreatefromstring( $col[1] ) or die("Cannot Initialize new GD image stream");

          // Sauvegarde
          imagejpeg($img_out, $original) or die ("Cannot write thumbnail");
      }
}


// Envoi de la miniature
header("Content-Type: image/jpg");
header("Content-Length: " . filesize($original));
header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
header("Content-Location: " . $original );
// FIXME do we want to send a redirect, here ?

readfile($original);

?> 
