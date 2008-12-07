<?php
class users
{
  //var from db_users
  var $idusers,
      $boattype,
      $username,
      $password,
      $boatname,
      $color,
      $boatheading,
      $pilotmode,
      $pilotparameter,
      $engaged,
      $lastchange,
      $nwp,
      $userdeptime,
      $lastupdate,
      $loch,
      $country,
      $class,
      $targetlat,
      $targetlong,
      $targetandhdg,
      $mooringtime,
      $releasetime,
      $hidepos,
      $blocnote,
      $ipaddr,
      $pilototo;

  function users($id)
  {
    //  echo "constructeur users with $id \n";

    $query= "SELECT idusers, boattype, username, password,".
            " boatname, color, boatheading, pilotmode, pilotparameter,".
            " engaged, lastchange, email, nextwaypoint, userdeptime, " .
      " lastupdate, loch, country, class, targetlat,targetlong, targetandhdg, ".
            " mooringtime, releasetime, hidepos, blocnote, ipaddr  FROM  users WHERE idusers = ".$id;


//    $result = mysql_db_query(DBNAME,$query) or die("\n FAIL::::::: ".$query."\n");
    $result = mysql_db_query(DBNAME,$query) or die("\n FAILED !!\n");
    $row = mysql_fetch_array($result, MYSQL_NUM);

    $this->idusers = $row[0];
    $this->boattype = $row[1];
    $this->username = $row[2];
    $this->password = $row[3];
    $this->boatname = $row[4];
    $this->color = $row[5];
    $this->boatheading = $row[6];
    $this->pilotmode = $row[7]; 
    $this->pilotparameter = $row[8];
    $this->engaged = $row[9];
    $this->lastchange = $row[10];
    $this->email = $row[11];
    $this->nwp = $row[12];
    $this->userdeptime = $row[13];
    $this->lastupdate = $row[14];
    $this->loch = $row[15];
    $this->country = $row[16]; if ( strlen($this->country) < 3 ) $this->country="";
    $this->class = $row[17]; 
    $this->targetlat = $row[18]; 
    $this->targetlong = $row[19]; 
    $this->targetandhdg = $row[20]; 
    $this->mooringtime = $row[21]; 
    $this->releasetime = $row[22]; 
    $this->hidepos = $row[23]; 
    $this->blocnote = $row[24]; 
    if ( eregi("^http|://|script|language|<|>", $this->blocnote) ) {
        $this->blocnote="some characters are not valid in your notepad";
    } 
    $this->ipaddr = $row[25]; 
  }

  //update boatname and color
  function write()
  {
    //write everything in db
    $query = "UPDATE users SET `boatname` = '" . addslashes($this->boatname) . "'," . 
        " `color` = '" . $this->color . "'," . 
        " `email` = '" . $this->email . "'," . 
        " `country` = '" . $this->country . "'," . 
        " `hidepos` =  " . $this->hidepos . "," . 
        " `blocnote` = '" . mysql_real_escape_string( $this->blocnote) . "'" . 
        " WHERE idusers = " . $this->idusers;
    mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

    logUserEvent($this->idusers , $_SESSION['IP'] , $this->engaged, "Update prefs." );

  }

  // locks a boat (so the engine won't run for her) for a time (seconds) from now
  function lockBoat($time)
  {
    $this->releasetime = time() + $time;
    $query = "UPDATE users SET releasetime = " . $this->releasetime .  
           " WHERE idusers = " . $this->idusers;
    mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
  }


  // Check pilote auto returns true if an action was done, else false
  function pilototoCheck()
  {
    $flag_pilototo=false;

    $now=time();
    // lookup for a task to do
    $query = "SELECT taskid, pilotmode, pilotparameter FROM auto_pilot
         WHERE status='" . PILOTOTO_PENDING . "'
       AND idusers = $this->idusers
       AND time <= $now";
    echo $quety;
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

    while ( $row = mysql_fetch_array($result, MYSQL_NUM) ) {
        // Execute the task
        $PIM=$row[1];
  if ( $PIM == 0 OR $PIM > 4 ) $flag_err=true;
  $this->pilotmode=$PIM;

  $PIP=$row[2];

  printf( "** AUTO_PILOT : executing task %d, PIM=%d, PIP=%s... ** ", $row[0], $row[1], $row[2]);
  $query="UPDATE users SET pilotmode=$PIM ";

  if ( $PIM == PILOTMODE_HEADING ) {
     $this->boatheading=$PIP;
     $query .= ", boatheading=$PIP ";
  }
  if ( $PIM == PILOTMODE_WINDANGLE ) {
     $this->pilotparameter=$PIP;
     $query .= ", pilotparameter=$PIP ";
  }
  if ( $PIM == PILOTMODE_ORTHODROMIC or $PIM == PILOTMODE_BESTVMG ) {
     if ( strlen($PIP) != 0 && $PIP != "0" ) {
     $values = explode("@", $PIP);
     $Coords = explode(",", $values[0]);
     $query .= ", targetlat=round($Coords[0],4), targetlong=round($Coords[1],4) ";
           if ( round($values[1]) > 0 ) {
                $query .= ", targetandhdg=" . $values[1] ;
           } else {
                $query .= ", targetandhdg=-1 ";
           }
     echo $query;
     }
  }
  // Don't forget tu add the where clause... and execute the query
  $query .= " WHERE idusers=$this->idusers;";
        mysql_db_query(DBNAME,$query); //or die("Query failed : " . mysql_error." ".$query);

  // Mark the task as DONE
  $query = "UPDATE auto_pilot SET status = '" . PILOTOTO_DONE . "' WHERE taskid = $row[0];";
        mysql_db_query(DBNAME,$query); //or die("Query failed : " . mysql_error." ".$query);
        
        // Purge old tasks
        $this->pilototoPurge( PILOTOTO_KEEP );

        $flag_pilototo=true;
    }

    return ($flag_pilototo);
  }

  // Give the number of pilototo tasks in the given Status (pending by default)
  function pilototoCountTasks($status = PILOTOTO_PENDING)
  {
    // lookup for a task to do
    $query = "SELECT count(*) NumTasks FROM auto_pilot
     WHERE idusers = $this->idusers
       AND status = '" . $status . "'";
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
    //echo $query;

    if ( $row = mysql_fetch_array($result, MYSQL_NUM) ) {
       $numRows=$row[0];
    } else {
       $numRows=0;
    }

    return($numRows);
   
  }

  // Delete a task from pilototo
  // List the piltoto orders
  function pilototoList($status = PILOTOTO_PENDING)
  {
    $now=time();
    $this->pilototo=array();
    // lookup for a task to do
    $query = "SELECT taskid, time, pilotmode, pilotparameter, status FROM auto_pilot
     WHERE idusers = $this->idusers
       ORDER by time ASC";
//       AND status = '" . $status . "'
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

    //echo $query;
    while ( $row = mysql_fetch_array($result, MYSQL_NUM) ) {

    array_push ($this->pilototo, $row);
        
    }
    return(0);
  }

  // Delete a task from pilototo
  function pilototoDelete($taskid)
  {
    $now=time();
    // lookup for a task to do
    $query = "DELETE FROM auto_pilot
     WHERE idusers = $this->idusers
       AND taskid = $taskid";
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

//echo $query;
    return(0);
  }

  // Delete Old Tasks from auto_pilot
  /**
    * if (seconds) is 0 => delete all tasks for this user
    *    else delete tasks older than (seconds) seconds
    */

