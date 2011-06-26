<?php
  //smallest possible code
  @include_once('config-defines.php');
  @require_once('griblib.php');
  @require_once('functions.php');

  $now = time();
  $row = getLastUpdateRow();
  $lastupdate = $row2['time'];
  $duration = max($row2['duration'],0.001);
  $minmax = get_grib_minmax_time();
  $min = minmax['min']

  $msg  = "\n";
  $msg .= "NOW: $now\n";
  $msg .= "Last update: $lastupdate\n";
  $msg .= "Time from last update: ".($now-$lastupdate)."\n";
  $msg .= "Last engine duration: $duration\n";
  $msg .= "Time from last weather forecast : ".($now - $min);


  #Are we late for running engine ?
  if (($now - $lastupdate) > 2*DELAYBETWEENUPDATE ) die503("KO$msg");

  #Are we slow ?
  if ($duration > UPDATEDURATION) die503("KO$msg");
  
  #Are we late for grib refresh
  if ( ($now - $min) > 10*3600 ) die503("KO$msg");
  
  echo "OK";
?>
