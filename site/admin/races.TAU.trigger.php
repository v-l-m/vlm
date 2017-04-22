<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    $res = $this->myQuery("UPDATE races SET updated = NOW() WHERE idraces = '".$_REQUEST['PME_data_idraces']."';");

    // if races is not started, then update all users which position is not WP0
    $res = $this->myQuery("select idraces, startlong, startlat from races where started=0 and idraces='".$this->rec."';");
    echo "getting changed raceid (if any)".$this->rec."-".$_REQUEST['PME_data_idraces']."\n";
    echo $res;
    return True;
?>
