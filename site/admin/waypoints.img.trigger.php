<?php
    require_once("functions.php");
    $res = $this->myQuery("SELECT RWP.idraces, WP.maparea, WP.latitude1, WP.longitude1, WP.latitude2, WP.longitude2, RWP.wporder FROM races_waypoints as RWP, waypoints as WP WHERE WP.idwaypoint = RWP.idwaypoint AND WP.idwaypoint = ".$this->rec);
    $row = mysql_fetch_assoc($res);
    $center = centerDualCoordMilli($row['latitude1'], $row['longitude1'], $row['latitude2'], $row['longitude2']);
    $url = sprintf("/mercator.img.php?idraces=%d&boat=1&lat=%f&long=%f&maparea=%d&drawwind=no&tracks=on&maptype=compas&wp=%d&age=0&ext=right&x=800&y=600&proj=mercator",
        $row['idraces'],
        $center['mlat']/1000.,
        $center['mlon']/1000.,
        $row['maparea'],     
        $row['wporder']
        );
    echo "<a target=\"_blank\" href=\"$url\">";
    echo "<img width=\"200\" height=\"150\" src=\"$url\" />";
    echo "</a>";
    return True;
?>
