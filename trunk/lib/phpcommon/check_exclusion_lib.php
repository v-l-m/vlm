<?php

// Return distance in Naut. Miles between 2 points
function SurfaceDistance($lon1, $lat1, $lon2, $lat2)
{
	$EARTH_RADIUS = 3443.84;
	// Convert all angles, pos to radians (and reverse E/W to match formula standard)
	$lon1=-$lon1/180*pi();
	$lat1=$lat1/180*pi();
	$lon2=-$lon2/180*pi();
	$lat2=$lat2/180*pi();
	
	return 2*asin(sqrt(pow(sin(($lat1-$lat2)/2),2) + pow(cos($lat1)*cos($lat2)*(sin(($lon1-$lon2)/2)),2))) * $EARTH_RADIUS;
}

// Return intersection point for orthodromes defined with Lon1, Lat1, Bearing1 and 
// Lon2, Lat2, Bearing2
function  IntersectOrthodromes ($Lon1, $Lat1,$Bearing1, $Lon2, $Lat2,$Bearing2,&$Lon3, &$Lat3)
{


	if ($Bearing1 > 180) 
	{
		$Bearing1 = $Bearing1 - 360;
	}

	if ($Bearing2 > 180) 
	{
		$Bearing2 = $Bearing2 - 360;
	}
echo "\n";
echo "Lon1 : " . $Lon1." Lat1 : ".$Lat1." Crs13 : ".$Bearing1."\n"; 
echo "Lon2 : " . $Lon2." Lat2 : ".$Lat2." Crs23 : ".$Bearing2."\n"; 

	// Convert all angles, pos to radians (and reverse E/W to match formula standard)
	$L1 = $Lon1;
	$L2 = $Lon2;
	$La1 = $Lat1;
	$La2 = $Lat2;
	
	$Lon1=-$Lon1/180.*pi();
	$Lat1=$Lat1/180.*pi();
	$crs13= $Bearing1/180.*pi();
	
	$Lon2=-$Lon2/180.*pi();
	$Lat2=$Lat2/180.*pi();
	$crs23= $Bearing2/180.*pi();
	
//Now how to compute the latitude, lat3, and longitude, lon3 of an intersection formed by the crs13 true bearing from point 1 and the crs23 true bearing from point 2:
/*echo "\n";
echo "Lon1 : " . $Lon1." Lat1 : ".$Lat1." Crs13 : ".$crs13."\n"; 
echo "Lon2 : " . $Lon2." Lat2 : ".$Lat2." Crs23 : ".$crs23."\n"; 
echo "\nSqrt " .pow(sin(($Lat1-$Lat2)/2.),2);
*/
	$dst12=2.* asin(sqrt(pow(sin(($Lat1-$Lat2)/2.),2)+pow(cos($Lat1)*cos($Lat2)*sin(($Lon1-$Lon2)/2.),2.)));
	
echo "dst12 : " . $dst12."\n"; 
	
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
	
	echo "\n\t angles @".($ang1/pi()*180)." ".($ang2/pi()*180)."\n";
	echo "\n\t courses @".($crs12/pi()*180)." ".($crs21/pi()*180)." ".($crs13/pi()*180)."\n";
	
	
	if (sin($ang1)==0 && sin($ang2)==0)
	{
	   //"infinity of intersections"
	   echo "\n\t infinity of intersections";
	   return 0;
	}
	/*elseif (sin($ang1)*sin($ang2)<0)
	{
	   //"intersection ambiguous"
	   echo "\n\t ambiguous intersections";
	   return 0;
	}*/
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
		
		echo "\n\t intersection @".$Lon3." ".$Lat3."\n";
		echo "\t surface distance from p1 ".SurfaceDistance ($L1,$La1,$Lon3,$Lat3)."\n";
		return 1;
	}

}

// Return true course from P1 to P2 along orthodrome (function is probably in vlm-c)
// Needs fixing
// Return is in deg
function GetTrueCourse($lon1, $lat1, $lon2, $lat2)
{
	$lon1 = -$lon1/180*pi();
	$lon2 = -$lon2/180*pi();
	$lat1 = $lat1/180*pi();
	$lat2 = $lat2/180*pi();
	
	
	$Tc= fmod(atan2(sin($lon1-$lon2)*cos($lat2),cos($lat1)*sin($lat2)-sin($lat1)*cos($lat2)*cos($lon1-$lon2)), 2*pi())/pi()*180;
	$Tc = fmod($Tc + 360,360);
	return $Tc;
}


