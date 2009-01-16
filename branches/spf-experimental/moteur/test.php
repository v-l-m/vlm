<?php

include "functions.php";
include "positions.class.php";

	$pos = new positions();

	for ( $lat=0 ; $lat >-75000 ;$lat-=1000 ) {
	   $pos->lat=$lat;
	   $pos->long=0;
	   $pos->addDistance2positions(90,100);
	   printf ("Lat=%f, Long=%f, (L*2)=%f\n", $pos->lat, $pos->long, $pos->long*2);
	}

?>

