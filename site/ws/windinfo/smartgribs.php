<?php
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8"); //FIXME ?

    // NOT AUTHENTIFIED

    $ws = new WSBase();

    $step  = intval(get_cgi_var('step', 15));
    if ($step != 5 && $step != 15) $ws->reply_with_error("GRB01");

    //La zone, arrondie, au step le plus proche
    $north = $step*ceil(floatval(get_cgi_var('north'))/$step);
    $south = $step*floor(floatval(get_cgi_var('south'))/$step);
    if (is_null($south) || is_null($north) || $south >= $north) $ws->reply_with_error("GRB02");
    $east  = $step*ceil(floatval(get_cgi_var('east'))/$step); 
    $west  = $step*floor(floatval(get_cgi_var('west'))/$step);
    if (is_null($west) || is_null($east)) $ws->reply_with_error("GRB02");
    if ($west >= $east) $west -= 360;

    $grib_date = intval(get_cgi_var("date", 0));
    if ($grib_date === 0) {
      require_once("vlmc.php");
      $global_vlmc_context = new vlmc_context();
      global_vlmc_context_set($global_vlmc_context);
      shm_lock_sem_construct_grib(1);
      $nb_grib = get_prevision_count();
      $ts = get_prevision_time_index(0);
      shm_unlock_sem_destroy_grib(1);
      if ($nb_grib <= 5) {
        $ts -= 6*3600; // 6 hours before
        $ws->maxage = 30; // update is running, retry very soon
      } else {
        $ws->maxage = $ts - time() + 34200; //grib offset + 9h30
      }
      //we change maxage only when no date has been given.
      $grib_date = intval(date("YmdH", $ts));
    }
    
    $gribfile = sprintf("%s/gfs_NOAA-%s.grb", GRIB_DIRECTORY, $grib_date);
    if (! file_exists($gribfile)) $ws->reply_with_error("GRB03");
    
    //Préremplissagge
    $ws->answer['request'] = Array('time_request' => $ws->now, 'north' => $north, 'south' => $south, 'east' => $east, 'west' => $west, 'step' => $step, 'date' => $grib_date);
    $ws->answer['gribs_url'] = Array();
        
    $count = 0;
    while ($south < $north) {
      $twest = $west;
      while ($twest < $east) {
        $rwest = $twest;
        while ($rwest < -180) {
          $rwest += 360;
        }
        $originaldir = sprintf("%d/%d", $south, $rwest);
        $original = sprintf("%s/%d.%02d.grb", $originaldir, $grib_date, $step);
        $ws->answer['gribs_url'][] = $original;
        $count += 1;
        $twest += $step;
      }
      $south += $step;
    }
    $ws->answer['count_url'] = $count;
    $myfile = fopen( DIRECTORY_GRIBFILES. "/GribCacheIndex", "r");
    if ($myfile)
    {
      $CacheIndexStr = fread($myfile,200);
      $ws->answer['GribCacheIndex'] = trim(preg_replace('/\s\s+/', ' ', $CacheIndexStr));
      fclose($myfile);
    }
    
    $ws->reply_with_success();
?>