//
// Unit test of the GetTrueCourseFunction
//
function TestGetTrueCourse()
{
  echo "\nTesting GetTrueCourse\n";
	$tc = GetTrueCourse(0,0,1,0);
	echo $tc."=90\n";
	if ($tc <> 90) 
	{
		die;
	}
	
	$tc = GetTrueCourse(0,0,0,1);
	echo $tc."=0\n";
	if ($tc <> 0) 
	{
		die;
	}
	
	$tc = GetTrueCourse(0,0,-1,0);
	echo $tc."=270\n";
	if ($tc <> 270) 
	{
		die;
	}
	
	$tc = GetTrueCourse(0,0,0,-1);
	echo $tc."=180\n";
	if ($tc <> 180) 
	{
		die;
	}
	
	$tc = GetTrueCourse(0,0,10,10);
	echo $tc."=44.561451413258\n";
	if (abs ($tc - 44.561451413258) > 1e-6)
	{
		echo "\n error ".($tc - 44.561451413258)."\n";
		die;
	}
	
}

// Returns closest longitude where GC crosses a given lat. Returns 1 if found else 0
function GetGCLonAtLat($lon1,$lat1,$lon2,$lat2,$curlon,$lat3, &$lon3)
{
	// Fix signs and convert all to rads
	$lon1=-$lon1/180*pi();
	$lat1=$lat1/180*pi();
	$lon2=-$lon2/180*pi();
	$lat2=$lat2/180*pi();
	$curlon=-$curlon/180*pi();
	$lat3=$lat3/180*pi();
	

	$l12 = $lon1-$lon2;
	$A = sin($lat1)*cos($lat2)*cos($lat3)*sin($l12);
	$B = sin($lat1)*cos($lat2)*cos($lat3)*cos($l12) - cos($lat1)*sin($lat2)*cos($lat3);
	$C = cos($lat1)*cos($lat2)*sin($lat3)*sin($l12);
	$lon = atan2($B,$A); //                      ( atan2(y,x) convention)
	if (abs($C) >sqrt(pow($A,2) + pow($B,2)))
	{
		return 0;
		//"no crossing"
	}
	else
	{
		$dlon = acos($C/sqrt(pow($A,2) + pow($B,2)));
		$lon3_1=fmod($lon1+$dlon+$lon+pi(), 2*pi())-pi();
		$lon3_2=fmod($lon1-$dlon+$lon+pi(), 2*pi())-pi();
		$d1 = SurfaceDistance($lon3_1,$lat3,$curlon,$lat3);
		$d2 = SurfaceDistance($lon3_2,$lat3,$curlon,$lat3);
		//echo "dists : ".$d1." ".$d2." \n";
		if ($d1<=$d2)
		{
			$lon3 = -$lon3_1/pi()*180;
		}
		else
		{
			$lon3 = -$lon3_2/pi()*180;
		}
		return 1;
	}
}
	
function TestExclusionLib()
{
	TestGetTrueCourse();
  testIntersect1();
}

