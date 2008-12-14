<?php

//=================================================================//
//                   Distance Ortodromique                         //
//                   par John-Pet Juin 2007                        //
//-----------------------------------------------------------------//
//         position de départ à position d'arrivée,                //
//            retourne la distance orthodromique.                  //
//=================================================================//
//        Algo écrit par John-Pet (JP@virtual-winds.com)           //
//=================================================================//
function orthodromicDistance($lat, $long, $latnm, $longnm)
{
  return ortho($long,$lat, $longnm, $latnm);

}

//=================================================================//
//                     Cap Orthodromique                           //
//                   par John-Pet Avril 2007                       //
//                      modifié Juin 2007			     		   //
//-----------------------------------------------------------------//
//         position de départ à position d'arrivée,                //
//              retourne le cap orthodromique.                     //
//=================================================================//
//        Algo écrit par John-Pet (JP@virtual-winds.com)           //
//=================================================================//

  function orthodromicHeading($lat, $long, $latnm, $longnm )

  // gestion des cas spécifiques
 {
	if ($lat == -90 or $latnm == 90)
		{$caportho = 0;}
	elseif ($lat == $latnm and $long == $longnm)
		{$caportho = 0;}
	elseif ($lat == -$latnm and abs($long - $longnm) == 180)
		{$caportho = 0;}
	elseif ($lat < $latnm and $long == $longnm)
		{$caportho = 0;}
	elseif ($lat == 90 or $latnm == -90)
		{$caportho = 180;}
	elseif ($lat > $latnm and $long == $longnm)
		{$caportho = 180;}
	elseif (abs($long - $longnm) == 180) {
		if ($lat < 0) $caportho = 180;
		else $caportho = 0;
	}
	else {
  // gestion des cas généraux
		$lat_bat  = deg2rad($lat);
		$long_bat = deg2rad($long);
		$lat_wp  = deg2rad($latnm);
		$long_wp = deg2rad($longnm);

	    	$X = M_PI_2 - $lat_wp;
	    	$Y = M_PI_2 - $lat_bat;
		$Z = acos(sin($lat_bat) * sin($lat_wp) + (cos($lat_bat) * cos($lat_wp) * cos($long_bat-$long_wp)));
		$cap = rad2deg(acos ((cos($X) -  (cos($Y) * cos($Z)))/ (sin($Y) * sin($Z))));

		if (sin($long_wp - $long_bat) > 0)
			{$caportho = $cap;}
		else $caportho = 360 - $cap ;
	}
	if ($caportho == 360) $caportho = 0;

	return $caportho;
 }


//=================================================================//
//                   Coordonnées croisement                       //
//                   par John-Pet Juin 2007                        //
//-----------------------------------------------------------------//
//              retourne les coordonnées les plus proches          //
//           		pour le passage de porte.                  //
//=================================================================//
//        Algo écrit par John-Pet (JP@virtual-winds.com)           //
//=================================================================//