  function pilototoPurge($seconds)
  {
    $timestamp=time();
    if ( $seconds != 0 ) {
         $timestamp-=$seconds;
    }
    
    // lookup for a task to do
    $query = "DELETE FROM auto_pilot
     WHERE idusers = $this->idusers
       AND time   <= $timestamp";
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

//echo $query;
    return(0);
  }

  // Add a task to Pilototo
  function pilototoAdd($time, $pim, $pip)
  {
    // lookup for a task to do
    $query = "SELECT COUNT(*) from auto_pilot
              WHERE idusers=$this->idusers";
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    if ( $row[0] < PILOTOTO_MAX_EVENTS ) {
         $query = "INSERT INTO auto_pilot
             ( time, idusers, pilotmode, pilotparameter, status)
       VALUES ( $time, $this->idusers, '" . $pim . "', '" . $pip . "', '".PILOTOTO_PENDING."');";
         $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
         //echo $query;
         return(0);
    } else {
       return(1);
    }
  }

  // Update a task of the pilototo
  function pilototoUpdate($taskid, $time, $pim, $pip)
  {
    // lookup for a task to do
    $query = "UPDATE auto_pilot
     SET time=$time,
         pilotmode = $pim,
         pilotparameter = '" . $pip . "',
         status = '" .PILOTOTO_PENDING . "' 
         WHERE idusers = $this->idusers
     AND taskid = $taskid";
    $result = mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
//echo $query;

    logUserEvent($this->idusers , $_SESSION['IP'] , $this->engaged, "Update pilototo task $taskid : time=$time, pim=$pim,pip=$pip" );

    return(0);
  }



}

class fullUsers
{
  var $users, //a user object
      //var deduced from others
      $lastPositions,     //a postions object
      $hours,
      $wspeed, $wheading,
      $boatanglewithwind, $boatspeed,
      $anteLastPositions, //another positions obj
      $races,             //include a race object
      $loxoangletoend, $orthoangletoend,
      $distancefromend, $nwp,
      $VMG, $VMGortho,
      $LongNM, $LatNM,
      $loch;

  function fullUsers($id, $north = 80000, $south = -80000, $west = -180000, $east = 180000, $age = MAX_DURATION)
  {

    $now = time();

    $this->users = new users($id);

    // if boat not engage in a race, nothing else to do ....
    if ($this->users->engaged == 0) return;

    //find last position and time interval from now
    $lastPositionsObject = new positions;
    $lastPositionsObject->getLastPositions($this->users->idusers, $this->users->engaged);

    // inherit of nwp
    $this->nwp = $this->users->nwp;
    $this->loch = $this->users->loch;

    if (!isset($lastPositionsObject->long) )
    {
  //if object is empty
  //that shouldnot happen if base is written automaticaly
  //but it's dangerous
  //write a default position

  //echo "writing default positions ".$this->users->idusers."\n";
  $lastPositionsObject->writeDefaultPositions(
        $this->users->idusers, $this->users->engaged);
    }
    $this->lastPositions = $lastPositionsObject;


    $anteLastPositionsObject = new positions;
//  echo "Avant appel anteLast depuis users.class.php 2\n";
    $anteLastPositionsObject->getAnteLastPositions(
        $this->users->idusers, $this->users->engaged);
    $this->anteLastPositions = $anteLastPositionsObject;

    // this->hours (temps depuis la dernière VAC)
    if ( $this->users->userdeptime == -1 ) {
         $time = $now;
   $this->hours = 0;
    } else {
         $time = $this->lastPositions->time;
         $this->hours = ($now - $time )/3600 ;  //everything is in GMT
    }

    $this->races = new races($this->users->engaged);

    // windAtPosition returns a small array : (wspeed, wheading);
    // see functions.php
    $wind = windAtPosition($this->lastPositions->lat, $this->lastPositions->long, 0, 'OLD');
    $this->wspeed = $wind[0];
    $this->wheading = $wind[1];

    //find the angle boat/wind
    $this->boatanglewithwind = angleDifference($this->users->boatheading, 
                                               $this->wheading) ;
    //echo "\n**angleDifference ( " . $this->users->boatheading . " and " . $this->wheading .") is ".$this->boatanglewithwind . "**";

    //find boatspeed
    //echo "calling findboatspeed with ".$this->boatanglewithwind." ". $this->wspeed." ".  $this->users->boattype;
    $this->boatspeed =  findboatspeed(abs($this->boatanglewithwind),
          $this->wspeed,
          $this->users->boattype);

    // Find the best coordinates to cross the nextwaypoint (LatNM & LongNM)
    /* 
       Since 2007-October-10, 
       these LatNM & LongNM are a Waypoint given by the user (if not 0/0)
     */
    if ( $this->users->targetlat == 0 && $this->users->targetlong == 0 ) {
    //echo "*Race WP*";
         $rc = $this->bestWayToWaypoint($this->nwp);
    } else {
    //echo "*User WP*";
   $this->LatNM = $this->users->targetlat*1000;
   $this->LongNM = $this->users->targetlong*1000;
    }

    $this->distancefromend = ortho($this->lastPositions->long, $this->lastPositions->lat, $this->LongNM, $this->LatNM);

    $this->loxoangletoend = $this->loxodromicHeading();
    $this->orthoangletoend = $this->orthodromicHeading();

    $this->VMG =
      VMG( $this->lastPositions->long, $this->lastPositions->lat,
     $this->LongNM, $this->LatNM,
     $this->users->boatheading, $this->boatspeed, 0);

    $this->VMGortho =
      VMGortho($this->lastPositions->long, $this->lastPositions->lat,
     $this->users->boatheading, $this->boatspeed, $this->orthoangletoend);

    //    print_r($this);

  }


  //====================================================================================
  // This function gives the lat ant long where it seems the best to cross next waypoint
  //====================================================================================
  function bestWayToWaypoint($wp)
  {
    // Get coords of the nextwaypoint
    // Retourne long/lat + long/lat
    $nextwaypoint = giveWaypointCoordinates($this->users->engaged, $wp, WPLL/WP_NUMSEGMENTS);

    // Get the best crossing point
    $coords=array();

    // Attend des couples lat/long et retourne lat/long
    //printf ("Lat1=%f, Long1=%f\n", $nextwaypoint[1]/1000, $nextwaypoint[0]/1000);
    //printf ("Lat2=%f, Long2=%f\n", $nextwaypoint[3]/1000, $nextwaypoint[2]/1000);
    //printf ("BoatLat=%f, BoatLong=%f\n", $this->lastPositions->lat/1000, $this->lastPositions->long/1000);
    $coords=coordonneescroisement($nextwaypoint[1]/1000, $nextwaypoint[0]/1000,
              $nextwaypoint[3]/1000, $nextwaypoint[2]/1000,
                                  $this->lastPositions->lat/1000, $this->lastPositions->long/1000);

    $this->LatNM=$coords[0]*1000;
    $this->LongNM=$coords[1]*1000;

//    printf ("Lat=%f, Long=%f\n", $this->LatNM, $this->LongNM);
    return (0);
  }



  // this function will update the userdeptime in the users table
  function updateDepTime($time)
  {
    //TODO write a positions->deletepositions that will be called here for every positions
    //delete old positions from database
    //printf ("Time = %d\n",$time);
    $query_deptime = "UPDATE users set userdeptime = " . $time . " WHERE idusers = ". $this->users->idusers  ;
    //echo ( "Query failed : " . mysql_error." ".$query_deptime );
    mysql_db_query(DBNAME,$query_deptime) or die ( "Query failed : " . mysql_error." ".$query_deptime );
  }

