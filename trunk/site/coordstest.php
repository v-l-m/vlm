<?php

include_once("functions.php");
include_once ("jp.php");


$cords=array();
$LatBuoyA=quote_smart($_REQUEST["lata"]);
$LatBuoyB=quote_smart($_REQUEST["latb"]);
$LatBoat=quote_smart($_REQUEST["latboat"]);
$LongBuoyA=quote_smart($_REQUEST["longa"]);
$LongBuoyB=quote_smart($_REQUEST["longb"]);
$LongBoat=quote_smart($_REQUEST["longboat"]);
$coords=coordonneescroisement($LatBuoyA, $LongBuoyA, 
			      $LatBuoyB, $LongBuoyB, 
			      $LatBoat, $LongBoat);
echo "<H3>";
printf ("Coordonnees Croisement JP : Lat=%f , Long=%f<BR>", $coords[0], $coords[1]);

$dist=orthodromicDistance( $LatBoat , $LongBoat , $coords[0], $coords[1]);
$head=orthodromicHeading( $LatBoat , $LongBoat , $coords[0], $coords[1]);

printf ("Distance :%f<BR>", $dist);
printf ("Heading :%f<BR>", $head);
echo "</H3>";

$coords=coordonneescroisement2($LatBuoyA, $LongBuoyA, 
			      $LatBuoyB, $LongBuoyB, 
			      $LatBoat, $LongBoat);
echo "<H3>";
printf ("Coordonnees Croisement 2: Lat=%f , Long=%f<BR>", $coords[0], $coords[1]);

$dist=orthodromicDistance( $LatBoat , $LongBoat , $coords[0], $coords[1]);
$head=orthodromicHeading( $LatBoat , $LongBoat , $coords[0], $coords[1]);

printf ("Distance :%f<BR>", $dist);
printf ("Heading :%f<BR>", $head);
echo "</H3>";

?>
