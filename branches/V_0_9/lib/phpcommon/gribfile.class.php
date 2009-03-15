<?php
class gribFile
{
  var 
	$minlat,
	$maxlat,
	$minlong,
	$maxlong,
	$validity;

  function store($filename)
  {
    // Todo : tester le nombre d'arguments. Attention, en PHP, $argc compte le nom du script
    $handle = fopen($filename, "r");
    if ( $handle ) {
    	printf ("Gribfile opened filename=%s\n", $filename);

    // get latitudes for the zone (min/max)
    $buffer = fscanf ($handle, "%s\t%s\n");
    list ($this->maxlat , $this->minlat) = $buffer;

    printf ("Lat min/max = %f / %f\n", $this->minlat, $this->maxlat);

    // get longitudes for the zone (min/max)
    $buffer = fscanf ($handle, "%s\t%s\n");
    list ($this->minlong , $this->maxlong) = $buffer;

    // Handle case of a square near -180 W / 180E (if longi <0 the loop will fail in a 0 length "grid" )
    if ( $this->minlong < 0 ) $this->minlong+=360;
    if ( $this->maxlong < 0 ) $this->maxlong+=360;
    printf ("Long min/max = %f / %f\n", $this->minlong, $this->maxlong);
    // Those values are corrected in the longi loop.

    list($h,$i,$s,$m,$d,$y) = explode(',',date('H,i,s,m,d,Y', time()));
    $now_pile=mktime($h,$i,$s,$m,$d,$y);
    //$now_rond=$now_pile - 35*60;
    $now_rond=mktime($h,0,0,$m,$d,$y);

    $i=0;
    for ($lati=$this->maxlat; $lati >= $this->minlat ; $lati-=0.5 )	
    {
    //printf ("%f\n", $lati);
      for ($longi=$this->minlong; $longi <= $this->maxlong ; $longi+=0.5 )
      {
          //printf ("%f\n", $longi);
          if ( $longi > 180 ) {
	  	$corrected_longi = $longi - 360;
	  } else {
	  	$corrected_longi = $longi;
	  }

          $buffer = fscanf ($handle, "%s\t%s\t%s\t%s\n");  
       	  list ($uwind , $vwind, $uwind3, $vwind3) = $buffer;
	
	  $query0 = "REPLACE INTO wind "  
	     .      " (latitude, longitude, wspeed, wheading, time, uwind, vwind, uwind3, vwind3)" 
	     .      "   VALUES ( " 
	     .                    $lati  . ", " 
	     .                    $corrected_longi . ", "  
	     .                    norm($uwind, $vwind) . ", " 
	     .                    angle($uwind, $vwind)  . ", "
	     .			  $now_rond . ", "
	     .                    $uwind . ", "
	     .                    $vwind . ", "
	     .			  $uwind3 . ", "
	     .			  $vwind3
	     .                   ");"   ;


	  if ($verbose != 0) echo $query0."\n";

	  if ( $longi == round ($longi ) && $lati == round ($lati) ) {
	    wrapper_mysql_db_query(DBNAME,$query0);
	  }

    	  $i++;
        }
      }
      printf ("Gridsize = %d\n", $i);
      fclose($handle);
    }
    else
    {
    	printf ("Gribfile can not be opened filename=%s\n", $filename);
    }
   }



  // Deletes old wind data (> 1 day)
  function clean()
  {
      $query0 = "DELETE FROM wind WHERE time < " . (time() - 86400)  ;

      wrapper_mysql_db_query(DBNAME,$query0);
      echo $query0 ."\n";
      if ($verbose != 0) echo $query0."\n";
   }

  // Dicwvision de la force du vent en approchant du pole nord
  function zerowind($lat = 89)
  {
      $latitude=$lat;
      while ( $latitude < 90 ) {
      	$query0 = "update wind set uwind=uwind*0.7,vwind=vwind*0.7,uwind3=uwind3*0.7,vwind3=vwind3*0.7  where abs(latitude) > " . $latitude  ;

      	wrapper_mysql_db_query(DBNAME,$query0);
      	echo $query0 ."\n";
      	if ($verbose != 0) echo $query0."\n";
        $latitude++;
      }
   }

  function newstore($filename)
  {
    // Todo : tester le nombre d'arguments. Attention, en PHP, $argc compte le nom du script
    $handle = fopen($filename, "r");
    if ( $handle ) {
    	printf ("Gribfile opened filename=%s\n", $filename);
    } else {
    	printf ("Gribfile can not be opened filename=%s\n", $filename);
        exit;
    }

    // get latitudes for the zone (min/max)
    $buffer = fscanf ($handle, "%s\t%s\n");
    list ($this->maxlat , $this->minlat) = $buffer;

    printf ("Lat min/max = %f / %f\n", $this->minlat, $this->maxlat);

    // get longitudes for the zone (min/max)
    $buffer = fscanf ($handle, "%s\t%s\n");
    list ($this->minlong , $this->maxlong) = $buffer;

    // Handle case of a square near -180 W / 180E (if longi <0 the loop will fail in a 0 length "grid" )
    if ( $this->minlong < 0 ) $this->minlong+=360;
    if ( $this->maxlong < 0 ) $this->maxlong+=360;
    printf ("Long min/max = %f / %f\n", $this->minlong, $this->maxlong);
    // Those values are corrected in the longi loop.

    // get GribValidity
    $buffer=fscanf ($handle, "%s\n");
    list($this->validity) =$buffer; 

    $i=0;
    for ($lati=$this->maxlat; $lati >= $this->minlat ; $lati-=0.5 )	
    {
    //printf ("%f\n", $lati);
      for ($longi=$this->minlong; $longi <= $this->maxlong ; $longi+=0.5 )
      {
          //printf ("%f\n", $longi);
          if ( $longi > 180 ) {
	  	$corrected_longi = $longi - 360;
	  } else {
	  	$corrected_longi = $longi;
	  }

          $buffer = fscanf ($handle, "%s\t%s\n");  
       	  list ($uwind , $vwind) = $buffer;
	
	  $query0 = "REPLACE INTO winds "  
	     .      " (latitude, longitude, time, uwind, vwind)" 
	     .      "   VALUES ( " 
	     .                    $lati  . ", " 
	     .                    $corrected_longi . ", "  
	     .			  $this->validity  . ", "
	     .                    $uwind . ", "
	     .                    $vwind . ")"     ;

	  // Ajout d'une ligne pour -180 (PB antemeridien)
	  if ( $corrected_longi == 180 ) {
	       $query0 .=     "   , ( "
	               .                    $lati  . ", "
	               .                    -180   . ", "
	               .                    $this->validity  . ", "
	               .                    $uwind . ", "
	               .                    $vwind . ")"     ;

	  }

	  $query0 .= ";";

	  if ($verbose != 0) echo $query0."\n";

	  if ( $longi == round ($longi ) && $lati == round ($lati) ) {
	    wrapper_mysql_db_query(DBNAME,$query0);
	  }

    	  $i++;
        }
      }
      printf ("Gridsize = %d\n", $i);
      fclose($handle);
   }

  // Deletes old wind data (> 1 day)
  function newclean()
  {
      $query0 = "DELETE FROM winds WHERE time < " . (time() - 3600)  ;

      wrapper_mysql_db_query(DBNAME,$query0);
      echo $query0 ."\n";
      if ($verbose != 0) echo $query0."\n";
   }


}
?>
