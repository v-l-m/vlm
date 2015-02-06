<?php

	include_once("check_exclusion_lib.php");
	
	require_once("exclusionzone.class.php");
	
	TestExclusionLib();

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
		
    $IntLon = new doublep();
    $IntLat = new doublep();
    
    echo " ".$lonAvant ." ". $latAvant ." " .$lonApres . " " .$latApres ." ".$Lon1. " " .$Lat1." ".$Lon2." ".$Lat2."\n";
    $Ratio=intersects($latAvant/1000/180*pi(),$lonAvant/1000/180*pi(),$latApres/1000/180*pi(),$lonApres/1000/180*pi(),$Lat1/180*pi(),$Lon1/180*pi(),$Lat2/180*pi(),$Lon2/180*pi(),$IntLat,$IntLon);
		if ($Ratio!=-1)
		{
			echo "Intersection ratio ".$Ratio."\n";
      $tc = GetTrueCourse($lonAvant/1000, $latAvant/1000,$lonApres/1000,$latApres/1000);
		
      VLM_raw_move_loxo($latAvant, $lonAvant, $distVac*$Ratio, $tc, $IntLat, $IntLon);
				
      echo "\nIntersection ".(doublep_value($IntLon)/1000)." ".(doublep_value($IntLat)/1000)."\n";
      $coast_xinglat = $IntLat;
      $coast_xinglong = $IntLon;
      $coast_xingratio = new doublep;
      $coast_xingratio = $Ratio;
      $crosses_the_coast=1;
      // Break the loop, once the 1st intersection has been crossed.
      break;
		}
		/*else
		{
			echo " No intersect : ".$Ratio."\n";
		}*/
		
	}

?>
