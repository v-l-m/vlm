<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    $res = $this->myQuery("UPDATE races SET updated = NOW() WHERE idraces = '".$_REQUEST['PME_data_idraces']."';");
    return True;
?>
