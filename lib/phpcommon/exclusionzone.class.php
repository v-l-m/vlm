<?php

class exclusionZone
{
	// Declare local members.
  var $Exclusions=array();
  var $activeZoneName="No Exclusions";
  
  // Class construction, use to init the exclusion zone list for a given race
  function exclusionZone($idRace)
  {
    
    if ($idRace == 141502)
    {
      // VOR 2014 Leg2 Madagascar
      $p1=array(-25.976217, 32.98825);  // 25° 58.573'S 32° 59.295'E
      $p2=array(-25.590117, 45.143317); // 25° 35.407'S 45° 08.599'E
      $p3=array(-20.51015,57.396433 );  // 20° 30.609'S 57° 23.786'E
      
      // VOR Leg 2 Iranian Zone
      $p4=array(24.671667,53.938333);		//	24º 40.300N 53º 56.300E
      $p5=array(24.833333,61.561667);		//	24º 50.000N 61º 33.700E
      $p6=array(25.071667,60.581667);		//	25º 04.300N 60º 34.900E
      $p7=array(25.193333,59.576667);		//	25º 11.600N 59º 34.600E
      $p8=array(25.2,59.093333);			//	25º 12.000N 59º 05.600E
      $p9=array(25.458333,57.656667);		//	25º 27.500N 57º 39.400E
      $p10=array(25.576667,55.191667);		//	25º 34.600N 55º 11.500E
      $p11=array(25.66,57.158333);			//	25º 39.600N 57º 09.500E
      $p12=array(26.248333,55.71);			//	26º 14.900N 55º 42.600E
      $p13=array(26.315,56.795);			//	26º 18.900N 56º 47.700E
      $p14=array(26.606667,56.301667);		//	26º 36.400N 56º 18.100E
      $p15=array(26.71,56.558333);			//	26º 42.600N 56º 33.500E 


      // Vor 2 Strait of hormuz obstruction
      /*$p7=array(	26° 27.500'N 56° 35.500'E
      $p7=array(	26° 32.400'N 56° 29.000'E
      $p7=array(	26° 32.300'N 56° 32.300'E
      $p7=array(	26° 30.100'N 56° 23.200'E
      */
      
      $this->activeZoneName="VOR Leg 2";
      $this->Exclusions = array ( array($p1,$p2), array($p2,$p3),
                  array($p4,$p5), array($p5,$p6), array($p6,$p7),array($p7,$p8), array($p8,$p9), array($p9,$p10),
                  array($p10,$p11), array($p11,$p12), array($p12,$p13),array($p13,$p14), array($p14,$p15)
                );
    }
  }
  
  // Returns the list of exclusions zones
  function exclusions()
  {
    return $this->Exclusions;
  }
  
  // Return the active exclusion zone name
  function getActiveZoneName()
  {
    return $this->activeZoneName;
  }
  
}
?>