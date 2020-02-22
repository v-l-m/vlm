<?php

  include('config.php');
  require('racesiterators.class.php');
  
  class JsonRacesIterator extends RacesIterator {
    var $query = "SELECT * FROM races
                  WHERE ( ( started = 0 AND deptime > UNIX_TIMESTAMP() ) OR ( started = 1 ) )
                  ORDER BY started ASC, deptime ASC, closetime ASC ";
    var $jsonarray;

    function __construct($iduser=-1, $OldRaces=0) 
    {      
      if ($OldRaces)
      {
        if ($OldRaces > 200)
        {
          $OldRaces=($OldRaces-200).",".$OldRaces;
        }
        $this->query = "SELECT * FROM races
                  ORDER BY started ASC, deptime desc, closetime ASC limit ".$OldRaces;
      }
      parent::__construct($iduser);      
    }

    function listing() 
    {
      $this->start();
      $res = wrapper_mysql_db_query_reader($this->query) or die("Query [".$this->query."] failed \n");
      while ($row = mysqli_fetch_assoc($res) ) $this->onerow($row);
      $this->end();
    }

    function onerow($row) 
    {
      $row['idraces'] = (int) $row['idraces'];
      $row['started'] = (int) $row['started'];
      $row['deptime'] = (int) $row['deptime'];
      $row['closetime'] = (int) $row['closetime'];
      $row['startlong'] = (float) $row['startlong']/1000.;
      $row['startlat'] = (float) $row['startlat']/1000.;
      $row['racetype'] = (int) $row['racetype'];
      $row['firstpcttime'] = (int) $row['firstpcttime'];
      $row['coastpenalty'] = (int) $row['coastpenalty'];
      $row['bobegin'] = (int) $row['bobegin'];
      $row['boend'] = (int) $row['boend'];
      $row['maxboats'] = (int) $row['maxboats'];
      $row['vacfreq'] = (int) $row['vacfreq'];
      $row['updated'] = $row['updated'];
      $row['CanJoin'] = in_array($row['idraces'],$this->AvRaces);

      // If race complete for winner, then compute race closing date
      // FIXME - Should be refactored with desc.php, in races class
      $rnkQuery= "SELECT RR.position as status, RR.duration + RR.penalty duration, RR.idusers idusers,  RR.deptime deptime
      FROM      races_results RR, users US
      WHERE     idraces=".$row['idraces'].
      " AND       US.idusers = RR.idusers
      AND       position=1
      order by RR.duration+RR.penalty desc
      limit 1;";
      $res = wrapper_mysql_db_query_reader($rnkQuery);
      if ($res)
      {
        while ($ri = mysqli_fetch_assoc($res)) 
        {
          //var_dump($ri);
          if ($row['racetype']===RACE_TYPE_RECORD)
          {
            $row["RaceCloseDate"] = $row['closetime']+$ri['duration']*(1+$row['firstpcttime']);
          }
          else
          {
            $row["RaceCloseDate"] = $row['deptime']+$ri['duration']*(1+$row['firstpcttime']/100);
          }

          break;
        }
      }
      $this->jsonarray[$row['idraces']] = $row;
    }
        
    function start() 
    {
      $this->jsonarray = Array();

      if ($this->IdUser)
      {
        $this->AvRaces = AvailableRaces($this->IdUser);
      }
      else
      {
        $this->AvRaces = [];
      }
    }
    
    function end() 
    {
        echo json_encode($this->jsonarray);
    }

  }

  header('Content-type: application/json; charset=UTF-8');
  if(array_key_exists('iduser',$_REQUEST))
  {
    $iduser=htmlentities(quote_smart($_REQUEST['iduser']));
  }
  else
  {
    $iduser = null;
  }
  $OldRaces=0;
  if (array_key_exists("OldRaces", $_REQUEST))
  {
    $OldRaces=htmlentities(quote_smart($_REQUEST['OldRaces']));
  }  
  new JsonRacesIterator($iduser,$OldRaces);
?>
