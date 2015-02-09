<?php
        print "<ul>";
        $copy_list = Array(
            "waypoints" => "SELECT * FROM waypoints WHERE idwaypoint IN (SELECT idwaypoint FROM races_waypoints as RW WHERE RW.idraces = '".$this->rec."');",
            "races_waypoints" => "SELECT *  FROM races_waypoints WHERE idraces = '".$this->rec."';",
            "races_instructions" => "SELECT * FROM races_instructions WHERE idraces = '".$this->rec."';",
            "racesmap" => "SELECT idraces, length(racemap) as size FROM racesmap WHERE idraces = '".$this->rec."';",
            );
        foreach ($copy_list as $k => $v) {
            print "<li>Copy from $k :<br />";
            print htmlQuery($v);
            print "</li>";
        }
        print "</ul>";
?>
