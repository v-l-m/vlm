<?php
include_once("functions.php");

function LoadPng ($imgname) {
    $im = @imagecreatefrompng ($imgname); /* Tentative d'ouverture */
    if (!$im) { /* Test d'�chec */
        $im = imagecreatetruecolor (150, 30); /* Cr�ation d'une image vide */
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
    if (!$im) { /* Test d'�chec */
        $im = imagecreatetruecolor (150, 30); /* Cr�ation d'une image vide */
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        /* Affichage d'un message d'erreur */
        imagestring ($im, 1, 5, 5, "Erreur au chargement de l'image $imgname", $tc);
    }
    return $im;
}


$noHeader=quote_smart($_REQUEST['noHeader']);
$boatheading=quote_smart($_REQUEST['boatheading']);
$wheading=quote_smart($_REQUEST['wheading']);
$wspeed=quote_smart($_REQUEST['wspeed']);
$roadtoend=quote_smart($_REQUEST['roadtoend']);

header("Cache-Control: no-store, no-cache, must-revalidate");
if ($noHeader !=1)
{
    header("Content-type: image/png");
}
include_once("config.php");


$im = imagecreatefrompng(COMPASS_IMAGE );
$deck = imagecreatefrompng(BOAT_IMAGE );

$bg =  imagecolorallocate($deck, 170, 170, 170);

$deck = imagerotate($deck, geographic2drawing($boatheading), $bg);

imagecopymerge ( $im, $deck, (imagesx($im)  - imagesx($deck))/2,  
     (imagesy($im)  - imagesy($deck))/2, 
     0, 0, imagesx($deck), imagesy($deck), 100);


//draw a line from the center of the circle to the circle
//with a lenght and the color of the wind
$windcolor = windspeedtocolorbeaufort($wspeed, $im);
drawWindVector($im, $windcolor, 50, geographic2drawingforwind($wheading), 5);

$color = imagecolorallocate($im, 0, 0, 0);
drawWindVector($im, $color, 15, geographic2drawingforwind($roadtoend - 180), 3);
 

// Chargement du fond d'image
$instrum = LoadGif ('images/site/Afficheur-vide.gif');

// D�finition de code de couleurs
$white = imagecolorallocate($instrum, 255, 255, 255);
$grey = imagecolorallocate($instrum, 160, 160, 160);
$or = imagecolorallocate($instrum, 220, 200, 140);
$black = imagecolorallocate($instrum, 0, 0, 0);


// Merge
//imagecopymerge ( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h, int pct )
imagecopymerge ( $instrum, $im, 30, 23, 0, 0, 141, 141, 95 );
//imagecopyresized ( resource dst_image, resource src_image, int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h )
//imagecopyresized($instrum, $im, 22, 23, 0, 0, 150, 150, 141, 141);

// =======================
// Nom de l'instrument
$instrname="VLM20 Wind-angle";
$fontsize=5;
$x_instrname=25;
$y_instrname=175;
imagestring($instrum, $fontsize, $x_instrname, $y_instrname+2, $instrname, $black);
imagestring($instrum, $fontsize, $x_instrname, $y_instrname, $instrname, $grey);
imagestring($instrum, $fontsize, $x_instrname+1, $y_instrname, $instrname, $or);


// affichage de l'image
//transform true color image in indexed image 
imagetruecolortopalette($instrum, true, 10);

imagepng($instrum);
?> 
