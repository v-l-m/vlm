<?php
include_once("functions.php");

/*function LoadPng ($imgname) {
    $im = @imagecreatefrompng ($imgname); // Tentative d'ouverture /
    if (!$im) { // Test d'�chec /
        $im = imagecreatetruecolor (150, 30); /* Cr�ation d'une image vide /
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        // Affichage d'un message d'erreur /
        imagestring ($im, 1, 5, 5, "Erreur au chargement de l'image $imgname", $tc);
    }
    return $im;
}

function LoadGif ($imgname) {
    $im = @imagecreatefromgif ($imgname); /* Tentative d'ouverture /
    if (!$im) { // Test d'�chec /
        $im = imagecreatetruecolor (150, 30); /* Cr�ation d'une image vide /
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        // Affichage d'un message d'erreur /
        imagestring ($im, 1, 5, 5, "Erreur au chargement de l'image $imgname", $tc);
    }
    return $im;
}*/

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
        // creating a cut resource 
        $cut = imagecreatetruecolor($src_w, $src_h); 

        // copying relevant section from background to the cut resource 
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
        
        // copying relevant section from watermark to the cut resource 
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
        
        // insert cut resource to destination image 
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
    }

function rotate_image_alpha ($image, $angle, $bgcolor, $bgtransparency)
{
  // seen at http://www.php.net/manual/en/function.imagerotate.php
  
  $srcw = imagesx($image);
  $srch = imagesy($image);
  
  //Normalize angle
  $angle %= 360;
  
  if($angle == 0) {
    return $image;
  }
  
  // Convert the angle to radians
  $theta = deg2rad ($angle);
  
  // Standard case of rotate
  if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
    $width = $srch;
    $height = $srcw;
    if ( ($angle == 90) || ($angle == -270) ) {
      $minX = 0;
      $maxX = $width;
      $minY = -$height+1;
      $maxY = 1;
      $sin = 1;
    } else if ( ($angle == -90) || ($angle == 270) ) {
      $minX = -$width+1;
      $maxX = 1;
      $minY = 0;
      $maxY = $height;
      $sin = -1;
    }
    $cos = 0;
  } else if (abs($angle) === 180) {
    $width = $srcw;
    $height = $srch;
    $minX = -$width+1;
    $maxX = 1;
    $minY = -$height+1;
    $maxY = 1;
    $sin = 0;
    $cos = -1;
  } else {
    $sin = sin($theta);
    $cos = cos($theta);
    
    // Calculate the width of the destination image.
    $temp = array (0,
                   $srcw * $cos,
                   $srch * $sin,
                   $srcw * $cos + $srch * $sin
                  );
    $minX = floor(min($temp));
    $maxX = ceil(max($temp));
    $width = $maxX - $minX;
    
    // Calculate the height of the destination image.
    $temp = array (0,
                   $srcw * $sin * -1,
                   $srch * $cos,
                   $srcw * $sin * -1 + $srch * $cos
                  );
    $minY = floor(min($temp));
    $maxY = ceil(max($temp));
    $height = $maxY - $minY;
  }
  
  $destimg = imagecreatetruecolor($width, $height);
  $r=$bgcolor>>16;
  $g=($bgcolor>>8) & 0xFF;
  $b=($bgcolor) & 0xFF;
  $bgcolor = imagecolorallocatealpha ($destimg, $r,$g,$b, $bgtransparency);
  imagefill($destimg, 0, 0, $bgcolor);
  imagesavealpha($destimg, true);
  
  // sets all pixels in the new image
  for($x=$minX; $x<$maxX; $x++) {
    for($y=$minY; $y<$maxY; $y++) {
      // fetch corresponding pixel from the source image
      $srcX = round($x * $cos - $y * $sin);
      $srcY = round($x * $sin + $y * $cos);
      if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
        $color = imagecolorat($image, $srcX, $srcY);
      } else {
        $color = $bgcolor;
      }
      imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
    }
  }
  return $destimg;
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

/*imagesavealpha($im,true);
imagesavealpha($deck,true);
imagealphablending($im,true);
imagealphablending($deck,true);*/
//$transparent = imagecolorallocatealpha( $im, 255, 255, 255, 0 ); 
//imagefill( $im, 0, 0, $transparent ); 
//imagefill( $deck, 0, 0, $transparent ); 
//$bg =  imagecolorallocatealpha($deck, 0, 0, 0,255);
//imagealphablending($bg,true);
//imagefill( $deck, 0, 0, $transparent ); 
//$deck = imagerotate($deck, geographic2drawing($boatheading), 0X00FFFFFF,0);
$deck=rotate_image_alpha($deck,geographic2drawing($boatheading), 0XFFFFFF, 127 );

imagealphablending($deck,true);

imagecopymerge( $im, $deck, (imagesx($im)  - imagesx($deck))/2,  
     (imagesy($im)  - imagesy($deck))/2, 
     0, 0, imagesx($deck), imagesy($deck), 100);
//imagesavealpha($im,true);
//draw windpolar
/*
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

imagepng ($im);

?> 
