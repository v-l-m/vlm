<?php
    require_once("functions.php");


    $oldrectocopy = $this->get_cgi_var('oldrectocopy');
    if (intval($oldrectocopy) > 0) {
         //There could be some things to copy...

        $rec = $this->get_cgi_var("PME_data_idraces");
        $copy_list = Array(
            "races_instructions" => "INSERT INTO races_instructions (idraces, instructions, flag) SELECT '".$rec."', instructions, flag FROM races_instructions WHERE idraces = '".$oldrectocopy."';",
            "racesmap" => "INSERT INTO racesmap (idraces, racemap) SELECT '".$rec."', racemap FROM racesmap WHERE idraces = '".$oldrectocopy."';",
            );
        echo "<div class=\"adminwarnbox\">";
        print "Following operations have been performed : <br /><ul>";
        foreach ($copy_list as $k => $v) {
            $res = $this->myQuery($v);
            $a = $this->sql_fetch($res);
            print "<li>Copy from $k : $v</li>";
        }
        
        $res = $this->myQuery("SELECT * FROM races_waypoints WHERE idraces = '".$oldrectocopy."';");
        while ($row = $this->sql_fetch($res) ) {
            $insertracewp = "INSERT INTO races_waypoints (idraces, wporder, idwaypoint, laisser_au, wptype) VALUES ("
                        .$rec.", "
                        .$row['wporder'].", "
                        .sprintf("%d%02d", $rec, $row['wporder']).", "
                        .$row['laisser_au'].", "
                        ."'".$row['wptype']."' "
                        .");";
            print "<li>Copy from races_waypoints : ".$insertracewp."</li>";
            $res2 = $this->myQuery($insertracewp);
            $insertwp = "INSERT INTO waypoints (`idwaypoint`, `latitude1`, `longitude1`, `latitude2`, `longitude2`, `libelle`, `maparea`) SELECT "
                        .sprintf("%d%02d", $rec, $row['wporder']).", `latitude1`, `longitude1`, `latitude2`, `longitude2`, `libelle`, `maparea` "
                        ."FROM waypoints WHERE idwaypoint = ".$row['idwaypoint'].";";
            print "<li>Copy from waypoints : ".$insertwp."</li>";
            $res2 = $this->myQuery($insertwp);
          }        
        print "</ul></div>";
        insertAdminChangelog(Array("operation" => "Copy all race datas (racesmap, waypoints, instructions) from race $oldrectocopy to $rec"));
    }
    return True;
?>
