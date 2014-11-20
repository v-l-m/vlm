<?php

// Return distance in Naut. Miles between 2 points
function SurfaceDistance($lon1, $lat1, $lon2, $lat2)
{
	define (EARTH_RADIUS,3443.84);
	// Convert all angles, pos to radians (and reverse E/W to match formula standard)
	$lon1=-$lon1/180*pi();
	$lat1=$lat1/180*pi();
	$lon2=-$lon2/180*pi();
	$lat2=$lat2/180*pi();
	
	return 2*asin(sqrt(pow(sin(($lat1-$lat2)/2),2) + pow(cos($lat1)*cos($lat2)*(sin(($lon1-$lon2)/2)),2))) * EARTH_RADIUS;
}

// Return intersection point for orthodromes defined with Lon1, Lat1, Bearing1 and 
// Lon2, Lat2, Bearing2
function  IntersectOrthodromes ($Lon1, $Lat1,$Bearing1, $Lon2, $Lat2,$Bearing2,&$Lon3, &$Lat3)
{

	// Convert all angles, pos to radians (and reverse E/W to match formula standard)
	$Lon1=-$Lon1/180*pi();
	$Lat1=$Lat1/180*pi();
	$crs13= $Bearing1/180*pi();
	
	$Lon2=-$Lon2/180*pi();
	$Lat2=$Lat2/180*pi();
	$crs23= $Bearing2/180*pi();
	
//Now how to compute the latitude, lat3, and longitude, lon3 of an intersection formed by the crs13 true bearing from point 1 and the crs23 true bearing from point 2:
/*echo "\n";
echo "Lon1 : " . $Lon1." Lat1 : ".$Lat1." Crs13 : ".$crs13."\n"; 
echo "Lon2 : " . $Lon2." Lat2 : ".$Lat2." Crs23 : ".$crs23."\n"; 
echo "\nSqrt " .pow(sin(($Lat1-$Lat2)/2.),2);
*/
	$dst12=2.*asin(sqrt(pow(sin(($Lat1-$Lat2)/2.),2)+pow(cos($Lat1)*cos($Lat2)*sin(($Lon1-$Lon2)/2.),2.)));
	
//echo "dst12 : " . $dst12."\n"; 
	
	if (sin($Lon2-$Lon1)<0)
	{
	   $crs12=acos((sin($Lat2)-sin($Lat1)*cos($dst12))/(sin($dst12)*cos($Lat1)));
	   $crs21=2.*pi()-acos((sin($Lat1)-sin($Lat2)*cos($dst12))/(sin($dst12)*cos($Lat2)));
	}
	else
	{
	   $crs12=2.*pi()-acos((sin($Lat2)-sin($Lat1)*cos($dst12))/(sin($dst12)*cos($Lat1)));
	   $crs21=acos((sin($Lat1)-sin($Lat2)*cos($dst12))/(sin($dst12)*cos($Lat2)));
	}
	$ang1=fmod($crs13-$crs12+pi(),2.*pi())-pi();
	$ang2=fmod($crs21-$crs23+pi(),2.*pi())-pi();

	if (sin($ang1)==0 && sin($ang2)==0)
	{
	   //"infinity of intersections"
	   return 0;
	}
	elseif (sin($ang1)*sin($ang2)<0)
	{
	   //"intersection ambiguous"
	   return 0;
	}
	else
	{
		$ang1=abs($ang1);
		$ang2=abs($ang2);
		$ang3=acos(-cos($ang1)*cos($ang2)+sin($ang1)*sin($ang2)*cos($dst12)) ;
		$dst13=atan2(sin($dst12)*sin($ang1)*sin($ang2),cos($ang2)+cos($ang1)*cos($ang3));
		$Lat3=asin(sin($Lat1)*cos($dst13)+cos($Lat1)*sin($dst13)*cos($crs13));
		$dlon=atan2(sin($crs13)*sin($dst13)*cos($Lat1),cos($dst13)-sin($Lat1)*sin($Lat3));
		$Lon3=fmod($Lon1-$dlon+pi(),2*pi())-pi();
		
		// Convert back coords to degs
		$Lon3 = -$Lon3/pi()*180;
		$Lat3 = $Lat3/pi()*180;
		return 1;
	}

}
?>