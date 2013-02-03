<?php
header ("Content-type: image/png");
header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
$image = imagecreate(40,32);

$orange = imagecolorallocate($image, 255, 128, 0); // Le fond est orange (car c'est la première couleur)
$bleu = imagecolorallocate($image, 0, 0, 255);
$bleuclair = imagecolorallocate($image, 156, 227, 254);
$noir = imagecolorallocate($image, 0, 0, 0);
$blanc = imagecolorallocate($image, 255, 255, 255);
$color_idu = imagecolorallocate($image, 50, 0, 100);

if($_GET['rank'] == "1")
{ $src = imagecreatefrompng('bateauPremier.png'); } else { $src = imagecreatefrompng('bateauEnCourse.png'); }
imagecopy($image, $src, 4, 0, 4, 0, 32, 32);

// image,distance du bord gauche, distance du bord haut, distance du bord droit, distance du bas
//ImageFilledRectangle($image, 0, 5, 40, 20, $bleu);
//ImageRectangle($image, 0, 4, 39, 16, $bleu);

// image, taille texte, distance du bord gauche, distance du bord haut, texte, couleur
imagestring($image, 2, 1, 2, $_GET['idu'], $color_idu);
imagecolortransparent($image, $orange); // On rend le fond orange transparent

imagepng($image);

?>
