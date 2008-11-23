<?php
function LoadPng ($imgname) {
   $im = @imagecreatefrompng ($imgname); /* Tentative d'ouverture */
   if (!$im) { /* Test d'échec */
       $im = imagecreatetruecolor (150, 30); /* Création d'une image vide */
       $bgc = imagecolorallocate ($im, 255, 255, 255);
       $tc = imagecolorallocate ($im, 0, 0, 0);
       imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
       /* Affichage d'un message d'erreur */
       imagestring ($im, 1, 5, 5, "Erreur au chargement de l'image $imgname", $tc);
   }
   return $im;
}

function LoadGif ($imgname) {
   $im = @imagecreatefromgif ($imgname); /* Tentative d'ouverture */
   if (!$im) { /* Test d'échec */
       $im = imagecreatetruecolor (150, 30); /* Création d'une image vide */
       $bgc = imagecolorallocate ($im, 255, 255, 255);
       $tc = imagecolorallocate ($im, 0, 0, 0);
       imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
       /* Affichage d'un message d'erreur */
       imagestring ($im, 1, 5, 5, "Erreur au chargement de l'image $imgname", $tc);
   }
   return $im;
}


// Chargement du fond d'image
$im = LoadGif ('Afficheur-vide.gif');
$map_im = LoadPng('../minimaps/map-'.htmlentities($_GET["idusers"]).'.png');

// Définition de code de couleurs
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 160, 160, 160);
$or = imagecolorallocate($im, 220, 200, 140);
$black = imagecolorallocate($im, 0, 0, 0);


// Merge
//imagecopymerge ( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h, int pct )
imagecopymerge ( $im, $map_im, 22, 23, 0, 0, 156, 140, 55 );

// =======================
// Nom de l'instrument
$instrname="VLM200 Radar/Map";
$fontsize=5;
$x_instrname=25;
$y_instrname=175;
imagestring($im, $fontsize, $x_instrname, $y_instrname+2, $instrname, $black);
imagestring($im, $fontsize, $x_instrname, $y_instrname, $instrname, $grey);
imagestring($im, $fontsize, $x_instrname+1, $y_instrname, $instrname, $or);


// affichage de l'image
header("Content-type: image/png");
imagepng($im);
?> 
