<?php

//=================================================================//
//        Get Real Boats                                           //
// Retourne un tableau avec numero, latitide, longitude, couleur   //
// Si un parametre est passé, c'est le nuémro de la course, sinon toutes //
//=================================================================//
function getRealBoats($race = 0, $age = 10800 ) {

        // Temps de référence
        $reftime = time();

        // Le retour
        $boatarr = array();

  $query = "SELECT P.idusers, P.lat, P.long, U.color, U.boatname  
            FROM positions P, users U 
      WHERE  P.idusers < 0 
                  AND    P.time > $reftime - $age
      AND    U.idusers = P.idusers ";
        if ( $race != 0 ) $query .= "AND race = $race ";

  // On se limite le nombre de positions pour les bateaux réels pour l'instant
  $query .= " ORDER BY P.race DESC, P.idusers ASC , P.time DESC  ";
  // Dans la mise en tableau, on arrête la boucle si le numéro de bateau est supérieur au dernier qu'on a ajouté

        $result = mysql_query($query) or die("Query [$query] failed \n");
        $num_rows = mysql_num_rows($result);
//echo "NUM=$num_rows\n";

        while ( $boat = mysql_fetch_array($result, MYSQL_NUM) ) {
              array_push($boatarr, $boat);
        }
//print_r($boatarr);
        return ($boatarr);
}

//function CoordCarte () //($lat_bat, $long_bat, $unite, $HauteurCartePixel, $LargeurCartePixel)
//=================================================================//
//                  Dimensions de la carte                         //
//                   By John-Pet Mai 2007                          //
//-----------------------------------------------------------------//
//               Retourne les coordonnées de la carte              //
//               avec comme milieu le point reference.             //
//=================================================================//
//        Algo written by John-Pet (JP@virtual-winds.com)          //
//=================================================================//

function coordCarte($lat_bat, $long_bat, $unite, $HauteurCartePixel, $LargeurCartePixel)
//function CoordCarte () //($lat_bat, $long_bat, $unite, $HauteurCartePixel, $LargeurCartePixel)
    // $unite : unite de carte,  pour le zoom plus le chiffre est élevé plus le zoom est puissant
    // $HauteurCartePixel : hauteur de la carte en pixel
    // $LargeurCartePixel : largeur de la carte en pixel
        // ce qui permettra de customiser les cartes 
        // $lat_bat et $long_bat sont les coordonnées du point milieu de la carte
        // ce qui permettra par la suite de centrer la carte sur un point quelconque grace à des boutons de déplacement
{
/*
   if      ( abs($lat_bat) < 20 ) { $unite=600/$maparea; } 
   else if ( abs($lat_bat) < 40 ) { $unite=450/$maparea; } 
   else                           { $unite=300/$maparea; }
*/

    // calcul du delta entre la latitude bateau et latitude 45°
    $deltalat_bat = (10800 / M_PI) * log(tan(deg2rad(45 + ($lat_bat/2))));

    // pour la latitude superieure de la carte
    $latnord = 2 * (rad2deg(atan(exp(($deltalat_bat + ($HauteurCartePixel / (2 * $unite))) * M_PI / 10800))) - 45);

    // pour la latitude inferieure de la carte
    $latsud  = 2 * (rad2deg(atan(exp(($deltalat_bat - ($HauteurCartePixel / (2 * $unite))) * M_PI / 10800))) - 45);

    // pour la longitude à gauche de la carte avec gestion de l'antimeridien
    $longouest = $long_bat - ($LargeurCartePixel / ($unite * 120));
    if ($longouest == 180 or $longouest == -180) {
        $longouest = 180;
    } elseif ( sin(deg2rad($longouest)) < 0) {
        $longouest = $longouest - (180 * floor($longouest / 180 )) -180; 
    } else {
        $longouest = $longouest - (180 * floor($longouest / 180 ));
    }

    // pour la longitude à droite de la carte avec gestion de l'antimeridien
    $longest = $long_bat + ($LargeurCartePixel / ($unite * 120));
    if ($longest == 180 or $longest == -180) {
        $longest = 180;
    } elseif ( sin(deg2rad($longest)) < 0) {
        $longest = $longest - (180 * floor($longest / 180 )) -180;
    } else {
        $longest = $longest - (180 * floor($longest / 180 ));
    }

    return array( $latnord, $latsud, $longouest, $longest );
}



/*this function comes from php.net*/
function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
    {
      /* this way it works well only for orthogonal lines
     imagesetthickness($image, $thick);
     return imageline($image, $x1, $y1, $x2, $y2, $color);
      */
      if ($thick == 1) {
  return imageline($image, $x1, $y1, $x2, $y2, $color);
      }
      $t = $thick / 2 - 0.5;
      if ($x1 == $x2 || $y1 == $y2) {
  return imageline($image, $x1, $y1, $x2, $y2, $color);
  //I dont know why, but this next line fails when using a big zoom
  //return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
      }
      $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
      $a = $t / sqrt(1 + pow($k, 2));
      $points = array(
          round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
          round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
          round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
          round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
          );   
      imagefilledpolygon($image, $points, 4, $color);
      return imagepolygon($image, $points, 4, $color);
    }

function arrow($im, $x1, $y1, $x2, $y2, $color, $alength = 10, $awidth = 2 ) {

   $distance = sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));

   $dx = $x2 + ($x1 - $x2) * $alength / $distance;
   $dy = $y2 + ($y1 - $y2) * $alength / $distance;

   $k = $awidth / $alength;

   $x2o = $x2 - $dx;
   $y2o = $dy - $y2;

   $x3 = $y2o * $k + $dx;
   $y3 = $x2o * $k + $dy;

   $x4 = $dx - $y2o * $k;
   $y4 = $dy - $x2o * $k;

   imageline($im, $x1, $y1, $dx, $dy, $color);
   imageline($im, $x3, $y3, $x4, $y4, $color);
   imageline($im, $x3, $y3, $x2, $y2, $color);
   imageline($im, $x2, $y2, $x4, $y4, $color);
}

?>
