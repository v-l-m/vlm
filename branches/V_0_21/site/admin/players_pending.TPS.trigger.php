<?php
    $row = $this->sql_fetch($this->myQuery("SELECT * FROM players_pending WHERE `idplayers_pending` = ".$this->rec));
    print "Click <a target=\"_blank\" href=\"/create_player.php?createplayer=validate&emailid=".urlencode($row['email']).'&seed='.$row['seed']."\">here</a> to validate the player subscription. <b>Use only when asked by the player !</b>";
    print "<hr />";
?>
