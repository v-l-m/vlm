<?php

	include_once("check_exclusion_lib.php");

	$p1= array(-25.976217, 32.98825); // 25° 58.573'S 32° 59.295'E
	$p2= array(-25.590117, 45.143317); // 25° 35.407'S 45° 08.599'E
	$p3= array(-20.51015,57.396433 ); // 20° 30.609'S 57° 23.786'E
		
	$Exclusions = array ( array($p1,$p2), array($p2,$p3) );

	echo "*** Processing exclusion zones *** \n";
	// Exclusion zones
	foreach  ($Exclusions as $Exclusion)
	{
		$StartSeg=$Exclusion[0];
		$EndSeg=$Exclusion[1];
		$Lon1=$StartSeg[0];
		$Lon2=$EndSeg[0];
		$Lat1=$StartSeg[1];
		$Lat2=$EndSeg[1];
		// echo "\n Processing ".$Lon1."-".$Lat1;
		if (IntersectOrthodromes($Lon1,$Lat1,0,$fullUsersObj->lastPositions->long,$fullUsersObj->lastPositions->lat,$fullUsersObj->users->targetandhdg, $Lon, $Lat)==1)
		{
			$distintersect = SurfaceDistance($Lon,$Lat,$fullUsersObj->lastPositions->long,$fullUsersObj->lastPositions->lat);
			echo "Check intersect distance ".$distintersect. ">".$distVac."?";
			if ($distVac >= $distintersect)
			{
				echo "intersection @ ".$Lon." ".$Lat." ".$distintersect."\n";
			}
		} 
	}

?>
