<?php
    require_once("functions.php");
    if (intval($this->rec) < 1) {
        return False;
    }
    $res = $this->myQuery("DELETE FROM playerstousers WHERE idplayers = ".$this->rec.");
    
    echo "<div class=\"adminbox\">";
    insertAdminChangelog(Array("operation" => "Delete all players/boats links", "rowkey" => "".$this->rec));
    echo "  <h3>Corresponding links with boats have been also deleted.</h3>";
    echo "</div>";
    return True;
?>