  //this function will delete all the positions of the boat for this race
  function deletePositions($idraces)
  {
    //TODO write a positions->deletepositions that will be called here for every positions
    //delete old positions from database
    $query65 = "DELETE FROM positions WHERE idusers = ". $this->users->idusers  . 
         " AND  race = " . $idraces;
    mysql_db_query(DBNAME,$query65);// or die("Query failed : " . mysql_error." ".$query65);
  }

  function updateAngles()
  {
    if ( $this->users->pilotmode == PILOTMODE_BESTSPEED ) //  BEST SPEED
    {
  $Hdg=0; $bestHdg=0; 
  $Spd=-1 ;$bestSpd=-1;
  while ( $Hdg <= 359 ) {
          $Spd = findboatspeed( angleDifference($Hdg, $this->wheading),
                                    $this->wspeed,
                                    $this->users->boattype);
    if ( $Spd >= $bestSpd ) {
      $bestHdg=$Hdg;
      $bestSpd=$Spd;
    }
    $Hdg+=1;
    //echo "DEBUG Spd=$Spd, H=$Hdg \n";
  }

        // On se refait un petit calcul avec un pas de 0.1 autour du cap "au degré près".
  for ( $Hdg=$bestHdg-1;$Hdg<$bestHdg+1;$Hdg+=0.1 ) {
          $Spd = findboatspeed( angleDifference($Hdg, $this->wheading),
                                    $this->wspeed,
                                    $this->users->boattype);
    if ( $Spd > $bestSpd ) {
      $bestHdg=$Hdg;
      $bestSpd=$Spd;
    }
    //echo "DEBUG Spd=$Spd, H=$Hdg \n";
  }

  $this->users->boatheading = $bestHdg;
  $query1 = "UPDATE users SET boatheading =". $this->users->boatheading 
          ." WHERE idusers =".$this->users->idusers;
  $result1 = mysql_db_query(DBNAME,$query1);
    }

    if ( $this->users->pilotmode == PILOTMODE_BESTVMG ) //  BEST VMG
    {
       $cap_ortho = caportho($this->lastPositions->long, $this->lastPositions->lat, $this->LongNM, $this->LatNM);

       $cap_vent = ($this->wheading + 180)%360;
       $wind_speed = $this->wspeed;
       $boat_type = $this->users->boattype;
       $vmg_max_t = 0;
       $vmg_max_b = 0;
       $DegRad = pi()/180;
       //echo "Debug cap_ortho= ".$cap_ortho;
       //echo "Debug cap_vent= ".$cap_vent;
       //echo "Debug wind_speed= ".$wind_speed;
       //echo "Debug boat_type= ".$boat_type;

       for ($i=20; $i<=170;$i++) //Pour limiter le nb de calcul on peut mettre ($i=30; $i<=170;$i++)
       {
           $boatspeed = findboatspeed($i, $wind_speed, $boat_type);
           //echo "Debug boatspeed= ".$boatspeed;

           $vmg_t = $boatspeed * cos(($cap_ortho - ($cap_vent - $i)) * $DegRad);
           $vmg_b = $boatspeed * cos(($cap_ortho - ($cap_vent + $i)) * $DegRad);
           //echo "Debug vmg_t= ".$vmg_t;
           //echo "Debug vmg_b= ".$vmg_b;

           if ($vmg_t > $vmg_max_t)
           {
               $vmg_max_t = $vmg_t;
               $angle_vmg_max_t = $i;
               //echo "Debug vmg_max_t= ".$vmg_max_t;
               //echo "Debug angle_vmg_max_t= ".$angle_vmg_max_t;
           }
           if ($vmg_b > $vmg_max_b)
           {
               $vmg_max_b = $vmg_b;
               $angle_vmg_max_b = $i;
               //echo "Debug vmg_max_b= ".$vmg_max_b;
               //echo "Debug angle_vmg_max_b= ".$angle_vmg_max_b;
           }
       }
       //echo "Debug vmg_max_t= ".$vmg_max_t;
       //echo "Debug angle_vmg_max_t= ".$angle_vmg_max_t;
       //echo "Debug vmg_max_b= ".$vmg_max_b;
       //echo "Debug angle_vmg_max_b= ".$angle_vmg_max_b;

       if ($vmg_max_t > $vmg_max_b)
       {
           $bestHdg = $cap_vent - $angle_vmg_max_t;
           $bestVMG = $vmg_max_t;
       }
       else
       {
           $bestHdg = $cap_vent + $angle_vmg_max_b;
           $bestVMG = $vmg_max_b;
       }
       //echo "Debug bestHdg= ".$bestHdg;
       //echo "Debug bestVMG= ".$bestVMG;


       $this->users->boatheading = ($bestHdg+360)%360;
       $this->VMG = $bestVMG;
       $query1 = "UPDATE users SET boatheading =". $this->users->boatheading
                ." WHERE idusers =".$this->users->idusers;
       $result1 = mysql_db_query(DBNAME,$query1) ; 
    }

    if ($this->users->pilotmode == PILOTMODE_WINDANGLE) //constant wind angle
    {
  //update boatheading
  $this->users->boatheading = (($this->wheading+180) + $this->users->pilotparameter) ;

  while ( $this->users->boatheading > 360 ) $this->users->boatheading-=360;
  while ( $this->users->boatheading < 0 ) $this->users->boatheading+=360;

  $query1 = "UPDATE users SET boatheading =". round($this->users->boatheading ,1)
    ." WHERE idusers =".$this->users->idusers;
  $result1 = mysql_db_query(DBNAME,$query1);
  //echo $query1;
    }

    if ($this->users->pilotmode == PILOTMODE_ORTHODROMIC) //orthodromic course, no pilot parameter required
    {
  //update boatheading
  $this->users->boatheading = $this->orthodromicHeading();
  $query1 = "UPDATE users SET boatheading =". $this->users->boatheading
    ." WHERE idusers = ".$this->users->idusers;
  $result1 = mysql_db_query(DBNAME,$query1);
  //echo $query1;
    }
    //find the angle boat/wind
    $this->boatanglewithwind = angleDifference($this->users->boatheading,
            $this->wheading) ;

    //find boatspeed
    //echo "calling findboatspeed with ".$this->boatanglewithwind." ". $this->wspeed." ".  $this->users->boattype;
    $this->boatspeed =  findboatspeed($this->boatanglewithwind,
          $this->wspeed,
          $this->users->boattype);

  }

