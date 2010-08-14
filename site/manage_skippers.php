<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");

    $actionskipper = get_cgi_var("actionskipper", "confirm_cancel");
    $pu_idusers = get_cgi_var("pu_idusers", '');
    $pu_idplayers = get_cgi_var("pu_idplayers", '');
    $pu_linktype = get_cgi_var("pu_linktype", 2);

    function htmlDelLinktype(&$rel) {
        $ret = "";
        $ret .= "<form name=\"linktype\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
        $ret .= "<input type=\"hidden\" name=\"pu_idusers\" value=\"".$rel['idusers']."\" />";
        $ret .= "<input type=\"hidden\" name=\"pu_idplayers\" value=\"".$rel['idplayers']."\" />";
        $ret .= "<input type=\"hidden\" name=\"pu_linktype\" value=\"".$rel['linktype']."\" />";        
        $ret .= "<input type=\"hidden\" name=\"actionskipper\" value=\"request_delete\" />";
        $ret .= "<input type=\"submit\" value=\"".getLocalizedString("delete")."\" />";
        $ret .= "</form>";
        return $ret;
    }
    
    function checkUserPlayerInput(&$u, &$p, &$msg) {
        if (is_null($u) || is_null($p)) {
            $msg .= getLocalizedString("Error when selecting boat or player");
            return False;
        }
        if (!in_array($u->idusers, getLoggedPlayerObject()->getOwnedBoatIdList())) {
            $msg .= getLocalizedString("You are not owner of this boat !");
        }
        return True;
    }

    function checkAddBoatsit(&$u, &$p, &$msg) {
        if (!checkUserPlayerInput($u, $p, $msg)) return False;
        if (in_array($u->idusers, $p->getOwnedBoatIdList())) {
            $msg .= getLocalizedString("This boat is already owned by the player");
            return False;
        }
        if (in_array($u->idusers, $p->getBoatsitIdList())) {
            $msg .= getLocalizedString("This boat is already boatsitted by the player");
            return False;
        }
        return True;
    }

    function checkDelBoatsit(&$u, &$p, &$msg) {
        if (!checkUserPlayerInput($u, $p, $msg)) return False;
        if (!in_array($u->idusers, $p->getBoatsitIdList())) {
            $msg .= getLocalizedString("This boat is not currently boatsitted by the player");
            return False;
        }
        return True;
    }

    
    $player = getLoggedPlayerObject();
    $userObj = getUserObject($pu_idusers);
    $playerObj = getPlayerObject($pu_idplayers);
    $pu_linktype = PU_FLAG_BOATSIT; // FORCE le boatsit pour des raisons de sécurités pour l'instant

    $actionlist = Array("confirm_cancel");

    $msg = "";
    switch($actionskipper) {
        case 'request_delete' :
            if (checkDelBoatsit($userObj, $playerObj, $msg)) {
                $actionskipper = 'confirm_delete';
            }
            $actionlist[] = $actionskipper;
            break;
        case 'request_add' :
            if (checkAddBoatsit($userObj, $playerObj, $msg)) {
                $actionskipper = 'confirm_add';
            }
            $actionlist[] = $actionskipper;
            break;
        default :
            switch($actionskipper) {
                case 'confirm_add' :
                    if (!checkAddBoatsit($userObj, $playerObj, $msg)) {
                        break;
                    } else if (!$userObj->setRelationship($playerObj->idplayers, $pu_linktype)) {
                        $msg .= getLocalizedString("Error when attaching the boat");
                    } else {
                        $msg .= getLocalizedString("OK, player is now boatsitter of this boat.");
                    }
                    break;
                case 'confirm_delete' :
                    if (!checkDelBoatsit($userObj, $playerObj, $msg)) {
                        break;
                    } else if (!$userObj->removeRelationship($playerObj->idplayers, $pu_linktype)) {
                        $msg .= getLocalizedString("Error when deleting relationship with the boat");
                    } else {
                        $msg .= getLocalizedString("OK, player is not anymore boatsitter of this boat.");
                    }
                    break;                    
                case 'confirm_cancel' :
                default :
            }
            $pu_idusers = '';
            $pu_idplayers = '';
            $pu_linktype = 2;
            $actionlist[] = 'request_add';
            $actionlist[] = 'request_delete';
            $actionskipper = 'request_add';
    }

    $grantlist = $player->getGrantedBoatList();

    echo "<h2>".getLocalizedString("Boat-sitting management")."</h2>";
    echo $msg;
    echo "<div id=\"skipperlistbox\">";
    echo "<table>";
    echo "<tr>";
    echo "<form name=\"manage\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
    echo '<input type="hidden" name="pu_linktype" value="'.$pu_linktype.'" />';
    echo '<td>';
    echo '<select name="pu_idplayers">';
    foreach (getPlayerList("idplayers != ".getPlayerId() ) as $row) {
        echo '<option value="'.$row['idplayers'].'"';
        if ($row['idplayers'] == $pu_idplayers) echo " selected";
        echo '>'.$row['playername'].'</option>\n';
    }
    echo "</select>\n";
    echo '</td>';
    echo '<td>';
    echo "<select name=\"actionskipper\" >";
    foreach ($actionlist as $actionvalue) {
        echo "<option value=\"".$actionvalue."\"";
        if ($actionvalue == $actionskipper) echo " selected";
        echo ">".getLocalizedString(substr($actionvalue, 1+strpos($actionvalue, "_")))."</option>";
    }
    echo "</select>";
    echo '</td>';
    echo '<td>';
    echo '<select name="pu_idusers">';
    foreach ($player->getOwnedBoatIdList() as $id) {
        $u = getUserObject($id);
        echo '<option value="'.$u->idusers.'"';
        if ($u->idusers == $pu_idusers) echo " selected";
        echo ">".$u->username."</option>";
    }
    echo "</select>";
    echo '</td>';
//    <input size="10" maxlength="10" name="pu_idusers" value="'.$pu_idusers.'" /></td>';
    echo "<td>";
    echo "<input type=\"submit\" value=\"";
    if (substr($actionskipper, 0, strpos($actionskipper, "_")) == 'confirm') {
        echo getLocalizedString("Confirm");
    } else {
        echo ucfirst(getLocalizedString("check"));
    }
    echo "\" /></td>";
    echo "</form></tr>";
    echo "<tr><th>".getLocalizedString("idplayer")."</th><th>".getLocalizedString("linktype")."</th><th>".getLocalizedString("login_id")."</th><th>".ucfirst(getLocalizedString("action"))."</tr>";
    foreach ($grantlist as $rel) {
        $p = getPlayerObject($rel['idplayers']);
        $u = getUserObject($rel['idusers']);
        echo "<tr>";
        echo "<td>".$p->htmlIdplayersPlayername()."</td>";
        echo "<td>".getLinktypeString($rel['linktype'])."</td>";
        echo "<td>".$u->htmlIdusersUsernameLink()."</td>";
        echo "<td>";
        if ($rel['linktype'] != PU_FLAG_OWNER) {
            echo htmlDelLinktype($rel);
        }
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>"; //skipperlistbox

  include_once("includes/footer.inc");
  
?>
