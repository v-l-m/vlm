<?php
    /*
     * make a grib "tile" for better caching and bandwidth management.
     * called when origin url doesn't (yet) exists
     * origin cached url : /gribtiles/<south>/<west>/<AAAAMMJJHH>.<STEP>.grb
     * - AAAAMMJJHH : grib's date
     * - STEP : 05 or 15° (size of the tile)
     * - <south> : South longitude (modulo STEP) of the tile
     * - <west> : West latitude (modulo STEP) of the tile
     * This should fail if :
     * - STEP is not 05 or 15
     * - south / west are not consistent with step
     * - no grib file available for AAAAMMJJHH
     * TODO : clean old files from cache in noaa scripts
     */
    include_once("config-defines.php");

    $step = get_cgi_var('step', 15);
    if ($step != "05" && $step != "15") die("Bad step");
    $step = intval($step);
    $south = intval(get_cgi_var('south', 0));
    if (($south % $step) != 0 || $south < -90 || $south+$step > 90) die("South invalid");
    $west = intval(get_cgi_var('west', 0));
    if (($west % $step) != 0 || $west < -180 || $west+$step > 180) die("West invalid");
    $grib_date = intval(get_cgi_var("date", date("Ymd")."00"));
    $force = get_cgi_var('force', 'no');
    $gribfile = sprintf("%s/gfs_NOAA-%s.grb", GRIB_DIRECTORY, $grib_date);
    if (! file_exists($gribfile)) die("Grib unavailable");

    $dlname = sprintf("%d.%02d.%d.%d.grb", DIRECTORY_GRIBTILES, $grib_date, $step, $south, $west);
    $originaldir = sprintf("%s/%d/%d", DIRECTORY_GRIBTILES, $south, $west);
    $original = sprintf("%s/%d.%02d.grb", $originaldir, $grib_date, $step);

    // Création et mise en cache
    if ( ( ! file_exists($original) ) ||  ($force == 'yes') ) {
        if (!is_dir($originaldir)) {
            umask(0002);
            mkdir($originaldir, 0777, True);
        }
        //ggrib ~/vlmdatas/gribs/latest.grb out.grb -15 45 0 60
        $execcmd = sprintf("%s %s %s %d %d %d %d", GGRIB_PATH, $gribfile, $original, $west, $south, $west+$step, $south+$step);
//            print $execcmd; die();
            shell_exec($execcmd);
    }

    header("Content-Type: image/png");
    header("Cache-Control: max-age=86400"); // default 1 day
    header(sprintf("Content-Disposition: attachment; filename=%s", $dlname));
    readfile($original);
    exit(0); //To prevent bad spaces appended from php script
?>
