<?php


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


  // Oldloxo
  function oldloxo($longnm, $latnm, $long, $lat)
  {
    // Historiquement : la fonction retournait
     return (angle( $longnm - $long, $latnm - $lat) );
  }

//=================================================================//
//                     loxodromic Heading                          //
//                       By JP Mars 2007                           //
//-----------------------------------------------------------------//
//            from a position and a destination,                   //
//       return the angle to follow an loxodromic course.          //
//-----------------------------------------------------------------//
//        Algo written by John-Pet (JP@virtual-winds.com)          //
//=================================================================//

  function loxodromicHeading($longnm, $latnm, $long, $lat)
  {

     // Find the best coordinates to cross the nextwaypoint
     $lat_bat  = deg2rad($lat);
     $long_bat = deg2rad($long);

     $lat_wp  = deg2rad($latnm);
     $long_wp = deg2rad($longnm);

     //printf ("   En radian : lat_bat=%f, long_bat=%f<BR>", $lat_bat, $long_bat);
     //printf ("   En radian : lat_wp=%f, long_wp=%f<BR>", $lat_wp, $long_wp);

     // Correction de la longitude de départ pour la gestion de l'antiméridien
     if ( $long_bat < 0 and $long_wp > 0 and ($long_bat - $long_wp) < M_PI and ($long_bat - $long_wp) < -M_PI ) {
        $cor_long_bat = 2 * M_PI + $long_bat;
     } else {
        $cor_long_bat = $long_bat;
     }

     // Correction de la longitude d'arrivée pour la gestion de l'antiméridien
     if ( $long_bat > 0 and $long_wp < 0 and ($long_bat - $long_wp) > M_PI and ($long_bat - $long_wp) > - M_PI ) {
        $cor_long_wp = 2 * M_PI + $long_wp;
     } else {
        $cor_long_wp = $long_wp;
     }

     // Nouvelles longitudes selon correction ou pas
     $long_bat = $cor_long_bat;
     $long_wp  = $cor_long_wp;

     // calcul de l'angle avec gestion des caps 90 et 270°
     $dla = rad2deg(60 * ($lat_bat - $lat_wp));
     $dlom = rad2deg(60 * cos(($lat_bat + $lat_wp) / 2) * ($long_bat - $long_wp));
      
     if ($dlom == 0 ) {
        $angle = 0;
     } else {
        $angle = abs(rad2deg(atan($dla / $dlom)));
     }

     // Résultat pour le cap loxo à suivre
     if ( $lat_bat < $lat_wp and $long_bat > $long_wp ) {
        $caploxo = 270 + $angle;
     } elseif ( $lat_bat < $lat_wp and $long_bat < $long_wp ) {
        $caploxo = 90 - $angle;
     } elseif ( ( $lat_bat > $lat_wp and $long_bat > $long_wp ) or ($lat_bat == $lat_wp and $long_bat > $long_wp ) ) {
        $caploxo = 270 - $angle;
     } elseif ( ($lat_bat > $lat_wp and $long_bat < $long_wp ) or ($lat_bat == $lat_wp and $long_bat < $long_wp ) ) {
        $caploxo = 90 + $angle;
     } elseif ( ($lat_bat < $lat_wp and $long_bat == $long_wp ) or ($lat_bat == $lat_wp and $long_bat == $long_wp ) ) {
        $caploxo = 0;
     } elseif ( $lat_bat > $lat_wp and $long_bat == $long_wp ) {
        $caploxo = 180;
     } 

     // Résultat pour la distance loxo
     if ( $dlom == 0 ) {
        $distloxo = abs($dla);
     } else {
        $distloxo = abs($dlom / cos(deg2rad($angle * M_PI)));
     }

     return $caploxo;
  }

//=================================================================//
//                    Orthodromic Heading                          //
//                   By John-Pet Avril 2007                        //
//-----------------------------------------------------------------//
//            from a position and a destination,                   //
//       return the angle to follow an orthodromic course.         //
//=================================================================//
//        Algo written by John-Pet (JP@virtual-winds.com)          //
//=================================================================//

  function orthodromicHeading($longnm, $latnm, $long, $lat)
  {
     // Find the best coordinates to cross the nextwaypoint
     $lat_bat  = deg2rad($lat);
     $long_bat = deg2rad($long);

     $lat_wp  = deg2rad($latnm);
     $long_wp = deg2rad($longnm);

	 if ($lat_bat == $lat_wp and $long_bat == $long_wp) $caportho = 2; 
	 else
	  {
	     $X = M_PI_2 - $lat_wp;
	     $Y = M_PI_2 - $lat_bat;
		 $Z = acos(sin($lat_bat) * sin($lat_wp) + cos($lat_bat) * cos($lat_wp) * cos(($long_bat-$long_wp)));
		 $W = (cos($X) -  (cos($Y) * cos($Z)))/ (sin($Y) * sin($Z));
		 if ($W > 1 or $W < -1) $cap = 0;
			 else
				 $cap = rad2deg(acos($W));
		 }
		 {
			 if (($lat_bat < $lat_wp and $long_bat == $long_wp) or ($lat_bat == $lat_wp and $long_bat == $long_wp) or ($lat_bat == 0 and $long_bat == 0 and $lat_wp == 0 and $long_wp == 0 )) $caportho = 0;
			 elseif (($lat_bat > $lat_wp and $long_bat == $long_wp)) $caportho = 180 ;
			 elseif (sin($long_wp - $long_bat) > 0) $caportho = $cap;
			 elseif ($caportho = 360 - $cap );
		 }

   return $caportho;
  }

//=================================================================//
//              double heading OrthodromicHeading()                //
//                   By Otocinclus May 2005                        //
//-----------------------------------------------------------------//
//            from a position and a destination,                   //
//       return the angle to follow an orthodromic course.         //
//=================================================================//

  function otoorthodromicHeading($longnm, $latnm, $long, $lat)
  {
     // Find the best coordinates to cross the nextwaypoint
     $longA = $long;
     $latA = $lat;

     $longB = $longnm;
     $latB = $latnm;

     $c=acos(sin(deg2rad($latA))*sin(deg2rad($latB)) + cos(deg2rad($latA))*cos(deg2rad($latB))*cos(deg2rad(($longA-$longB))));
     $cosA=(cos(deg2rad(90 - $latB))-(cos(deg2rad(90 - $latA))*cos($c)))/(sin(deg2rad(90 - $latA))*sin($c));

     if ($cosA > 1) $cosA = 1;
     else if ($cosA < -1) $cosA = -1;

     if ($longA < $longB) return rad2deg(acos($cosA));
     else return 360 - rad2deg(acos($cosA));
  }

$lat=quote_smart($_REQUEST["lat"]);
$long=quote_smart($_REQUEST["long"]);
$latnm=quote_smart($_REQUEST["latnm"]);
$longnm=quote_smart($_REQUEST["longnm"]);

printf ("Coordonnées du bateau   : lat=%f,long=%f<BR>", $lat,$long);
printf ("Coordonnées du waypoint : lat=%f,long=%f<BR><BR>", $latnm,$longnm);
printf ("Orthodromie Oto         : %d<BR>", otoorthodromicHeading($longnm,$latnm,$long,$lat));
printf ("Orthodromie JP          : %d<BR><BR>", orthodromicHeading($longnm,$latnm,$long,$lat));
printf ("Old Loxodromie          : %d<BR>", oldloxo($longnm,$latnm,$long,$lat));
printf ("Loxodromie JP           : %d<BR>", loxodromicHeading ($longnm,$latnm,$long,$lat));

//printf ("M_PI=%f <BR>", M_PI);
?>
