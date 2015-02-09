<?php
include_once "functions.php";

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
$im = LoadGif ('images/site/Afficheur-vide-2zones.gif');

// Définition de code de couleurs
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 160, 160, 160);
$or = imagecolorallocate($im, 220, 200, 140);
$black = imagecolorallocate($im, 0, 0, 0);

// Les libellés de l'instrument
$poslib="POSITION";
$bslib="Speed";
$avglib="Avg";
$hdglib="Heading";
$dnmlib="DNM";
$lochlib="Loch";
$cnmolib="Ortho";
$cnmllib="Loxo";
$vmglib="VMG";


//$lat= "46°55'30\"N";
//$long="10°38'25\"W";
$str=giveDegMinSec('img',sprintf('%f',htmlentities($_GET["latitude"] / 1000 )) ,
                   sprintf('%f',htmlentities($_GET["longitude"]/ 1000 )) );
$dms_coords=explode("/", $str);
$lat=$dms_coords[0];
$long=$dms_coords[1];


//$speed="10.58";
$speed=htmlentities($_GET["speed"]);
//$heading="108°";
$heading=htmlentities($_GET["cap"])."°";

//$dnm="2558.2";
$dnm=htmlentities($_GET["dnm"]);
//loch="20720.1" nm
$loch=htmlentities($_GET["loch"]);
$avg=htmlentities($_GET["avg"]);
//$cnmo="225°";
$cnmo=htmlentities($_GET["cnmo"]);
//$cnml="229°";
$cnml=htmlentities($_GET["cnml"]);
//$vmg="15.25";
$vmg=htmlentities($_GET["vmg"]);

$instrname="VLM100 Nav-Center";

$textcolor=$black;
$textshade=$grey;

$titlesize=2;
$fontsize=5;
$x=85;
$y=030;


// ajout des libellés
// Position
//imagestring($im, $titlesize, $x-60, $y, $poslib, $textcolor);
//imagestring($im, $titlesize, $x-60+1, $y, $poslib, $textcolor);


// Ajout des informations
// Ajout de la latitude
imagestring($im, $fontsize, $x-30+2, $y+2, $lat, $textshade);
imagestring($im, $fontsize, $x-30, $y, $lat, $textcolor);
imagestring($im, $fontsize, $x-30+1, $y, $lat, $textcolor);

// Ajout de la longitude
imagestring($im, $fontsize, $x-30+2, $y+15+2, $long, $textshade);
imagestring($im, $fontsize, $x-30, $y+15, $long, $textcolor);
imagestring($im, $fontsize, $x-30+1, $y+15, $long, $textcolor);

// Speed
imagestring($im, $titlesize, $x-50, $y+35, $bslib, $textcolor);
imagestring($im, $titlesize, $x-50+1, $y+35, $bslib, $textcolor);
// Ajout de la vitesse
imagestring($im, $fontsize, $x-60, $y+35+15+2, $speed, $textshade);
imagestring($im, $fontsize, $x-60, $y+35+15, $speed, $textcolor);
imagestring($im, $fontsize, $x-60+1, $y+35+15, $speed, $textcolor);

// Moyenne
imagestring($im, $titlesize, $x+5, $y+35, $avglib, $textcolor);
imagestring($im, $titlesize, $x+5+1, $y+35, $avglib, $textcolor);
// Ajout de la vitesse moyenne
imagestring($im, $fontsize, $x-5, $y+35+15+2, $avg, $textshade);
imagestring($im, $fontsize, $x-5, $y+35+15, $avg, $textcolor);
imagestring($im, $fontsize, $x-5+1, $y+35+15, $avg, $textcolor);

// Heading
imagestring($im, $titlesize, $x+45, $y+35, $hdglib, $textcolor);
imagestring($im, $titlesize, $x+45+1, $y+35, $hdglib, $textcolor);

// Ajout du Cap
imagestring($im, $fontsize, $x+42, $y+35+15+2, $heading, $textshade);
imagestring($im, $fontsize, $x+42, $y+35+15, $heading, $textcolor);
imagestring($im, $fontsize, $x+42+1, $y+35+15, $heading, $textcolor);

// ===============
// cadran du bas
// DNM
imagestring($im, $titlesize, $x-40, $y+75+2, $dnmlib, $textcolor);
imagestring($im, $titlesize, $x-40+1, $y+75+2, $dnmlib, $textcolor);
// Ajout de Distance restante
imagestring($im, $fontsize-1, $x-50, $y+90+2, $dnm. "", $textshade);
imagestring($im, $fontsize-1, $x-50, $y+90, $dnm."", $textcolor);
imagestring($im, $fontsize-1, $x-50+1, $y+90, $dnm, $textcolor);

// LOCH
imagestring($im, $titlesize, $x+50, $y+75+2, $lochlib, $textcolor);
imagestring($im, $titlesize, $x+50+1, $y+75+2, $lochlib, $textcolor);
// de la distance parcourue
imagestring($im, $fontsize-1, $x+35, $y+90+2, $loch. "", $textshade);
imagestring($im, $fontsize-1, $x+35, $y+90, $loch."", $textcolor);
imagestring($im, $fontsize-1, $x+35+1, $y+90, $loch, $textcolor);

// CNM Ortho
imagestring($im, $titlesize, $x-55, $y+105+2, $cnmolib, $textcolor);
imagestring($im, $titlesize, $x-55+1, $y+105+2, $cnmolib, $textcolor);
// CNM Ortho
imagestring($im, $fontsize, $x-60, $y+105+15+2, $cnmo, $textshade);
imagestring($im, $fontsize, $x-60, $y+105+15, $cnmo, $textcolor);
imagestring($im, $fontsize, $x-60+1, $y+105+15, $cnmo, $textcolor);

// CNM Loxo
imagestring($im, $titlesize, $x, $y+105+2, $cnmllib, $textcolor);
imagestring($im, $titlesize, $x+1, $y+105+2, $cnmllib, $textcolor);
// CNM Loxo
imagestring($im, $fontsize, $x-7, $y+105+15+2, $cnml, $textshade);
imagestring($im, $fontsize, $x-7, $y+105+15, $cnml, $textcolor);
imagestring($im, $fontsize, $x-7+1, $y+105+15, $cnml, $textcolor);

// VMG 
imagestring($im, $titlesize, $x+60, $y+105+2, $vmglib, $textcolor);
imagestring($im, $titlesize, $x+60+1, $y+105+2, $vmglib, $textcolor);
// VMG
imagestring($im, $fontsize, $x+47, $y+105+15+2, $vmg, $textshade);
imagestring($im, $fontsize, $x+47, $y+105+15, $vmg, $textcolor);
imagestring($im, $fontsize, $x+47+1, $y+105+15, $vmg, $textcolor);


// =======================
// Nom de l'instrument
$x_instrname=25;
$y_instrname=175;
imagestring($im, $fontsize, $x_instrname, $y_instrname+2, $instrname, $black);
imagestring($im, $fontsize, $x_instrname, $y_instrname, $instrname, $grey);
imagestring($im, $fontsize, $x_instrname+1, $y_instrname, $instrname, $or);


// affichage de l'image
header("Content-type: image/png");
imagepng($im);
?> 
