<?php

include_once ("functions.php");

$idraces=($_REQUEST["idraces"]);
// Récupération des dimensions (x et y) : valeurs mini par défaut = 250
$image="regate".$idraces;
$thumb="images/minimaps/" . $image . ".png";
$original="images/racemaps/" . $image . ".jpg";

// Création et mise en cache de la miniature si elle n'existe pas ou est trop vieille
if ( 
     ( ! file_exists($thumb) ) 
      ||  (filemtime($thumb) < filemtime($original) )
      ||  ($_REQUEST['force'] == 'yes') 
      ||  (filemtime($thumb) < filemtime(__FILE__) )
   ) {

    list($x, $y, $type, $attr) = getimagesize($original);
    $ratio=$x/$y;
    $new_x=180;
    $new_y=$new_x/$ratio;

    $img_in  = imagecreatefromjpeg( $original ) or die("Cannot Initialize new GD image stream");
    $img_out = imagecreatetruecolor($new_x, $new_y);

    imagecopyresampled($img_out, $img_in, 0, 0, 0, 0, imagesx($img_out), imagesy($img_out), imagesx($img_in), imagesy($img_in));

    // Sauvegarde de la miniature
    imagepng($img_out, $thumb) or die ("Cannot write thumbnail");

    // libération des ressources
    imagedestroy($img_in);
    imagedestroy($img_out);
}

// Envoi de la miniature
header("Content-Type: image/png");
header("Content-Length: " . filesize($thumb));
header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
header("Content-Location: " . $thumb );
// FIXME do we want to send a redirect, here ?

readfile($thumb);

?> 
