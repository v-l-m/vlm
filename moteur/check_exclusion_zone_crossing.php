<?php

	include_once("check_exclusion_lib.php");
	include_once("exclusionzonedefs.php");

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
				// update "caller variables" to mimic the coast crossing mechanism
				$coast_xinglat = $Lat;
				$coast_xinglong = $Lon;
				$coast_xingratio = $distintersect / $distVac;
			}
		} 
	}

?>
