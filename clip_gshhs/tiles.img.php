<?php
    include_once("config.php");
    //include_once("tiles.class.php");
    define("TILES_G_PATH", "/home/commun/gshhs_steph/tiles_g");
    define("TILES_PATH", "/home/commun/tiles");
    define("TILES_BD_PATH", "/home/commun/gshhs_steph/bd/poly-f-1.dat");

    $tilex = intval(get_cgi_var('x', 0));
    $tiley = intval(get_cgi_var('y', 0));
    $tilez = intval(get_cgi_var('z', 0));
    $force = get_cgi_var('force', 'no');

    if ( $tilez < 0 && $tilez > 20 ) die("bad z index");
    $sizetile = pow(2, $tilez);
    $tilex = $tilex % $sizetile;
    $tiley = $tiley % $sizetile;
    if ($tilex < 0) $tilex += $sizetile;
    if ($tiley < 0) $tiley += $sizetile;    
  
    $original = sprintf("%s/%d/%d/%d.png", TILES_PATH, $tilez, $tilex, $tiley);
    $originaldir = sprintf("%s/%d/%d", TILES_PATH, $tilez, $tilex);
    
    // Création et mise en cache
    if ( ( ! file_exists($original) ) ||  ($force == 'yes') ) {
        //$mapObj = new tiles($tilez, $tilex, $tiley);
        //$mapObj->drawMap($mapObj->proj.'Long2x', $mapObj->proj.'Lat2y', "", MAP_POLYLINE_MODE, False);
        if (!is_dir($originaldir)) mkdir($originaldir, 0777, True);
        //imagepng($mapObj->mapImage, $original);
        $execcmd = sprintf("%s %d %d %d %s %s", TILES_G_PATH, pow(2, $tilez), $tilex, $tiley, TILES_BD_PATH, $original);
//        print $execcmd;
        shell_exec($execcmd);
    }
    header("Content-Type: image/png");
    header("Cache-Control: max-age=864000"); // default 10 days should be tunable.

    readfile($original);
?>
 


