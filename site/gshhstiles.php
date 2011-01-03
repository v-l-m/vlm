<?php
    include_once("config-defines.php");

    $tilex = intval(get_cgi_var('x', 0));
    $tiley = intval(get_cgi_var('y', 0));
    $tilez = intval(get_cgi_var('z', 0));
    $force = get_cgi_var('force', 'no');

    if ( $tilez < 0 || $tilez > 20) die("Bad z index");
    $sizetile = pow(2, $tilez);

    $original = sprintf("%s/%d/%d/%d.png", DIRECTORY_GSHHSTILES, $tilez, $tilex, $tiley);
    $originaldir = sprintf("%s/%d/%d", DIRECTORY_GSHHSTILES, $tilez, $tilex);

    $tilex = $tilex % $sizetile;
    $tiley = $tiley % $sizetile;
    while ($tilex < 0) $tilex += $sizetile;
    while ($tiley < 0) $tiley += $sizetile;    

    while ($tilex >= $sizetile) $tilex -= $sizetile;
    while ($tiley >= $sizetile) $tiley -= $sizetile;
  
    $regular = sprintf("%s/%d/%d/%d.png", DIRECTORY_GSHHSTILES, $tilez, $tilex, $tiley);
    $regulardir = sprintf("%s/%d/%d", DIRECTORY_GSHHSTILES, $tilez, $tilex);
    
    // Création et mise en cache
    if ( ( ! file_exists($original) ) ||  ($force == 'yes') ) {
        if (!is_dir($originaldir)) mkdir($originaldir, 0777, True);
        if (defined("TILES_SOURCE_SERVER")) {
            copy(sprintf("%s/%s", TILES_SOURCE_SERVER, $original), $original);
        } else if (file_exists($regular) ) {
            copy($regular, $original);
        } else {
            $rivers = "";
            if ($tilez > 4) $rivers = sprintf("--rivers %s ", GSHHS_CLIPPED_RIVER_FILENAME);
            $execcmd = sprintf("%s --n_tiles %d --x_tile %d --y_tile %d -d --coast_file %s %s -a %s --water_alpha 0x7F -t %s", TILES_G_PATH, pow(2, $tilez), $tilex, $tiley, GSHHS_CLIPPED_FILENAME, $rivers, GSHHS_CLIPPED_TOPO_FILENAME, $original);
//            print $execcmd; die();
            shell_exec($execcmd);
        }
    }

    header("Content-Type: image/png");
    header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
    readfile($original);
?>
 