function testIntersect1()
{
  echo "\n Test basic segments intersections\n";
  
  $ret = SegmentsIntersect(0,0,1,0,0,0,0,1,$ratio);
  echo "1 ".$ret."=1 ".$ratio."=0\n";
  if (!$ret || $ratio!=0)
  {
    die;
  }
  
  $ret = SegmentsIntersect(0,0,1,0,0,1,1,0,$ratio);
  echo "2 ".$ret."=1 ".$ratio."=1\n";
  if (!$ret || $ratio!=1)
  {
    die;
  }
  
  $ret = SegmentsIntersect(0,0,1,0,1,2,2,2,$ratio);
  echo "3 ".$ret."=0 R=".$ratio."\n";
  if ($ret )
  {
    die;
  }
  
  
  $ret = SegmentsIntersect(1,1,2,2,0,0,1,0,$ratio);
  echo "4 ".$ret."=0 R=".$ratio."\n";
  if ($ret )
  {
    die;
  }
  
  $ret = SegmentsIntersect(1,1,2,2,1.5,1.5,2,1.5,$ratio);
  echo "5 ".$ret."=1 ".$ratio."=0.5\n";
  if (!$ret || abs($ratio -0.5) > 1e-10)
  {
    echo "\ndying\n";
    if (!$ret)
    {
      echo "wrong return value\n";
    }
    if ($ratio != 0.5)
    {
      echo "wrong ratio value".$ratio." ".($ratio-0.5)."\n" ;
    }
    
    die;
  }
  
  $ret = SegmentsIntersect(0,0,1,0,1,1,2,2,$ratio);
  echo "6 ".$ret."=0 R=".$ratio."\n";
  if ($ret )
  {
    die;
  }
  
}

// Return is both segments intersects and return the ratio on segment AB from A for intersection
// derived from http://alienryderflex.com/intersect/ sample source code
//  public domain function by Darel Rex Finley, 2006



//  Determines the intersection point of the segment defined by points A and B with the
//  segment defined by points C and D.
//
//  Returns YES if the intersection point was found, and return in $Ratio the ratio from A on distance AB.
//  Returns NO if there is no determinable intersection point, in which case Ratio will
//  be unmodified.

function SegmentsIntersect($Ax, $Ay, $Bx, $By, $Cx, $Cy, $Dx, $Dy,&$Ratio)
{
	//  Fail if either line is undefined.
	if ($Ax==$Bx && $Ay==$By || $Cx==$Dx && $Cy==$Dy) 
	{
		return 0;
	}

	//  (1) Translate the system so that point A is on the origin.
	$Bx-=$Ax; $By-=$Ay;
	$Cx-=$Ax; $Cy-=$Ay;
	$Dx-=$Ax; $Dy-=$Ay;
  $Ax = 0; $Ay=0;

	//  Discover the length of segment A-B.
	$DistAB=sqrt($Bx*$Bx+$By*$By);

	//  (2) Rotate the system so that point B is on the positive X axis.
	$theCos=$Bx/$DistAB;
	$theSin=$By/$DistAB;
	$newX=$Cx*$theCos+$Cy*$theSin;
	$Cy  =$Cy*$theCos-$Cx*$theSin; $Cx=$newX;
	$newX=$Dx*$theCos+$Dy*$theSin;
	$Dy  =$Dy*$theCos-$Dx*$theSin; $Dx=$newX;

	//  Fail if the lines are parallel.
	if ($Cy==$Dy)
	{
		return 0;
	}

	//  (3) Discover the position of the intersection point along line A-B.
	$ABpos=$Dx+($Cx-$Dx)*$Dy/($Dy-$Cy);
  //echo "\n".$ABpos."=".$Dx."+(".$Cx."-".$Dx.")*".$Dy."/(".$Dy."-".$Cy.")\n";
 
	$Ratio = $ABpos / $DistAB;
	//echo "\n Segments Intersect dist : ".$ABpos." ratio : ".$Ratio."\n";
  
	if ($Ratio >= 0 && $Ratio <=1)
	{
		// Possible Success
    // Check other segment ratio
    
    // Get Intersect coords
    $Ix = $Ax+$ABpos;
    $Iy = $Ay;
    //echo " intersect ".$Ix." ".$Iy."\n";

    if (($Dx - $Cx) != 0)
    {
      // Seg is not vertical
      $Ratio2 = ($Ix - $Cx) / ($Dx - $Cx);
    }
    elseif ( ($Dy - $Cy) != 0)
    {
      // Seg is vertical
      $Ratio2 = ($Iy - $Cy) / ($Dy - $Cy);
    }
    else
    {
      // No segment !!
      return 0;
    }
    
    //echo "\n Ratio2 ".$Ratio2."\n";
    if ( ($Ratio2>=0) && ($Ratio2 <=1))
    {
      return 1;
    }
    else
    {
      return 0;
    }

  }
	else
	{
		// Segments do not intersect
		return 0;
	}
	
}
?>