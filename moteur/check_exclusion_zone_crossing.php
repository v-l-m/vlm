<?php

	include_once("check_exclusion_lib.php");
	
	include("exclusionzonedefs.php");
	
	TestExclusionLib();

	echo "\t*** Processing exclusion zones *** \n";
	// Exclusion zones
	foreach  ($Exclusions as $Exclusion)
	{
		$StartSeg=$Exclusion[0];
		$EndSeg=$Exclusion[1];
		$Lon1=$StartSeg[1];
		$Lon2=$EndSeg[1];
		$Lat1=$StartSeg[0];
		$Lat2=$EndSeg[0];
		// echo "\n Processing ".$Lon1."-".$Lat1;
		$tc = GetTrueCourse($Lon1, $Lat1,$Lon2,$Lat2);
		
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
		
		// Get intersction lon with exclusion zone is any
		/*if (GetGCLonAtLat($Lon1,$Lat1,$Lon2,$Lat2,$CurLon,$CurLat,$Lon))
		{
			// Create fake segment around lon, lat intersect
			echo "Intersect @ ".$Lon." ".$CurLat."\n";
			$new_lat1 = new doublep();
			$new_long1 = new doublep();
			$new_lat2 = new doublep();
			$new_long2 = new doublep();
			$Nextlat = new doublep();
			$Nextlon = new doublep();
			
			VLM_raw_move_loxo($CurLat, $Lon, $distVac, $tc, $new_lat1, $new_long1);
			VLM_raw_move_loxo($CurLat, $Lon, $distVac, $tc+180, $new_lat2, $new_long2);
			VLM_raw_move_loxo($CurLat, $CurLon, $distVac, $tc, $Nextlat, $Nextlon);
			
			//echo "cap : ".$tc." dist:".$distVac."\n";
			echo "Intersect seg ".doublep_value($new_lat1)." ".doublep_value($new_long1)." ".doublep_value($new_lat2)." ".doublep_value($new_long2)."\n";
			$Ratio =0;
			if (SegmentsIntersect($CurLon, $CurLat,doublep_value($Nextlon),doublep_value($Nextlat),doublep_value($new_long1), doublep_value($new_lat1),
									doublep_value($new_long2),doublep_value($new_lat2),$Ratio)==1)
			{
				echo "Intersection !! ".$Ratio;
				//$coast_xinglat = $Lat;
				//$coast_xinglong = $Lon;
				$coast_xingratio = $Ratio;
				$crosses_the_coast=1;
			}
			else
			{
				echo " No intersect : ".$Ratio."\n";
			}
			
		}*/
		$Nextlat = new doublep();
		$Nextlon = new doublep();
		VLM_raw_move_loxo($CurLat, $CurLon, $distVac, $tc, $Nextlat, $Nextlon);
		
		if (SegmentsIntersect($CurLon,$CurLat,doublep_value($Nextlon),doublep_value($Nextlat),$Lon1,$Lat1,$Lon2,$Lat2,$Ratio)==1)
		{
			echo "Intersection ration ".$Ratio."\n";
			
			if ($Ratio>=0 && $Ratio <=1)
			{
				VLM_raw_move_loxo($CurLat*1000, $CurLon*1000, $distVac*$Ratio, $tc, $Nextlat, $Nextlon);
				
				echo "\nIntersection ".(doublep_value($Nextlon)/1000)." ".(doublep_value($Nextlat)/1000)."\n";
				$coast_xinglat = $Nextlat;
				$coast_xinglong = $Nextlon;
				$coast_xingratio = new doublep;
				$crosses_the_coast=1;
				break;
			}
		}
		else
		{
			echo " No intersect : ".$Ratio."\n";
		}
		
		/*if (IntersectOrthodromes($Lon1,$Lat1,$tc,$CurLon,$CurLat,$fullUsersObj->users->boatheading, $Lon, $Lat)==1)
		{
			$distintersect = SurfaceDistance($Lon,$Lat,$fullUsersObj->lastPositions->long/1000,$fullUsersObj->lastPositions->lat/1000);
			echo "\tCheck intersect distance from ".$distintersect. ">".$distVac."?\n";
			if ($distVac >= $distintersect)
			{
				echo "intersection @ ".$Lon." ".$Lat." ".$distintersect."\n";
				// update "caller variables" to mimic the coast crossing mechanism
				$coast_xinglat = $Lat;
				$coast_xinglong = $Lon;
				$coast_xingratio = $distintersect / $distVac;
			}
		} */
	}

?>
