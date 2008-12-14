<?php
/********Functions*********/

/*return knots*/
function norm($x, $y)
{
  return ((sqrt(pow($x, 2) + pow($y, 2)))*MS2KNT);
}

/*from a cartesian vector (x,y), return the geographic angle between 0 and 359 */
function angle($x, $y)
{
  //$xknt = $x*MS2KNT;
  //$angle_trigo = rad2deg(acos ($xknt / norm ($x, $y)));
  $hyp=sqrt(pow($x, 2) + pow($y, 2));
  // Petite modif pour éviter les divisions/0 sur les zones sans vent.
  if ( $hyp == 0 ) $hyp=0.0001;
  $angle_trigo = rad2deg(acos($x/$hyp));
  if ($y < 0)
    $angle_trigo *= -1;

  //echo "angle_trigo = $angle_trigo norm =".norm($x, $y)." xknt = $xknt angle_geographic =". trigo2geographic($angle_trigo)."\n";

  return trigo2geographic($angle_trigo);
}

/*from a trigonometric angle in degree, return an geographic angle in degree*/
function trigo2geographic($angle)
{
  //$angle = $angle%360;
  $angle = -$angle;
  //$angle = $angle%360;
  $angle = $angle + 90; // ???? 90 ????

  if ($angle <= 0)
    $angle += 360;
  return $angle;
}

function geographic2trigo($angle)
{
  $angle = -$angle;
  //$angle = $angle%360;
  $angle = $angle - 90;

  if ($angle <= 0)
    $angle += 360;
  return $angle;
}

/*from a geographic angle return a drawing angle (turn same diretion but
different origin*/
function geographic2drawing($angle)
{
  $angle = -$angle;

  if ($angle <= 0)
    $angle += 360;
  return $angle;
}

function  geographic2drawingforwind($angle)
{
  $angle = ($angle +90 ) %360;
  return $angle;
}

function geographic2drawingforspeedchart($angle)
{
  $angle = -$angle;
  $angle = ($angle + 180)%360;
  return $angle;
}


/*from an angle (degrees) and a norm, give the normed cartesian cordinates*/
function polar2cartesian($a, $r)
{
  //convert $a from geographic angle to trigonometric angle
  $a_trigo = (360 - $a)%360 + 90;

  $result[0] = $r*cos(deg2rad($a_trigo));
  $result[1] = $r*sin(deg2rad($a_trigo));
  //echo "\npolar2cartesian angle_trigo = $a_trigo, x = $result[0], y = $result[1] \n";
  return $result;
}

/*same function for the map, angle conversion function not the same, dont know why
it is used to draw a triangle for the boat position and heading (map.php)*/
function polar2cartesianDrawing($a, $r)
{
  //convert $a from geographic angle to drawing angle
  $a = ($a -90 ) %360;

  $result[0] = $r*cos(deg2rad($a));
  $result[1] = $r*sin(deg2rad($a));
  //echo "\npolar2cartesian angle_trigo = $a_trigo, x = $result[0], y = $result[1] \n";
  return $result;
}

/*from two points (4 coordinates),
compute line equation
Si (Xa = Xb), alors : m = Xa, 
Si (Ya = Yb), alors : p = Ya
Sinon,  pas // à axe des ordonnées (Xa != Xb) , ni des abscisses : y = mx + p (m = meridien, p = parallèle)

return m and p in a vector*/
function linear($Xa, $Ya, $Xb, $Yb)
{
  //echo "Calling linear with $Xa, $Ya, $Xb, $Yb\n";
  //if ( $Xa == $Xb ) {
  //  $m = $Ya;
  //  $p = 0;
  //} else if ( $Ya == $Yb ) {
  //  
  //  } else {
  	$m = ($Yb-$Ya)/($Xb-$Xa);
  	//applies in a
        $p = $Ya - $m*$Xa;
  //}
  
  return array($m, $p);
}



?>
