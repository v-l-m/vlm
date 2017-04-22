<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    $res = $this->myQuery("UPDATE races SET updated = NOW() WHERE idraces = '".$_REQUEST['PME_data_idraces']."';");

    // if races is not started, then update all users which position is not WP0
    $res = $this->myQuery("select idraces, startlong, startlat from races where started=0 and idraces='".$this->rec."';");
    $res = $this->myQuery("update positions p inner join races r on p.race = r.idraces  set p.long = r.startlong, p.lat = r.startlat where r.started = 0 and r.idraces = '".$this->rec."';");
    echo $res;
    return True;
?>
