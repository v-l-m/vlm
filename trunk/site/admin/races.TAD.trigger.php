<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        die();
    }
    $res = $this->myQuery("SELECT count(*) as n FROM users WHERE engaged = '".$this->rec."';");
    $row = mysql_fetch_assoc($res);
    if (intval($row['n']) > 0) {
        echo "<div class=\"adminwarnbox\">";
        echo "<h3>There are still players engaged in this race.</h3>";
        htmlQuery("select idusers, username, boatname, class, engaged, from_unixtime(lastchange) as lastchange from users where engaged = '".$this->rec."';");
        echo "<h3>Delete abort</h3>";
        echo "</div>";
        return False;
    } else {

        #FIXME : there should be an additionnal report for this
        $res = $this->myQuery("DELETE FROM waypoints WHERE idwaypoint IN (SELECT idwaypoint FROM races_waypoints as RW WHERE RW.idraces = '".$this->rec."');");
        $res = $this->myQuery("DELETE FROM races_waypoints WHERE idraces = '".$this->rec."';");
        $res = $this->myQuery("DELETE FROM races_instructions WHERE idraces = '".$this->rec."';");
        $res = $this->myQuery("DELETE FROM racesmap WHERE idraces = '".$this->rec."';");
        
        echo "<div class=\"adminbox\">";
        insertAdminChangelog($operation = "Delete all race datas (racesmap, waypoints, waypoints links, instructions) for race : ".$this->rec);
        echo "  <h3>Corresponding waypoints and race instructions have been also deleted.</h3>";
        echo "</div>";
        return True;
    }
?>
