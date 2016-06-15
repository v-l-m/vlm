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


$noHeader=get_cgi_var('noHeader');
$boatheading=get_cgi_var('boatheading');
$wheading=get_cgi_var('wheading');
$wspeed=get_cgi_var('wspeed');
$roadtoend=get_cgi_var('roadtoend');
$boattype=get_cgi_var('boattype');
/* $vmg=quote_smart($_REQUEST['vmg']); */


header("Cache-Control: no-store, no-cache, must-revalidate");
if ($noHeader !=1)
{
    header("Content-type: image/png");
}
include_once("config.php");


$im = imagecreatefrompng("images/".COMPASS_IMAGE );
$deck = imagecreatefrompng("images/".BOAT_IMAGE );

imagesavealpha($im,true);
imagealphablending($deck,true);
//$transparent = imagecolorallocatealpha( $img, 0, 0, 0, 127 ); 
//imagefill( $im, 0, 0, $transparent ); 
//imagefill( $deck, 0, 0, $transparent ); 
$bg =  imagecolorallocatealpha($deck, 0, 0, 0,128);
//imagealphablending($bg,true);
imagefill( $deck, 0, 0, $transparent ); 
//$deck = imagerotate($deck, geographic2drawing($boatheading), $bg);
/*

imagealphablending($deck,true);

imagecopymerge ( $im, $deck, (imagesx($im)  - imagesx($deck))/2,  
     (imagesy($im)  - imagesy($deck))/2, 
     0, 0, imagesx($deck), imagesy($deck), 100);
//draw windpolar
$color = imagecolorallocate($im, 210, 200, 190);
$maxcolor = imagecolorallocate($im, 250, 200, 190);
drawWindPolar($im, $color, $maxcolor, $boattype, $wspeed, 1, $wheading);

//draw a line from the center of the circle to the circle
//with a lenght and the color of the wind
$windcolor = windspeedtocolorbeaufort($wspeed, $im);
drawWindVector($im, $windcolor, 50, geographic2drawingforwind($wheading), 5);

$color = imagecolorallocate($im, 0, 0, 0);
drawWindVector($im, $color, 15, geographic2drawingforwind($roadtoend - 180), 3);

/*
$color = imagecolorallocate($im, 0, 255, 0);
drawWindVector($im, $color, 15, geographic2drawingforwind($vmg - 180), 2);
*/

imagepng ($deck);

?> 
