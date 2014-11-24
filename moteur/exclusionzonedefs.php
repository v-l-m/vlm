<?php

	
	$Exclusions = array();
	
	if ($fullRacesObj->races->idraces == 141502)
	{
		// VOR 2014 Leg2 Madagascar
		$p1= array(-25.976217, 32.98825); // 25° 58.573'S 32° 59.295'E
		$p2= array(-25.590117, 45.143317); // 25° 35.407'S 45° 08.599'E
		$p3= array(-20.51015,57.396433 ); // 20° 30.609'S 57° 23.786'E
		
		// VOR Leg 2 Iranian Zone
		/* 	24º 40.300N 53º 56.300E
			24º 50.000N 61º 33.700E
			25º 04.300N 60º 34.900E
			25º 11.600N 59º 34.600E
			25º 12.000N 59º 05.600E
			25º 27.500N 57º 39.400E
			25º 34.600N 55º 11.500E
			25º 39.600N 57º 09.500E
			26º 14.900N 55º 42.600E
			26º 18.900N 56º 47.700E
			26º 36.400N 56º 18.100E
			26º 42.600N 56º 33.500E 
*/

		// Vor 2 Strait of hormuz obstruction
		/*	26° 27.500'N 56° 35.500'E
			26° 32.400'N 56° 29.000'E
			26° 32.300'N 56° 32.300'E
			26° 30.100'N 56° 23.200'E
		*/
		
		echo "\n\t Setting exclusion zone for VOR Leg 2\n";
		$Exclusions = array ( array($p1,$p2), array($p2,$p3) );
	}

?>