  //update the target lat / long
  // 3eme argument "abandonWP" pour différencier abandon de WP(true) de la saisie de WP(false)
  function updateTarget($lat, $long, $hdg, $abandonWP=false)
  {
    /**
      *  S'agit t'il d'un abandon de WP ? ( on ré-initialise targetandhdg )
      *  Si un cap est saisi (valeure positive inférieure à 360), on prend ce cap
      *  Sinon, il faut se mettre en route vers le prochain WP (redéfinir LatNM et LongNM)
      */
    if ( $abandonWP ) {

        $query = "UPDATE users SET targetandhdg      = -1 ,    " ;

        /*
         * If targetandhdg is between 0 and 360
         */
        // New heading will become this heading, and pilotmode is set to PILOTMODE_HEADING
        if ( $this->users->targetandhdg>= 0 and $this->users->targetandhdg<=360) {

             $this->users->pilotmode      = PILOTMODE_HEADING;
             $this->users->pilotparameter = $this->users->targetandhdg;
             $this->users->boatheading    = $this->users->targetandhdg;

             $query .=  " pilotmode       = " . $this->users->pilotmode      . " ," ;
             $query .=  " pilotparameter  = " . $this->users->pilotparameter . " ," ;
             $query .=  " boatheading     = " . $this->users->boatheading    . " ," ;
        }  else  {
       // On ne touche pas au pilotmode (c'est peut-être Ortho ou BestVMG)
             // Mais il faut remettre à jour LatNM et LongNM
              $rc = $this->bestWayToWaypoint($this->nwp);
        }
        $this->users->targetandhdg = -1;

        $this->users->targetlat      = 0;
        $this->users->targetlong     = 0;

        $query .=  " targetlat       = " . $this->users->targetlat      . " ," ;
        $query .=  " targetlong      = " . $this->users->targetlong     . " ," ;

        $query .= "   lastchange      = " . time()  .
        " WHERE idusers = " . $this->users->idusers;

        //echo "ABANDONWP QUERY=" . $query;
        mysql_db_query(DBNAME,$query) ; //or printf("\nQuery failed : " . mysql_error." ".$query);

    } else {
        // Il ne s'agit pas d'un abandon de WP, donc c'est une modification du paramétrage
        $query = "UPDATE users ";
        $query .= "set lastchange = "  . time() ;
    
        /**
          * Update targetlat
          */
        if ( is_numeric($lat) && abs($lat)<90 ) {
            $this->users->targetlat = $lat;
            $query .= " , `targetlat`  = " . $this->users->targetlat   ;
        } else { 
            $this->users->targetlat = 0;
        }
    
        /**
          * Update targetlong
          */
        if ( is_numeric($long) && abs($long)<180 ) {
      if ( $long >180 ) $long-=360;
      if ( $long <-180 ) $long+=360;
            $this->users->targetlong = $long;
            $query .= " , `targetlong` = " . $this->users->targetlong ; 
        } else { 
            $this->users->targetlong = 0;
        }
    
        /**
          * Update targetandhdg
          */
        if ( is_numeric($hdg) && abs($hdg)<=360 ) {
            $this->users->targetandhdg = $hdg;
            $query .= " , `targetandhdg` = " . $this->users->targetandhdg  ; 
        } else { 
            $this->users->targetandhdg=-1;
        }

        $query .= " WHERE idusers = " . $this->users->idusers;
        // echo "MODIFWP QUERY=" . $query;
        mysql_db_query(DBNAME,$query) ;//or printf("\nQuery failed : " . mysql_error." ".$query);

        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Target (lat=" . $this->users->targetlat. ", lon=" . $this->users->targetlong. ", @wph=" . $this->users->targetandhdg. ")" );


    }
  }


  //remove player from races
  function removeFromRaces()
  {
     $queryhistopositions = "INSERT INTO histpos SELECT * FROM positions 
                             WHERE idusers = " . $this->users->idusers . " and race = " . $this->users->engaged . ";";
     mysql_db_query(DBNAME,$queryhistopositions);
     //echo "QH = $queryhistopositions" . "\n";

     $querypurgepositions = "DELETE FROM positions 
                             WHERE idusers = " . $this->users->idusers . " and race = " . $this->users->engaged . ";";
     mysql_db_query(DBNAME,$querypurgepositions);
     //echo "QP = $querypurgepositions" . "\n";

     // And then, the most important...
     $this->subscribeToRaces(0);
  }

  // Function giveNextWaipoint
  function giveNextWaypoint()
  {
    // Retourne -1 si il n'y a plus de waypoints (on a passé le dernier, donc la finish line)
//     select wporder from races_waypoints where idraces=35 and wporder >1 ORDER BY wporder ASC LIMIT 1; 
    $query = "SELECT wporder FROM races_waypoints " .
        " WHERE idraces = " . $this->users->engaged . 
    " AND wporder > " . $this->users->nwp . 
    " ORDER BY wporder ASC LIMIT 1";

    $result = mysql_db_query(DBNAME,$query); // or die("Query failed : " . mysql_error." ".$query);
    //printf ("Request Races_Waypoints : %s\n" , $query);

    if ( mysql_num_rows($result) == 0 ) {
  printf (", No more Waypoint\n");
  return (-1);
    } else {
      $row = mysql_fetch_array($result, MYSQL_NUM);
  printf (", Next Waypoint : %d. ", $row[0] );
  return ($row[0]);
    }

  }

  // Function updateWaypoints
  function recordWaypointCrossing()
  {
     // Choix de "userdeptime"
     
     /*
     **  2008/09/13 : dans Waypoint_crossing, on stocke désormais le temps de course
     if ( $this->nwp == 1 ) {
          $udt = $this->users->userdeptime;
     } else {
          $udt = getWaypointCrossingTime($this->users->engaged,$this->nwp - 1, $this->users->idusers);
     }
     */

     $udt = $this->users->userdeptime;
     
     $query = "REPLACE INTO waypoint_crossing " .
                        "        (idraces , idwaypoint, idusers , time, userdeptime)  " .
                        " VALUES ( " . $this->users->engaged . ", " .
                                       $this->nwp . ", " .
                                       $this->users->idusers . ", " .
                                       time() . ", " .
               $udt . ");"   ;

     mysql_db_query(DBNAME,$query) ;//or die("Query failed : " . mysql_error." ".$query);

  }
  // Function updateWaypoints
  function updateNextWaypoint()
  {
    // MAJ la table users pour prise en compte du prochain Waypoint
    $query = "UPDATE users SET nextwaypoint = " . $this->nwp . 
       " WHERE idusers = " . $this->users->idusers;
    mysql_db_query(DBNAME,$query); // or die("Query failed : " . mysql_error." ".$query);
    //printf ("Request USERS : %s\n" , $query);

    // MAJ la table races_ranking pour prise en compte du prochain Waypoint
    //$query = "UPDATE races_ranking SET nwp = " . $this->nwp . ", " .
    //         "                         dnm       = " . $this->distancefromend     . ", " .
    //       " WHERE idusers = " . $this->users->idusers .
    //       "   AND idraces = " . $this->users->engaged;
    //   mysql_db_query(DBNAME,$query);// or die("Query failed : " . mysql_error." ".$query);
    //  printf ("Request RACES_RANKING : %s\n" , $query);
  }

