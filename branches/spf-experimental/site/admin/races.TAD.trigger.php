<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    #FIXME : there should be an additionnal report for this
    $res = $this->myQuery("DELETE FROM waypoints WHERE idwaypoint IN (SELECT idwaypoint FROM races_waypoints as RW WHERE RW.idraces = '".$this->rec."');");
    $res = $this->myQuery("DELETE FROM races_waypoints WHERE idraces = '".$this->rec."';");
    $res = $this->myQuery("DELETE FROM races_instructions WHERE idraces = '".$this->rec."';");
    $res = $this->myQuery("DELETE FROM racesmap WHERE idraces = '".$this->rec."';");
    
    echo "<div class=\"adminbox\">";
    insertAdminChangelog(Array("operation" => "Delete all race datas (racesmap, waypoints, waypoints links, instructions) for race : ".$this->rec));
    echo "  <h3>Corresponding waypoints and race instructions have been also deleted.</h3>";
    echo "</div>";
    return True;
?>
