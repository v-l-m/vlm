<?php

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

    $result = wrapper_mysql_db_query(DBNAME,$query);
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
    $result = wrapper_mysql_db_query(DBNAME,$query);
    //$result = wrapper_mysql_db_query(DBNAME,$query);

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

    $result = wrapper_mysql_db_query(DBNAME,$query);
    $row = mysql_fetch_array($result, MYSQL_NUM);

    return array($row[0], $row[1], $row[2]);

  }

  //  $newPos = addDistance2Positions($usersObj->lastPositions->long, $usersObj->lastPositions->lat, 
  //			 $usersObj->boatspeed*$usersObj->hours, $usersObj->boatheading);  
  /*compute a new position from old position (milliDegree, speed (knt) and heading (geo)
   */

  function addDistance2Positions( $distance, $heading  )
  {
    $vector = polar2cartesian  ($heading, $distance);

    // Latitude, no problem
    $this->lat = $this->lat + $vector[1]*MILDEGREE2NAUTICS; //true only for latitude

    // Longitude, we have to handle the +180/-180 crossing
    $this->long = $this->long +  $vector[0]*1000 / (60*cos(deg2rad($this->lat/1000)));  

    // Handle day changing line crossing West --> East (we work in millidegrees)
    if ( $this->long > 180000 ) $this->long-=360000;

    // Handle day changing line crossing East --> West (we work in millidegrees)
    if ( $this->long < -180000 ) $this->long+=360000;

  } 

  function writePositions()
  {
    $query7 = "INSERT INTO positions " .
              " SET time = ". time().
              ", `long` =". $this->long .
              ", `lat` =". $this->lat.
              ", idusers = ".$this->idusers .
              ", `race` = ".$this->race;
    wrapper_mysql_db_query(DBNAME,$query7);

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

// A list of the positions of the same player
class positionsList
{
  var $records  = array(),
    $idusers;

  // Mintime et Maxtime sont soit 0 (= maintenant), ou un timestamp.
  function positionsList($idusers, $race, $mintime = 0, $maxtime = 0 )
  {
   // Première position prise en compte : maintenant moins il y a DEF_POS_AGE si pas précisé.
   //                                     sinon, c'est un timestamp de début
   if ( $mintime == 0 ) $mintime = time() - DEFAULT_POSITION_AGE;

   // Dernière position prise en compte : maintenant si 0, spécifiée (timestamp de fin) sinon (cas des blackouts)
   if ( $maxtime == 0 ) $maxtime = time();

   $query= "SELECT `time`, `long`, `lat` ".
      " FROM positions " . 
      " WHERE idusers =  " . $idusers . 
      " AND race =" .  $race  .
      " AND `time` > " . $mintime .  
      " AND `time` < " . $maxtime .  
      " ORDER BY `time` ASC";
    $result = wrapper_mysql_db_query(DBNAME,$query);

    while ($row = mysql_fetch_array($result, MYSQL_NUM) ) {
	    $pos = new positions();
	    $pos->time = $row[0];
	    $pos->long = $row[1];
	    $pos->lat = $row[2];
	    $pos->idusers = $idusers;
	    $pos->race = $race;
      	    array_push ($this->records, $pos);
      }
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
           $vent = windAtPosition($latitude, $longitude, $timestamp, 'OLD');
      } else {
           $vent = windAtPosition($latitude, $longitude, 0, 'OLD');
      }

      $this->wspeed = $vent[0];
      $this->wheading = $vent[1];
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