  function writeCurrentRanking ( $freq = 0 )
  {
    $now = time();

    // Record classification data
    //"        (idraces , idusers , nwp , dnm, latitude, longitude, last1h, last3h, last24h)  " .
    // ICI, on appelle vraiment la fonction bWTW, pour indiquer la bonne distance dans les classements
    // MAIS : bug du 10/10/2007 : elle modifie LatNM et LongNM... !
    //==> En attendant de faire plus élégant, on sauve/restore ces deux valeurs (pas beau, non... pas du tout)
    // =======================================================================================
    // On met à jour les colonnes lastupdate + lastchange et la colonne loch de la table users
    // =======================================================================================
    $query_update = "UPDATE users set ";

    // On maj Lastchange uniquement pour les bateaux qui ne sont pas bout au vent.
    if ( $this->users->pilotmode != 2 
         || ( $this->users->pilotmode ==2 && $this->users->pilotparameter != 0 )  ) {
         
            $query_update .= " lastchange = " . time() . "," ;
    }

    // Cumul du loch sauf si bout au vent... (si vitesse <0.1, loch n'est pas incrémenté)
    if ( $this->boatspeed > 0.1 )  {
      $query_update .= " loch = loch + " . round($this->boatspeed*$this->hours,3) . "," ;
    }

    // On décrémente HidePos si positif
    if ( $this->users->hidepos > 0 ) {
         $this->users->hidepos = $this->users->hidepos - 1 ;
      $query_update .= " hidepos = " .  $this->users->hidepos . "," ;
    }

    $query_update .= " lastupdate = " . time() ; 
    $query_update .= " WHERE idusers  = " . $this->users->idusers ;
    mysql_db_query(DBNAME,$query_update);// or die("Query failed : " . mysql_error." ".$query_ranking);

    // =======================================================================================
    // En cas de blackout, on a fini.
    // =======================================================================================
    if ( $this->races->bobegin < $now && $now < $this->races->boend ) {
       if ( $this->users->idusers > 0 ) {
            printf ("*** Blackout ACTIVE ***\n");
       }
       return(0);
    } 

    
    // =======================================================================================
    // En cas de StealthPlay, on a fini aussi.
    // =======================================================================================
    if ( $this->users->hidepos > 0 ) {
       printf ("*** StealthPlay ACTIVE (%d) ***\n", $this->users->hidepos);
       return(0);
    } 


    // =======================================================================================
    // Si on est encore là : Calculs et mise à jour des classements (table races_ranking)
    // =======================================================================================
    $sauvlong=$this->LongNM;
    $sauvlat=$this->LatNM;
    //==> On restore quand on a plus besoin des "vraies valeurs"...

    $rc = $this->bestWayToWaypoint($this->nwp);
    $this->distancefromend = ortho($this->lastPositions->long, $this->lastPositions->lat, $this->LongNM, $this->LatNM);


    // 1 : corrected, 0 : not corrected
    $dist = $this->distRecords(24*3600);
    if ( $this->loch > $dist[1] ) {
      $last24h = $dist[1];
    } else {
      $last24h = $dist[0];
    }
    if ( $last24h > 24 * MAX_SPEED_FOR_RANKING ) $last24h = 0;

    $dist = $this->distRecords(3*3600);
    if ( $this->loch > $dist[1] ) {
        $last3h = $dist[1];
    } else {
        $last3h = $dist[0];
    }
    if ( $last3h > 3 * MAX_SPEED_FOR_RANKING ) $last3h = 0;

    $dist = $this->distRecords(3600);
    if ( $this->loch > $dist[1] ) {
        $last1h = $dist[1];
    } else {
        $last1h = $dist[0];
    }
    if ( $last1h > MAX_SPEED_FOR_RANKING ) $last1h = 0;

    $query_ranking = "UPDATE races_ranking " .
                     " SET      nwp       = " . $this->nwp                 . ", " .
                               "dnm       = " . $this->distancefromend     . ", " .
                               "nmlat     = " . $this->LatNM               . ", " .
                               "nmlong    = " . $this->LongNM              . ", " .
                               "latitude  = " . $this->lastPositions->lat  . ", " . 
                               "longitude = " . $this->lastPositions->long . ", " .
                               "loch      = " . $this->loch                . ", " .
                               "last1h    = " . $last1h                    . ", " .
                               "last3h    = " . $last3h                    . ", " .
                               "last24h   = " . $last24h                   . 
                     " WHERE idraces  = " . $this->users->engaged .
                     "  AND  idusers  = " . $this->users->idusers ;

    mysql_db_query(DBNAME,$query_ranking);// or die("Query failed : " . mysql_error." ".$query_ranking);
    //printf ("Query : %s\n", $query_ranking);


    //==> On restore là, maintenant...
    $this->LongNM=$sauvlong;
    $this->LatNM=$sauvlat;

    if ( $this->users->idusers > 0 ) {
         printf ("\n\t\t*** NWP=%d, DNM=%f ", $this->nwp, $this->distancefromend);
         printf ("\n\t\t*** ranking updated.\n");
    }

  }

  function deleteCurrentRanking()
  {
    // Classification data is to be deleted when a player gets out of a race
    $query_ranking = "DELETE from races_ranking " .
                     " WHERE idraces  = " . $this->users->engaged .
                     "  AND  idusers  = " . $this->users->idusers ;

    mysql_db_query(DBNAME,$query_ranking);

  }

  function setABD()
  {
    // Record classification only if this is not a "TYPE_RECORD" race and if no oldDuration is known
    $oldDuration=getOldDuration($this->races->idraces, $this->users->idusers);
    if ( $oldDuration == 0 ) {
       // replace into races_results (idraces , idusers , position , duration, longitude, latitude)
       //                       values ($1, $2, 0, $la_date, $longitude, $latitude);
       $result_timestamp = MAX_DURATION - time();
       $query_abandon = "REPLACE INTO races_results " .
                        "        (idraces , idusers , position , duration, longitude, latitude)  " .
                        " VALUES ( " . $this->users->engaged . ", " .
                                    $this->users->idusers . ", " .
                                    BOAT_STATUS_ABD . ", " .
                                    $result_timestamp . ", " .
                                    $this->lastPositions->long . ", " .
                                    $this->lastPositions->lat . ");"   ;

       mysql_db_query(DBNAME,$query_abandon);
    }

    // Then subscribe to race 0
    $this->subscribeToRaces(0);

    logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Abandon." );

  }

  function setDNF()
  {
    // Record classification
    // replace into races_results (idraces , idusers , position , duration, longitude, latitude)
    //                       values ($1, $2, 0, $la_date, $longitude, $latitude);
    $oldDuration=getOldDuration($this->races->idraces, $this->users->idusers);
    if ( $oldDuration == 0 ) {
        $result_timestamp = MAX_DURATION - time();
        $query_abandon = "REPLACE INTO races_results " .
                         "        (idraces , idusers , position , duration, longitude, latitude)  " .
                         " VALUES ( " . $this->users->engaged . ", " .
                                    $this->users->idusers . ", " .
                                    BOAT_STATUS_DNF . ", " .
                                    $result_timestamp . ", " .
                                    $this->lastPositions->long . ", " .
                                    $this->lastPositions->lat . ");"   ;

        mysql_db_query(DBNAME,$query_abandon);
    }

    // Then subscribe to race 0
    $this->subscribeToRaces(0);
  }

  function setHTP()
  {
    // Record classification
    // replace into races_results (idraces , idusers , position , duration, longitude, latitude)
    //                       values ($1, $2, 0, $la_date, $longitude, $latitude);
    $oldDuration=getOldDuration($this->races->idraces, $this->users->idusers);
    if ( $oldDuration == 0 ) {
        $result_timestamp = MAX_DURATION - time();
        $query_abandon = "REPLACE INTO races_results " .
                         "        (idraces , idusers , position , duration, longitude, latitude)  " .
                         " VALUES ( " . $this->users->engaged . ", " .
                                    $this->users->idusers . ", " .
                                    BOAT_STATUS_HTP . ", " .
                                    $result_timestamp . ", " .
                                    $this->lastPositions->long . ", " .
                                    $this->lastPositions->lat . ");"   ;

        mysql_db_query(DBNAME,$query_abandon);
    }

    // Then subscribe to race 0
    $this->subscribeToRaces(0);
  }

