<?php
    $PAGETITLE = "Change player password";
    include ("htmlstart.php");
    include_once ("functions.php");
    
// Main code


    $idp = get_cgi_var("idp", "");
    $playerpassword = get_cgi_var("playerpassword", "");
    $confirm = get_cgi_var("confirm", "no");
    $action = get_cgi_var("action", "blank");
    
    if ($action == "reinit_player_password" && $confirm == "yes" && $playerpassword != "" && intval($idp) > 0 && !is_null(getPlayerObject(intval($idp))) ) {
            //We're done.
            insertAdminChangelog(Array("operation" => "Change player passwd", "rowkey" => $idp));
            $player = getPlayerObject(intval($idp));
            $player->setPassword($playerpassword);
            $player->update();            
            echo "<h3>OK, Password of ".$player->playername." @".$player->idplayers." is now : ".$playerpassword." (player email is : ".$player->email.")</h3>";
    } else if ($action == "reinit_player_password" && $confirm == "no" && $playerpassword != "" && intval($idp) > 0 && !is_null(getPlayerObject(intval($idp))) ) {
        echo "<h2>Confirm the idp and the password you want to change... PLEASE USE WITH CAUTION !</h2>";
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="reinit_player_password" />
            <input type="hidden" name="confirm" value="yes" />
            <span>@Id Player :</span>
            <input name="idp" value="<?php echo $idp; ?>" /><br />
            <span>New Password :</span>
            <input name="playerpassword" value="<?php echo $playerpassword; ?>" /><br />
            <input type="submit" value="Confirm password change !" />
        </form>
<?
    } else {
        echo "<h2>Input the idp and the password you want to change... PLEASE USE WITH CAUTION !</h2>";
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="reinit_player_password" />
            <input type="hidden" name="confirm" value="no" />
            <span>@Id Player :</span>
            <input name="idp" value="<?php echo $idp; ?>" /><br />
            <span>New Password :</span>
            <input name="playerpassword" value="<?php echo $playerpassword; ?>" /><br />
            <input type="submit" value="Change player password" />
        </form>
<?
    }
    
    include ("htmlend.php");
?>
