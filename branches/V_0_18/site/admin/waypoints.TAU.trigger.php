<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    $res = $this->myQuery("UPDATE races SET updated = NOW() WHERE idraces IN (SELECT idraces FROM races_waypoints WHERE idwaypoint = '".$this->rec."');");
    return True;
?>
