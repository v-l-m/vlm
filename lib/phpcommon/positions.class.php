<?php

require_once("base.class.php");
require_once("vlmc.php");

class positions
{
  var $time ,
    $long, $lat,
    $idusers, $race;

  function positions()
  {
    $this->time = 0;
    $this->long = 0;
    $this->lat = 0;
    $this->idusers = 0;
    $this->race= 0;
    
  }

  function init($row) {
    $this->time    = int($row['time']);
    $this->long    = float($row['long']);
    $this->lat     = float($row['lat']);
    $this->idusers = int($row['idusers']);
    $this->race    = int($row['race']);
  }

  function getLastPositions($id, $race)
  {
    // same query as getAnteLastPositions do use the DB cache.
    $query= "SELECT `time`, `long`, `lat`, `idusers` , `race` ".
      "FROM positions WHERE idusers = $id AND race = $race " .
      "ORDER BY `time` DESC LIMIT 2";

//      echo "REQUEST: $query \n";

    $result = wrapper_mysql_db_query_reader($query);
    if (!$result) {
      return;
    }
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    if (!$row) {
      return;
    }
    $this->init($row);
  }


  function getAnteLastPositions($id, $race)
  {
    $query= "SELECT `time`, `long`, `lat`, `idusers` , `race` ".
      "FROM positions WHERE idusers = $id AND race = $race " .
      "ORDER BY `time` DESC LIMIT 2";
    $result = wrapper_mysql_db_query_reader($query);
    //$result = wrapper_mysql_db_query($query);
    if (!$result) {
      return;
    }
    $row = mysql_fetch_array($result, MYSQL_NUM);//we are not taking the last one
    if (!$row) {
      return;
    }
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    if (!$row) {
      return;
    }
    $this->init($row);
  }

  // Used by ranking_results for last1h, last3h, last24h
  // returns a timestamp, a longitude and a latitude
  // 
  function getOldPosition($id, $race, $min_timestamp)
  {
    $query= "SELECT `time`, `long`, `lat` ".
      " FROM positions WHERE idusers =  $id  AND race = $race " .
      " AND  time > " . ( $min_timestamp - DELAYBETWEENUPDATE ) .
      " ORDER BY time ASC LIMIT 1";
    //echo "REQUEST: $query \n";

    $result = wrapper_mysql_db_query_reader($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);

    return array($row['time'], $row['long'], $row['lat']);

  }

  //  $newPos = addDistance2Positions($usersObj->lastPositions->long, $usersObj->lastPositions->lat, 
  //       $usersObj->boatspeed*$usersObj->hours, $usersObj->boatheading);  
  /*compute a new position from old position (milliDegree, speed (knt) and heading (geo)
   */

  function addDistance2Positions( $distance, $heading  )
  {
    $new_lat = new doublep();
    $new_long = new doublep();
    
    VLM_raw_move_loxo($this->lat, $this->long, $distance, $heading, 
		      $new_lat, $new_long);

    $this->lat  = doublep_value($new_lat);
    $this->long = doublep_value($new_long);
  } 

  function writePositions($updatetime = 1)
  {
    if ($updatetime == 1) {
      $this->time = time();
    }
    $query7 = "INSERT INTO positions SET time = ". $this->time .
              ", `long` =". $this->long .
              ", `lat` =". $this->lat.
              ", idusers = ".$this->idusers .
              ", `race` = ".$this->race;
    wrapper_mysql_db_query_writer($query7);
  }

  function writeDefaultPositions($id, $race)
  {
    $races=new races($race);
    $this->long = $races->startlong;
    $this->lat = $races->startlat;
    $this->idusers = $id;
    $this->race = $race;
    $this->writePositions();
  }

  /*returns FALSE if it's false*/
  // N'est utilisée que par le moteur.
  // N'a plus de sens avec la nouvelle gestion meteo qui couvre tout le globe
  function isInsideGrid()
  {
      return (TRUE);
  }
  
}

class positionsIterator extends baseClass {

    //FIXME : should extended from a baseIteratorClass;
    
    var $roundstep, $idusers, $idraces, $mintime, $maxtime ;
    var $records  = array();
    
    function __construct( $idusers, $idraces, $mintime = null, $maxtime = null, $roundstep = null ) {
        if (is_null($roundstep)) $roundstep = DELAYBETWEENUPDATE;
        $this->roundstep = intval($roundstep);
        if (is_null($mintime) || $mintime == 0) $mintime = time() - DEFAULT_POSITION_AGE;
        if (is_null($maxtime) || $maxtime == 0) $maxtime = time();
        
        //Time normalizationparent::__construct();
        $this->mintime = $this->roundToVac($mintime);
        $this->maxtime = $this->roundToVac($maxtime)+$this->roundstep;
        $this->idusers = intval($idusers);
        $this->idraces = intval($idraces);

        //do it
        $this->listing();
    }
    