  function setSTOPPED()
  {
    // Record classification
    // replace into races_results (idraces , idusers , position , duration, longitude, latitude)
    //                       values ($1, $2, 0, $la_date, $longitude, $latitude);
    $timestamp=time();
    $query = "UPDATE users SET `pilotmode` = 2, " .
           " `pilotparameter` = 0 , " . 
       " `lastchange` = " . $timestamp . 
       " WHERE idusers = ".$this->users->idusers;
    mysql_db_query(DBNAME,$query); // or die("Query failed : " . mysql_error." ".$query);
    //printf ("Request USERS : %s\n" , $query);

    // Determiner quel est le timestamp de la dernière position écrite pour ce joueur
    $query = "SELECT max(time) FROM positions " .
             " WHERE race = " . $this->users->engaged . 
       " AND   idusers = ". $this->users->idusers  ;
    $result = mysql_db_query(DBNAME,$query);// or echo("Query failed : " . mysql_error." ".$query);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    $timestamp=$row[0];
    
    // Effacement de cette position là
    $query = "DELETE FROM positions " . 
             " WHERE `time` = " .  $timestamp  . 
             " AND race = " . $this->users->engaged . 
       " AND idusers = ". $this->users->idusers ;
    //printf ("Request POSITIONS : %s\n" , $query);
    mysql_db_query(DBNAME,$query);// or echo("Query failed : " . mysql_error." ".$query);

    $this->updateAngles();
  }


  function subscribeToRaces($id)
  {
    $query11 = "UPDATE users SET engaged =" . $id . ", " . 
               " pilotmode=2, " . 
         " pilotparameter=0,  " . 
         " nextwaypoint=1, " . 
         " userdeptime=-1, " . 
         " loch=0 " . 
         " WHERE idusers = ".$this->users->idusers;
    $result11 = mysql_db_query(DBNAME,$query11);

    if ( $id != 0 ) {
      $this->races = new races($id);
      $this->users->boattype = $this->races->boattype;

        //delete old positions from database for this race
        $this->deletePositions($id);

        // Purge all Pilototo tasks
        $this->users->pilototoPurge(0);

  // insert an initial position
      $query7 = "INSERT INTO positions SET     `time` = ". time().
                                       ",    `long` =". $this->races->startlong .
                                       ",     `lat` =". $this->races->startlat.
                                       ", `idusers` = ".$this->users->idusers.
                                       ", `race`    = ".$id;
      mysql_db_query(DBNAME,$query7);

      // Delete old positions from races_results (in case of sub/unsub/sub again) (only if not TYPE_RECORD)
        if ( $this->races->racetype != RACE_TYPE_RECORD ) {
         $query_clean_races_results = "DELETE FROM races_results WHERE `idraces` = ". $id .
                                        " AND `idusers` = " . $this->users->idusers;
         mysql_db_query(DBNAME,$query_clean_races_results);
  }
 
      // Delete all old entries from races_ranking
      $query_clean_races_ranking = "DELETE FROM races_ranking where idusers= " .  $this->users->idusers ;
      mysql_db_query(DBNAME,$query_clean_races_ranking);

      // Prepare the table races_ranking
      $query_clean_races_ranking = "INSERT INTO races_ranking ( idraces, idusers ) values " . 
             " ( ". $id . ", " . $this->users->idusers . ")";
      mysql_db_query(DBNAME,$query_clean_races_ranking);

      // Update boattype
        $query_boattype=" UPDATE users set boattype = '" . $this->races->boattype . "'" . 
                        "             ,    targetlat = 0, targetlong = 0, targetandhdg = -1        " .   
                  " WHERE idusers = " . $this->users->idusers;
        $result = mysql_db_query(DBNAME,$query_boattype) or die("Query [$query_boattype] failed \n");

        logUserEvent($this->users->idusers , $_SESSION['IP'] , $id, "Engaged." );

    } else {
        $this->deleteCurrentRanking();
    }

    
  }


  function writeNewheading($mode, $boath, $param)
  {

    //pilotmode = 1 if autopilot
    //pilotmode = 2 if wind angle
    //pilotmode = 3 if orthodromic
    //pilotmode = 4 if bestvmg
    //pilotmode = 5 if bestspeed

    // We timestamp each change, 
    // ==> to detect sleeping users who are in STOPPED mode due to coast crossing
    // Engine uses this field to set them DNF if the are sleeping for a long time
    $timestamp=time();

    if ($mode == "autopilot")
      {
  //find angle and wind angle
  $this->users->boatheading = $boath;
  $this->users->pilotmode = 1;
  $query = "UPDATE users SET `pilotmode` = 1, " . 
     " boatheading = ". $this->users->boatheading . ", " .
     " pilotparameter = ". $this->users->boatheading . ", " .
     " lastchange = ". $timestamp . ", " .
     " ipaddr = '". $_SESSION['IP'] . "'" .
           " WHERE idusers = ".$this->users->idusers;
  $result = mysql_db_query(DBNAME,$query) or die("Query [$query] failed \n");
        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Angles : pim=" . $this->users->pilotmode . ", pip=" . $this->users->boatheading );
      }

    else if ($mode == "windangle")
      {
  $this->users->pilotparameter = $param;
  $this->users->pilotmode = 2;
  $query = "UPDATE users SET `pilotmode` = 2, " . 
      " `pilotparameter` = " . round($this->users->pilotparameter,1) . ", " .
      " lastchange = ". $timestamp . ", " .
      " ipaddr = '". $_SESSION['IP'] . "'" .
      " WHERE idusers = ".$this->users->idusers;
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Angles : pim=" . $this->users->pilotmode . ", pip=" . $this->users->pilotparameter );

      }

    else if ($mode == "orthodromic")
      {
  $this->users->pilotmode = 3;
  $query = "UPDATE users SET `pilotmode` = 3, " . 
      " lastchange = ". $timestamp . ", " .
      " ipaddr = '". $_SESSION['IP'] . "'" .
      " WHERE idusers = ".$this->users->idusers;
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Angles : pim=" . $this->users->pilotmode  );

      }
    else if ($mode == "bestvmg")
      {
  $this->users->pilotmode = 4;
  $query = "UPDATE users SET `pilotmode` = 4, " . 
      " lastchange = ". $timestamp . ", " .
      " ipaddr = '". $_SESSION['IP'] . "'" .
      " WHERE idusers = ".$this->users->idusers;
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Angles : pim=" . $this->users->pilotmode  );

      }
    else if ($mode == "bestspeed")
      {
  $this->users->pilotmode = 5;
  $query = "UPDATE users SET `pilotmode` = 5, " . 
      " lastchange = ". $timestamp . ", " .
      " ipaddr = '". $_SESSION['IP'] . "'" .
      " WHERE idusers = ".$this->users->idusers;
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);

        logUserEvent($this->users->idusers , $_SESSION['IP'] , $this->users->engaged, "Update Angles : pim=" . $this->users->pilotmode  );
      }
    $this->updateAngles();

  }


