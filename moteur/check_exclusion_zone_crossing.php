<?php

	include_once("check_exclusion_lib.php");
	
	require_once("exclusionzone.class.php");
	
	//TestExclusionLib();

	// Load and test Exclusion zones
 
  $zones = new exclusionZone($fullRacesObj->races->idraces);
	echo "\t*** Processing exclusion zones (".$zones->getActiveZoneName().")*** \n";
	foreach  ($zones->Exclusions as $Exclusion)
	{
		$StartSeg=$Exclusion[0];
		$EndSeg=$Exclusion[1];
		$Lon1=$StartSeg[1];
		$Lon2=$EndSeg[1];
		$Lat1=$StartSeg[0];
		$Lat2=$EndSeg[0];
		// echo "\n Processing ".$Lon1."-".$Lat1;
		
		if ($Lon1 > $Lon2)
		{
			$t1 = $Lon1;
			$t2 = $Lat1;
			$Lon1 = $Lon2;
			$Lat1 = $Lat2;
			$Lon2 = $t1;
			$Lat2 = $t2;
		}
		
		// Assume Lon1 < Lon2
		$CurLon = $fullUsersObj->lastPositions->long/1000;
		$CurLat = $fullUsersObj->lastPositions->lat/1000;
    
    $IntLon = new doublep();
    $IntLat = new doublep();
    
    $Ratio=intersects($lonAvant/1000,$latAvant/1000,$lonApres/1000,$latApres/1000,$Lon1,$Lat1,$Lon2,$Lat2,$IntLat,$IntLon);
		if ($Ratio!=-1)
		{
			echo "Intersection ratio ".$Ratio."\n";
			
      echo "\nIntersection ".(doublep_value($Intlon)/1000)." ".(doublep_value($Intlat)/1000)."\n";
      $coast_xinglat = $IntLat;
      $coast_xinglong = $IntLon;
      $coast_xingratio = new doublep;
      $crosses_the_coast=1;
      break;
    
		}
		/*else
		{
			echo " No intersect : ".$Ratio."\n";
		}*/
		
	}

?>
