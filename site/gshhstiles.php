<?php
    include_once("config.php");

    $tilex = intval(get_cgi_var('x', 0));
    $tiley = intval(get_cgi_var('y', 0));
    $tilez = intval(get_cgi_var('z', 0));
    $force = get_cgi_var('force', 'no');

    if ( $tilez < 0 && $tilez > 20 ) die("Bad z index");
    $sizetile = pow(2, $tilez);
    $tilex = $tilex % $sizetile;
    $tiley = $tiley % $sizetile;
    if ($tilex < 0) $tilex += $sizetile;
    if ($tiley < 0) $tiley += $sizetile;    
  
    $original = sprintf("%s/%d/%d/%d.png", DIRECTORY_GSHHSTILES, $tilez, $tilex, $tiley);
    $originaldir = sprintf("%s/%d/%d", DIRECTORY_GSHHSTILES, $tilez, $tilex);
    
    // Création et mise en cache
    if ( ( ! file_exists($original) ) ||  ($force == 'yes') ) {
        if (!is_dir($originaldir)) mkdir($originaldir, 0777, True);
        if (defined("TILES_SOURCE_SERVER")) {
            copy(sprintf("%s/%s", TILES_SOURCE_SERVER, $original), $original);
        } else {
            $execcmd = sprintf("%s %d %d %d %s %s", TILES_G_PATH, pow(2, $tilez), $tilex, $tiley, GSHHS_CLIPPED_FILENAME, $original);
            shell_exec($execcmd);
        }
    }
    header("Content-Type: image/png");
    header("Cache-Control: max-age=864000"); // default 10 days should be tunable.
    readfile($original);
?>
 