//=================================================================//
//                     loxodromic Heading                          //
//                       By JP Mars 2007                           //
//-----------------------------------------------------------------//
//            from a position and a destination,                   //
//       return the angle to follow an loxodromic course.          //
//-----------------------------------------------------------------//
//        Algo written by John-Pet (JP@virtual-winds.com)          //
//=================================================================//

  function loxodromicHeading()
  {

     // Find the best coordinates to cross the nextwaypoint
     $long_wp = deg2rad($this->LongNM/1000);
     $lat_wp  = deg2rad($this->LatNM/1000);

     $long_bat = deg2rad($this->lastPositions->long/1000);
     $lat_bat  = deg2rad($this->lastPositions->lat/1000);

     //printf ("   En radian : lat_bat=%f, long_bat=%f<BR>", $lat_bat, $long_bat);
     //printf ("   En radian : lat_wp=%f, long_wp=%f<BR>", $lat_wp, $long_wp);

     // Correction de la longitude de départ pour la gestion de l'antiméridien
     if ( $long_bat < 0 and $long_wp > 0 and ($long_bat - $long_wp) < M_PI and ($long_bat - $long_wp) < -M_PI ) {
        $cor_long_bat = 2 * M_PI + $long_bat;
     } else {
        $cor_long_bat = $long_bat;
     }

     // Correction de la longitude d'arrivée pour la gestion de l'antiméridien
     if ( $long_bat > 0 and $long_wp < 0 and ($long_bat - $long_wp) > M_PI and ($long_bat - $long_wp) > - M_PI ) {
        $cor_long_wp = 2 * M_PI + $long_wp;
     } else {
        $cor_long_wp = $long_wp;
     }

     // Nouvelles longitudes selon correction ou pas
     $long_bat = $cor_long_bat;
     $long_wp  = $cor_long_wp;

     // calcul de l'angle avec gestion des caps 90 et 270°
     $dla = rad2deg(60 * ($lat_bat - $lat_wp));
     $dlom = rad2deg(60 * cos(($lat_bat + $lat_wp) / 2) * ($long_bat - $long_wp));
      
     if ($dlom == 0 ) {
        $angle = 0;
     } else {
        $angle = abs(rad2deg(atan($dla / $dlom)));
     }

     // Résultat pour le cap loxo à suivre
     if ( $lat_bat < $lat_wp and $long_bat > $long_wp ) {
        $caploxo = 270 + $angle;
     } elseif ( $lat_bat < $lat_wp and $long_bat < $long_wp ) {
        $caploxo = 90 - $angle;
     } elseif ( ( $lat_bat > $lat_wp and $long_bat > $long_wp ) or ($lat_bat == $lat_wp and $long_bat > $long_wp ) ) {
        $caploxo = 270 - $angle;
     } elseif ( ($lat_bat > $lat_wp and $long_bat < $long_wp ) or ($lat_bat == $lat_wp and $long_bat < $long_wp ) ) {
        $caploxo = 90 + $angle;
     } elseif ( ($lat_bat < $lat_wp and $long_bat == $long_wp ) or ($lat_bat == $lat_wp and $long_bat == $long_wp ) ) {
        $caploxo = 0;
     } elseif ( $lat_bat > $lat_wp and $long_bat == $long_wp ) {
        $caploxo = 180;
     } 

     // Résultat pour la distance loxo
     if ( $dlom == 0 ) {
        $distloxo = abs($dla);
     } else {
        $distloxo = abs($dlom / cos(deg2rad($angle * M_PI)));
     }

     return $caploxo;
  }

