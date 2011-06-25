<?php
  //smallest possible code
  @include_once('config-defines.php');
  @require_once('griblib.php');

  $row = getLastUpdateRow();
  $lastupdate = $row2['time'];

  #Are we late for running engine ?
  if ((time() - $lastupdate) > 2*DELAYBETWEENUPDATE ) die503("KO");

  #Are we slow ?
  $duration = max($row2['duration'],0.001);
  if ($duration > UPDATEDURATION) die503("KO");
  
  #Are we late for grib refresh
  $minmax = get_grib_minmax_time()
  if ( (time() - $minmax['min']) > 10*3600 ) die503("KO");
  
  echo "OK";
  #debug
  #echo "\n".(time()-$lastupdate)."\n".($duration)."\n".(time() - $minmax['min']);
?>
