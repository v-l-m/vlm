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
//$im = LoadPng ('Afficheur-VentApp.png');
//$im = LoadGif ('images/site/Afficheur-VentApp.gif');
//$im = LoadGif ('images/site/Afficheur-vide-2zones.gif');
$im = LoadGif ('images/site/Afficheur-vide.gif');

// Définition de code de couleurs
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 160, 160, 160);
$or = imagecolorallocate($im, 220, 200, 140);
$black = imagecolorallocate($im, 7, 9, 5);
$red = imagecolorallocate($im, 200, 32, 32);
$green = imagecolorallocate($im, 0, 160, 32);

// Les libellés de l'instrument
$instrname="VLM10 Windstation";
$twslib="Wind Speed";
$twsunit="kts";
$hdglib="Heading";
$twalib="Wind Angle";
$twaunit="o";
$twdlib="Wind Direction";
$twdunit="o";

$tws=htmlentities($_GET["tws"]);
$heading=htmlentities($_GET["cap"])."°";

$twd=htmlentities($_GET["twd"]);

//$twa=htmlentities($_GET["twa"]);
$twa = $twd - $heading;
if ($twa < -180 ) $twa +=360;
if ($twa > 180 ) $twa -=360;


    $winddir = (360 - $twd )%360 + 90;
    $boatdir = (360 - $heading )%360 + 90;

    if ( $twa > 0 ) {
        $amure = "tribord";
    } else {
        $amure = "babord";
    }
    $twa = abs($twa) ;





$textcolor=$black;
$textshade=$grey;

$titlesize=4;
$fontsize=12;
putenv('GDFONTPATH=' . realpath('.'));
$ttffontname="fonts/Verdana_Bold.ttf";
$ttffontsize=22;
$x=85;
$y=030;


// Speed
imagestring($im, $titlesize, $x-60, $y+0, $twslib, $textcolor);
imagestring($im, $titlesize, $x-60+1, $y+0, $twslib, $textcolor);
// Ajout de la valeur
imagettftext ( $im, $ttffontsize, 0, $x-10, $y+38+2, $textshade, $ttffontname , $tws );
imagettftext ( $im, $ttffontsize, 0, $x-10, $y+38, $textcolor, $ttffontname , $tws );
imagettftext ( $im, $ttffontsize, 0, $x-10+1, $y+38, $textcolor, $ttffontname , $tws );
// unit.
imagestring($im, $titlesize, $x+65, $y+05, $twsunit, $textcolor);

// TWD
imagestring($im, $titlesize, $x-60, $y+48, $twdlib, $textcolor);
imagestring($im, $titlesize, $x-60+1, $y+48, $twdlib, $textcolor);
// Ajout de la valeur
imagettftext ( $im, $ttffontsize, 0, $x-30, $y+50+35+2, $textshade, $ttffontname , $twd );
imagettftext ( $im, $ttffontsize, 0, $x-30, $y+50+35, $textcolor, $ttffontname , $twd  );
imagettftext ( $im, $ttffontsize, 0, $x-30+1, $y+50+35, $textcolor, $ttffontname , $twd );
// unit.
imagestring($im, $titlesize, $x+75, $y+50, $twdunit, $textcolor);


// TWA
imagestring($im, $titlesize, $x-60, $y+95, $twalib, $textcolor);
imagestring($im, $titlesize, $x-60+1, $y+95, $twalib, $textcolor);
// Ajout de la valeur
imagettftext ( $im, $ttffontsize, 0, $x-30, $y+100+35+2, $textshade, $ttffontname , $twa );
if ( $amure == "babord" ) {
  imagettftext ( $im, $ttffontsize, 0, $x-30, $y+100+35, $red, $ttffontname , $twa );
  imagettftext ( $im, $ttffontsize, 0, $x-30+1, $y+100+35, $red, $ttffontname , $twa );
} else {
  imagettftext ( $im, $ttffontsize, 0, $x-30, $y+100+35, $green, $ttffontname , $twa );
  imagettftext ( $im, $ttffontsize, 0, $x-30+1, $y+100+35, $green, $ttffontname , $twa );
}
// unit.
imagestring($im, $titlesize, $x+75, $y+100, $twaunit, $textcolor);



// =======================
// Nom de l'instrument
$x_instrname=25;
$y_instrname=175;
//imagestring($im, $fontsize, $x_instrname, $y_instrname+2, $instrname, $grey);
imagestring($im, $fontsize, $x_instrname, $y_instrname, $instrname, $grey);
imagestring($im, $fontsize, $x_instrname+1, $y_instrname, $instrname, $grey);


// affichage de l'image
header("Content-type: image/png");
imagepng($im);
?> 