Function coordonneescroisement($LatBuoyA, $LongBuoyA, $LatBuoyB, $LongBuoyB, $LatBoat, $LongBoat)
{

    // Le cas spécial des longitudes = 0
    if ( $LongBuoyA == 0 && $LongBuoyB == 0) {
         $longCroissement = 0;
         $maxlb=max($LatBuoyA, $LatBuoyB);
         $minlb=min($LatBuoyA, $LatBuoyB);

         if ( $LatBoat >= $maxlb ) {
	      $latCroissement = $maxlb-0.01;
	 } elseif ( $LatBoat <= $minlb ) {
	      $latCroissement = $minlb+0.01;
	 } else {
	      $latCroissement = $LatBoat;
	 }
	 return array( $latCroissement, $longCroissement);
    }

//echo "LATIBOUEE A=" . $LatBuoyA;
//echo "LONGBOUEE A=" . $LongBuoyA;
//echo "LATIBOUEE B=" . $LatBuoyB;
//echo "LONGBOUEE B=" . $LongBuoyB;

    $CapOrthoPorte = orthodromicHeading($LatBuoyA, $LongBuoyA, $LatBuoyB, $LongBuoyB);
    //gestion des cas spéciaux
    If ($LongBuoyA == $LongBuoyB) {
	$LongVertex = $LongBuoyA + 90;
    } elseif ($LatBuoyA == 0 And $LatBuoyB != 0) {
	$LongVertex = $LongBuoyA + 90;
    } elseif ($LatBuoyA != 0 And $LatBuoyB == 0) {
	$LongVertex = $LongBuoyB + 90;
    } elseif ($LongBuoyA - $LongBuoyB == 180) {
	$LongVertex = $LongBuoyA + 90;
    } else {
	//gestion des cas généraux
        $LongVertex = $LongBuoyA + rad2deg(atan(1 / (tan(deg2rad($CapOrthoPorte)) * sin(deg2rad($LatBuoyA)))));
    }

    //donne les longitudes de l'axe de rotation sur l'équateur
    $Long1 = $LongVertex + 90 - (360 * floor(($LongVertex + 90) / 360));
    If ($Long1 > 180) {
        $Long1 = $Long1 - 360;
    }

    $Long2 = $LongVertex - 90 - (360 * floor(($LongVertex - 90) / 360));
    If ($Long2 > 180) {
        $Long2 = $Long2 - 360;
    }

    if ($LongBuoyA < 0 ) {
        $SgnLongBuoyA = -1;
    } else {
        $SgnLongBuoyA = 1;
    }

    if ($LongBuoyB < 0 ) {
        $SgnLongBuoyB = -1;
    } else {
        $SgnLongBuoyB = 1;
    }

    if ($Long1 < 0 ) {
        $SgnLong1 = -1;
    } else {
        $SgnLong1 = 1;
    }

    $Signe = $SgnLongBuoyA;
    If (abs($LatBuoyA) > abs($LatBuoyB)) {
        $Signe = $SgnLongBuoyB;
    }
    //choix de la longitude de l'axe de rotation au plus prés des WP
    If ($Signe == $SgnLong1) {
	$LongAxeRotation = $Long1;
    } else {
	$LongAxeRotation = $Long2;
    }

    // distance ortho axe rotation équateur > bateau
    $DistOrthoBoat =orthodromicDistance(0, $LongAxeRotation, $LatBoat, $LongBoat);

    // angle axe rotation  équateur> bateau
    $AngleBoat = orthodromicHeading(0, $LongAxeRotation, $LatBoat, $LongBoat);

    // angle axe rotation équateur > bouées
    $AngleBuoys = orthodromicHeading(0, $LongAxeRotation, $LatBuoyA, $LongBuoyA);
    
    //delta angle bat > angle bouées
    $deltaAngle = $AngleBuoys - $AngleBoat;

    //angle axe rotation > bateau après rotation de l'ensemble bouées bateau
    $newAngleBoat = 90 - $deltaAngle;

    //latitude du bateau sur ce nouveau grand cercle
    $newLatBoat = rad2deg(asin(sin(deg2rad($DistOrthoBoat) / 60) * cos(deg2rad($newAngleBoat))));

    //longitude du bateau sur ce nouveau grand cercle
    //gestion des cas spéciaux
    If ( $newAngleBoat == 0 Or abs($newAngleBoat) == 180) {
        $newLongBoat = $LongAxeRotation;
    } else {
	//gestion des cas généraux
	//calcul
	$x = rad2deg(acos(cos(deg2rad($DistOrthoBoat) / 60)/(cos(deg2rad($newLatBoat)))));
	If ($newAngleBoat > 180) {
		$newLongBoat = $LongAxeRotation - $x;
	}
	If ($newAngleBoat < 180) {
		$newLongBoat = $LongAxeRotation + $x;
	}
    }
    // distance ortho bateau sur l'équateur
    $DistOrthoBoat =orthodromicDistance(0, $LongAxeRotation, 0, $newLongBoat);

	// distance ortho bouée A sur l'équateur
    $DistOrthoBuoyA =orthodromicDistance(0, $LongAxeRotation, $LatBuoyA, $LongBuoyA);

	// distance ortho bouée B sur l'équateur
    $DistOrthoBuoyB =orthodromicDistance(0, $LongAxeRotation, $LatBuoyB, $LongBuoyB);

    If ( ($DistOrthoBuoyA < $DistOrthoBuoyB And $DistOrthoBoat < $DistOrthoBuoyA )
       Or ( $DistOrthoBuoyA > $DistOrthoBuoyB And $DistOrthoBoat > $DistOrthoBuoyA) ) {

		$latCroissement = $LatBuoyA;
		$longCroissement = $LongBuoyA;

	} ElseIf ( ($DistOrthoBuoyA < $DistOrthoBuoyB And $DistOrthoBuoyB < $DistOrthoBoat )
	         Or ( $DistOrthoBuoyA > $DistOrthoBuoyB And $DistOrthoBuoyB > $DistOrthoBoat) ) {

		$latCroissement = $LatBuoyB;
		$longCroissement = $LongBuoyB;

	} Else {
		//latitude du croisement perpendiculaire entre le grand cercle passant par les bouées de la porte
		//et le grand cercle passant par le bateau
		$latCroissement = rad2deg(asin(sin(deg2rad($DistOrthoBoat) / 60) * cos(deg2rad($AngleBuoys))));

		//longitude du croisement perpendiculaire entre le grand cercle passant par les bouées de la porte
		//et le grand cercle passant par le bateau
		If ($AngleBuoys == 0 Or abs($AngleBuoys) == 180) {
			$longCroissement = $LongAxeRotation;
		} Else {
		//gestion des cas généraux
			//calcul
			$x = rad2deg(acos(cos(deg2rad($DistOrthoBoat) / 60)/(cos(deg2rad($latCroissement)))));
			If ( $AngleBuoys > 180 ) {
				$longCroissement = $LongAxeRotation - $x;
			}
			If ( $AngleBuoys < 180 ) {
				$longCroissement = $LongAxeRotation + $x;
			}
		}
	}
	return array( $latCroissement, $longCroissement );

//	return $latCroissement;
//	return $longCroissement;
}
 ?>