    function roundToVac($time) {
        //This is for normalizing queries;
        if ($this->roundstep < 60) return $time;
        return (intval($time/$this->roundstep)*intval($this->roundstep));
    }
    
    function getQuery() {
        $query =  "SELECT `time`, `long`, `lat` ".
                  " FROM `positions` " . 
                  " WHERE `idusers` = " . $this->idusers . 
                  " AND `race` = " .  $this->idraces  .
                  " AND `time` > " . $this->mintime .  
                  " AND `time` < " . $this->maxtime .  
                  " ORDER BY `time` ASC";

        return $query;
    }

    function listing() {
        $result = $this->queryRead($this->getQuery());
        if ($this->error_status) return;
        $this->start();
        while ($row = mysql_fetch_array($result, MYSQL_NUM) ) $this->onerow($row);
        $this->end();
    }

    function onerow($row) {
        array_push ($this->records, $row);
    }

    function start() {
        $this->records = Array();
    }
    
    function end() {
    }

}

class fullPositionsIterator extends positionsIterator {
    function getQuery() {
        $query =  "(".
                  "SELECT `time`, `long`, `lat` ".
                  " FROM `positions` " . 
                  " WHERE `idusers` = " . $this->idusers . 
                  " AND `race` = " .  $this->idraces  .
                  " AND `time` > " . $this->mintime .  
                  " AND `time` < " . $this->maxtime .
                  ") UNION (".
                  "SELECT `time`, `long`, `lat` ".
                  " FROM `histpos` " . 
                  " WHERE `idusers` = " . $this->idusers . 
                  " AND `race` = " .  $this->idraces  .
                  " AND `time` > " . $this->mintime .  
                  " AND `time` < " . $this->maxtime .
                  ") ORDER BY `time` ASC";
        return $query;
    }
}

// A list of the positions of the same player
class positionsList extends positionsIterator {

    function onerow($row) {
        $pos = new positions();
        $pos->time = $row[0];
        $pos->long = $row[1];
        $pos->lat = $row[2];
        $pos->idusers = $this->idusers;
        $pos->race = $this->idraces;

        array_push ($this->records, $pos);
    }
}

// La structure fullGrid contient lat, long, wspeed, et wheading
// Elle est utilisée pour positionner les vecteurs de vent sur les cartes
class fullGrid
{
  var $Lat,
      $Long,
      $wspeed,
      $wheading;

  //constructor, by position
  function fullGrid($latitude, $longitude, $timestamp = 0) {
      $vent = array();

      $this->Lat =  $latitude;
      $this->Long = $longitude;

      // Recherche de la donnée vent de ce point
      if ( $timestamp > 0 ) {
           $vent = windAtPosition($latitude, $longitude, $timestamp);
      } else {
           $vent = windAtPosition($latitude, $longitude, 0);
      }

      $this->wspeed = $vent['speed'];
      $this->wheading = $vent['windangle'];
    }
}

// GRIDLIST
class gridList
{
  var $records = array();

  // Utilisé par mercator.img.php
  // Cette nouvelle version de gridList est plus performante que l'ancienne
  // car elle ne prend que les points se situant sur la zone à cartographier
  function gridList($north, $south, $west, $east, $maille, $timestamp = 0)
  {
    $lat_step=$maille*abs($north - $south)/10;
    
    $min_lon = floor($west/1000)*1000;
    if ($west > 0 && $east < 0) {
      $lon_step=$maille*abs($east+360000-$west)/10;
      $max_lon = 180000;
    } else {
      $lon_step=$maille*abs($east-$west)/10;
      $max_lon = ceil($east/1000)*1000;
    }
    
    $south_limit = floor($south/1000)*1000;
    $north_limit =  ceil($north/1000)*1000;

    // For longitudes west of the International Date Line
    for ($lat=$north_limit; $lat >= $south_limit; $lat-=$lat_step) {
      for ($lon=$min_lon; $lon <= $max_lon; $lon+=$lon_step)  {
        $fullGridObj = new fullGrid($lat, $lon, $timestamp);
        array_push($this->records, $fullGridObj);
      }
    }

    if ($west >0 && $east< 0) {
      // recompute min_lon for longitudes east of the IDL
      $min_lon = -180000+$lon_step-fmod((180000-$min_lon), $lon_step);
      $east_limit = ceil($east/1000)*1000;
      for ($lat = $north_limit; $lat >= $south_limit; $lat-=$lat_step) {
	for ($lon = $min_lon; $lon <= $east_limit; $lon+=$lon_step) {
	  $fullGridObj = new fullGrid($lat, $lon, $timestamp);
	  array_push($this->records, $fullGridObj);
	}
      }
    }
  }
}
?>
