<?php
    include_once ("functions.php");

    //Get Flaglist to build the image
    $info = array();
    $flagres = getFlagsListCursor();
    $index = 0;
    while ($row = mysql_fetch_array($flagres, MYSQL_ASSOC) ) 
    {
        $info[$index++]=$row['idflags'];
    }
    mysql_free_result($flagres);

    $ret = sort ($info);
    
    $IMG_PITCH = 16;
    $IMG_WIDTH = 30;
    $IMG_HEIGHT = 20;
    $count = count($info);
    $width = $count % $IMG_PITCH;
    $row = 0;
    $col = 0;

    $img = imagecreatetruecolor ($IMG_WIDTH*$IMG_PITCH,(int)($count/$IMG_PITCH)*(1+$IMG_HEIGHT));
    foreach ($info as $flagid)
    {
        //echo $flagid."**".DIRECTORY_COUNTRY_FLAGS."<br>";
        /*if ( ! file_exists("cache\/flags\/".$flagid.".png") )
        {
            echo 'clic link to build missing cache flag... and try again...';
            echo '<img src="/flagimg.php?idflags='.$flagid.'"/>';

        }*/
        $original = getflag($flagid);
        $src=imagecreatefrompng ($original);
        imagecopymerge($img,$src,$col*$IMG_WIDTH,$row*$IMG_HEIGHT,0,0,$IMG_WIDTH, $IMG_HEIGHT,100);
        imagedestroy($src);
        $col++;
        if ($col == $IMG_PITCH)
        {
            $row++;
            $col=0;
        }
    }
    $map = DIRECTORY_COUNTRY_FLAGS . "/flagsmap.png";
    imagepng($img,$map);
    echo 'Flag map recreated at <a><img src="/'.$map.'"/></a>';
    
?>