//=================================================================//
//                    Orthodromic Heading                          //
//                   By John-Pet Avril 2007                        //
//-----------------------------------------------------------------//
//            from a position and a destination,                   //
//       return the angle to follow an orthodromic course.         //
//=================================================================//
//        Algo written by John-Pet (JP@virtual-winds.com)          //
//=================================================================//

  function orthodromicHeading()
  {
     // Find the best coordinates to cross the nextwaypoint
     $long_wp = deg2rad($this->LongNM/1000);
     $lat_wp = deg2rad($this->LatNM/1000);

     $long_bat = deg2rad($this->lastPositions->long/1000);
     $lat_bat = deg2rad($this->lastPositions->lat/1000);

     if ($lat_bat == $lat_wp and $long_bat == $long_wp) {
   $caportho = 2; 
     } else {
        $X = M_PI_2 - $lat_wp;
        $Y = M_PI_2 - $lat_bat;
        $Z = acos(sin($lat_bat) * sin($lat_wp) + cos($lat_bat) * cos($lat_wp) * cos(($long_bat-$long_wp)));
        $W = (cos($X) -  (cos($Y) * cos($Z)))/ (sin($Y) * sin($Z));

        if ($W > 1 or $W < -1) {
          $cap = 0;
        } else {
     $cap = rad2deg(acos($W));
        }
     }

     if (($lat_bat < $lat_wp and $long_bat == $long_wp) or ($lat_bat == $lat_wp and $long_bat == $long_wp) or ($lat_bat == 0 and $long_bat == 0 and $lat_wp == 0 and $long_wp == 0 )) $caportho = 0;
   elseif (($lat_bat > $lat_wp and $long_bat == $long_wp)) $caportho = 180 ;
   elseif (sin($long_wp - $long_bat) > 0) $caportho = $cap;
   elseif ($caportho = 360 - $cap );

   return $caportho;
  }


    // ============================================================================================
    // This function is used to verify if two vectors are crossing each-other
    // It returns true if yes, false if no
    // If it returns true, $encountercoordinates[] gives the longitude ang latitude of the crossing
    // ============================================================================================

    function dotheycross($x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4, &$encounterCoordinates, $verbose)
    {
        $rotation=0;

        //printf ("\nVecteur 1:x=%f,y=%f -> x=%f,y=%f\n", $x1,$y1,$x2,$y2);
        //printf ("Vecteur 2:x=%f,y=%f -> x=%f,y=%f\n", $x3,$y3,$x4,$y4);
        // Test sur la longueur des vecteurs (pour éviter boucle à vide)
        if ( $x1 == $x2 && $y1 == $y2 ) return (false);
        if ( $x3 == $x4 && $y3 == $y4 ) return (false);
        // On peut y aller, les coordonnées ne désignent pas des points.

        if ( $verbose > 0) echo "== Enterring dotheycross ==";
        //on test division !=0
        while(($x1==$x2)  || ($x3==$x4)) {
            //on fait une rotation de tous les pts
            //x' = xcos(q) - ysin(q) + a 
            //y' = xsin(q) + ycos(q) + b
            $rotation+=.01;
          
            $tmp=$x1;
            $x1=$x1*cos($rotation) - $y1*sin($rotation);
            $y1=$tmp*sin($rotation) + $y1*cos($rotation);

            $tmp=$x2;
            $x2=$x2*cos($rotation) - $y2*sin($rotation);
            $y2=$tmp*sin($rotation) + $y2*cos($rotation);

            $tmp=$x3;
            $x3=$x3*cos($rotation) - $y3*sin($rotation);
            $y3=$tmp*sin($rotation) + $y3*cos($rotation);

            $tmp=$x4;
            $x4=$x4*cos($rotation) - $y4*sin($rotation);
            $y4=$tmp*sin($rotation) + $y4*cos($rotation);
        } 
        if ( $verbose > 0) echo "== Après while ==";

        //calcul l'equation de la premiere droite de point 1 et 2
        //y=ax+b
        $a1 = ($y1 - $y2) / ($x1 - $x2);
        $b1 = $y2 - $a1 * $x2;

        //calcul l'equation de la seconde droite de point 3 et 4
        //y=ax+b
        $a2 = ($y3 - $y4) / ($x3 - $x4);
        $b2 = $y4 - $a2 * $x4;

        //on verifie que les 2 droites se croisent
        if( $a1 == $a2 ) {
            if ($verbose > 0) echo "droites // "; 
            return(false);
        }

        //calcul des points d'intersection
        $xi = ($b2 - $b1) / ($a1 - $a2);
        $yi = $a1 * $xi + $b1;

        //on test si le pt i est  sur le vecteur 1
        if ( $x1<$x2 ) {
            $maxX=$x2; $minX=$x1;
        } else {
            $maxX=$x1; $minX=$x2;
        }

        if ( $y1<$y2 ) {
            $maxY=$y2; $minY=$y1;
        } else {
            $maxY=$y1; $minY=$y2;
        }

        if( ($xi<$minX || $xi>$maxX) ||  ($yi<$minY || $yi>$maxY) ) {
            if ($verbose > 0) echo "les 2 vecteurs ne se croisent pas"; 
            return(false);
        }

        //on test si le pt i est  sur le vecteur 2
        if ( $x3<$x4 ) {
            $maxX=$x4; $minX=$x3;
        } else {
            $maxX=$x3; $minX=$x4;
        }

        if( $y3<$y4 ) {
            $maxY=$y4; $minY=$y3;
        } else {
            $maxY=$y3; $minY=$y4;
        }

        if ( ($xi<$minX || $xi>$maxX) ||  ($yi<$minY || $yi>$maxY) ) {
            if ($verbose > 0) echo "les 2 vecteurs ne se croisent pas"; 
            return(false);
        }

        // On a trouvé l'intersection
        // on fait une rotation inverse afin de retablir le repere d'origine
        $rotation=-$rotation;
        $tmp=$xi;
        $xi=$xi*cos($rotation) - $yi*sin($rotation);
        $yi=$tmp*sin($rotation) + $yi*cos($rotation);

        if ($verbose > 0) echo "intersection : (".$xi.";".$yi.")<br>";
        $encounterCoordinates = array ($xi , $yi );
        if ( $verbose > 0) echo "== Exiting dotheycross ==";
        return(true);
    }



   // This function is used to verify if two vectors are crossing each-other
   // It returns true if yes, false if no
   // If it returns true, $encountercoordinates[] gives the longitude ang latitude of the crossing
   // ============================================================================================
   // New version (called dotheycross2) of the intersection routine dotheycross. The previous
   // version is based on line equations (y=ax+b) whereas the new one is based on vectors. This
   // should 1) allow more rapid calculations and less if-then-else tests and 2) remove the need
   // for rotations of the coordinates in case one of the 2 segments are vertical.
   // History : creation date 2nd May 2007.
   function dotheycross2($x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4, &$encounterCoordinates, $verbose)
   {
      // Coast line is between two points C1 (x1,y1) and C2 (x2,y2).
      // The boat movement is between two points P1 (x3,y3) and P2 (x4,y4)
      // The intersection point of both lines is called I.

      // First test: make sure C1 != C2 and P1 != P2
      if ( $x1 == $x2 && $y1 == $y2 ) {

          if ($verbose > 0)
             echo ":dotheycross2: Le segment de cote est reduit a un point.";
          return (false);
      }
      if ( $x3 == $x4 && $y3 == $y4 ) {
          if ($verbose > 0)
             echo ":dotheycross2: Le vecteur deplacement du bateau est reduit a un point.";
          return (false);
      }

      if ( $verbose > 0) echo "== Enterring dotheycross2 ==";
     
      // Local variables
      $deltaXP = $x4 - $x3;
      $deltaYP = $y4 - $y3;
      $deltaXC = $x2 - $x1;
      $deltaYC = $y2 - $y1;
   
      // Compute the cross-product between the two segments/vectors.
      $crossProduct = $deltaYC*$deltaXP - $deltaYP*$deltaXC;

      // If the cross-product is not zero, lines are not parallel and there
      // is hence a chance that the two segments intersect.
      if ( $crossProduct != 0 ) {
         // two more local variables
         $deltaX1 = $x3-$x1;
         $deltaY1 = $y3-$y1;

         //kP is the length ratio of [P1,I] over [P1,P2]
         $kP = ($deltaY1*$deltaXC - $deltaX1*$deltaYC)/$crossProduct;
         //kC is the length ratio of [C1,I] over [C1,C2]
         $kC = ($deltaY1*$deltaXP - $deltaX1*$deltaYP)/$crossProduct;

         //If (and only if) both kP and kC are between 0 and 1
         // then the two segments have an intersection point I.
         if ( ($kP >= 0 && $kP <= 1) && ($kC >= 0 && $kC <= 1) ) {
            // Compute the coordinates of the intersection point
            $xi = $kP*$deltaXP + $x3;
            $yi = $kP*$deltaYP + $y3;
            if ($verbose > 0)
               echo ":dotheycross2: intersection : (".$xi.";".$yi.")";
            $encounterCoordinates = array ($xi , $yi );
         } else {
            if ($verbose > 0)
               echo ":dotheycross2: les 2 vecteurs ne se croisent pas";
            return(false);
         }
      } else {
         if ($verbose > 0)
            echo ":dotheycross2: droites // ";
         return(false);
      }

      if ( $verbose > 0) echo "== Exiting dotheycross2 ==";
      return(true);
   }

  //this function says how many milles the user travelled during the last
  //24hrs
  function distRecords($duration)
  {
    
    $timestamp = time();
    $position = $this->lastPositions->getOldPosition($this->users->idusers, $this->users->engaged, $timestamp - $duration);

    $lastPos = $this->lastPositions;
    $distance = ortho($lastPos->long, $lastPos->lat, $position[1], $position[2]);
    $time_elapsed = max($timestamp - $position[0], 1);

    if ($time_elapsed == 1 && $distance > 10) {
  $distance = 0;
        $corrected_distance = 0;
    } else {
        $corrected_distance =  $distance *  $duration / $time_elapsed ;
    }
    //printf ("\ndistRecords for duration = %d s , elapsed = %d, dur/ela = %f,\n distance=%f, corrected=%f\n", $duration, $time_elapsed, $duration/$time_elapsed,$distance,$corrected_distance);
    //return ($corrected_distance);
    return (array($distance,$corrected_distance));
  }


  //this function says how many milles the user travelled during the last
  //24hrs
  function olddistRecords($duration)
  {
    $sum = 0;

// A REECRIRE pour sortie de allPositions de la classe fullUsers et correction de l'imprécision
// CF mail avec Phille du 09/03 le soir.
// Concerne uniquement le moteur

    $lastPos = $this->allPositions->records[0];
    $pos = $this->allPositions[0];//scope of this var?

    foreach($this->allPositions->records as $pos)
    {
  //echo "sum = $sum, checking : ";
  //print_r($pos);
  if ( (time() - $pos->time) > $duration)
    break;
  $sum = $sum + ortho($pos->long, $pos->lat, $lastPos->long, $lastPos->lat);
  $lastPos = $pos;
    }


    return $sum;
  }

  //compute the distances in nautics
  function distFromUsers($idu)
  {
    $othersUsers = new fullUsers($idu, $this->north, $this->south, $this->west, $this->east);
    return ortho($this->lastPositions->long,
     $this->lastPositions->lat,
     $othersUsers->lastPositions->long,
     $othersUsers->lastPositions->lat);
  }

}



// Class excludedUser
// This class is used to implement tracks after a race, and to show posisions
// of dnf users

class excludedUsers
{
  var $users, //a user object
              //var deduced from others
      $lastPositions, //a postions object
      $hours,
      $anteLastPositions, //another positions obj
      $races ;    //include a race object


  // This one takes one more parameter than fullUsers (raceid)
  function excludedUsers($id, $raceid, $age = MAX_DURATION)
  {

    $this->users = new users($id);

    //find last position and time interval from now
    $lastPositionsObject = new positions;
    $lastPositionsObject->getLastPositions($this->users->idusers, $raceid);

    $this->lastPositions = $lastPositionsObject;

    $anteLastPositionsObject = new positions;
//    echo "Avant appel anteLast depuis users.class.php 2\n";
    $anteLastPositionsObject->getAnteLastPositions($this->users->idusers, $raceid);
 //   echo "Après appel anteLast depuis users.class.php 2\n";
    $this->anteLastPositions = $anteLastPositionsObject;

    $this->races = new races($raceid);
    $time = $this->lastPositions->time;
    $this->hours = (time() - $time )/3600 ;//everything is in GMT

  }


}



?>
