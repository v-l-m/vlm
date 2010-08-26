<?php

require_once("base.class.php");

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

  function getLastPositions($id, $race)
  {
    // same query as getAnteLastPositions do use the DB cache.
    $query= "SELECT `time`, `long`, `lat`, `idusers` , `race` ".
      "FROM positions WHERE idusers = $id AND race = $race " .
      "ORDER BY `time` DESC LIMIT 2";

//      echo "REQUEST: $query \n";

    $result = wrapper_mysql_db_query_reader($query);
    $row = mysql_fetch_array($result, MYSQL_NUM);

    $this->time = $row[0];
    $this->long = $row[1];
    $this->lat = $row[2];
    $this->idusers = $row[3];
    $this->race = $row[4];

  }


  function getAnteLastPositions($id, $race)
  {
    $query= "SELECT `time`, `long`, `lat`, `idusers` , `race` ".
      "FROM positions WHERE idusers = $id AND race = $race " .
      "ORDER BY `time` DESC LIMIT 2";
    $result = wrapper_mysql_db_query_reader($query);
    //$result = wrapper_mysql_db_query($query);

    $row = mysql_fetch_array($result, MYSQL_NUM);
    $row = mysql_fetch_array($result, MYSQL_NUM);//we are not taking the last one

    $this->time = $row[0];
    $this->long = $row[1];
    $this->lat = $row[2];
    $this->idusers = $row[3];
    $this->race = $row[4];

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
    $row = mysql_fetch_array($result, MYSQL_NUM);

    return array($row[0], $row[1], $row[2]);

  }

  //  $newPos = addDistance2Positions($usersObj->lastPositions->long, $usersObj->lastPositions->lat, 
  //       $usersObj->boatspeed*$usersObj->hours, $usersObj->boatheading);  
  /*compute a new position from old position (milliDegree, speed (knt) and heading (geo)
   */

  function addDistance2Positions( $distance, $heading  )
  {
    include_once("vlmc.php");

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
      $idgrid=0;

      //printf ("West=%f, East=%f\n" , $west, $east);
      $pas_latitude=$maille*abs($north - $south)/10000;

      $min_longitude = floor($west/1000);
      if ( $west > 0 && $east < 0 ) {
         $pas_longitude=$maille*abs($east+360000 - $west)/10000;
   $max_longitude = 180;
      } else {
         $pas_longitude=$maille*abs($east - $west)/10000;
         $max_longitude = ceil($east/1000) ;
      }

      //printf ("Minlong=%f, Maxlong=%f\n" , $min_longitude, $max_longitude);

      $latitude = ceil($north/1000) ;
      // Boucle sur des parallèles (N->S), 
      while ( $latitude >= floor($south/1000)  ) {

    // Boucle sur les méridiens (W->E)
          $longitude = $min_longitude  ;
    // Pour les longitudes à l'ouest de Day Changing Line
    while ( $longitude <= $max_longitude +1 )  {
        // Instanciation du point de grille (Long, Lat, wspeed, wheading)
        $fullGridObj = new fullGrid ( $latitude*1000, $longitude*1000 , $timestamp);
        array_push ($this->records, $fullGridObj);
              //printf("Latitude : %d, Longitude : %d, idgrid : %d\n", $latitude, $longitude, $idgrid);
        //printf ("Vent : %d au %d\n" , $fullGridObj->wspeed, $fullGridObj->wheading);
        $idgrid++;

        $longitude+=$pas_longitude;
          }
    //printf ("Num gridpoints = %f, " , $idgrid);

    // Pour les longitudes à l'est de Day Changing Line
    if ( $west >0 && $east< 0 ) {
              $longitude -=360  ;
        while ( $longitude <= ceil($east/1000) )  {
            // Instanciation du point de grille (Long, Lat, wspeed, wheading)
            $fullGridObj = new fullGrid ( $latitude*1000, $longitude*1000 , $timestamp);
            array_push ($this->records, $fullGridObj);
            $idgrid++;

            $longitude+=$pas_longitude;
              }
    }
    //printf ("Num gridpoints = %f\n" , $idgrid);

          $latitude-=$pas_latitude;
      }
  }

}


?>
