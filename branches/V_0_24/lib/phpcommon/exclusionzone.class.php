<?php

require_once('base.class.php');

class exclusionZone extends baseclass
{
	// Declare local members.
  var $Exclusions=array();
  var $activeZoneName="No Exclusions";
  
  // Class construction, use to init the exclusion zone list for a given race
  function exclusionZone($idRace)
  {
    // Get exclusion zones def from database
    $query = "select lon1, lat1, lon2, lat2 from nszsegment S inner join nszracesegment RS on S.idsegment = RS.idsegment and RS.idraces = ".$idRace.";";
    //echo "sql : ".$query;
    $res = $this->queryRead($query);
    if ($res) 
    {
        while ($line = mysql_fetch_assoc($res)) 
        {
          $lon1 =$line["lon1"]/1000;
          $lat1 =$line["lat1"]/1000;
          $lon2 =$line["lon2"]/1000;
          $lat2 =$line["lat2"]/1000;
          
          $this->Exclusions[] = array(array($lat1,$lon1),array($lat2,$lon2)); 
        }
        $this->activeZoneName="DB Loaded exclusion zone ".$idRace;
    }    
  }
  
  // Returns the list of exclusions zones
  function exclusions()
  {
    return $this->Exclusions;
  }
  
  // Return the active exclusion zone name.
  function getActiveZoneName()
  {
    return $this->activeZoneName;
  }
  
}
?>