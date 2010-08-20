<?php

include_once("functions.php");
include_once("base.class.php");

class playersPending extends baseClass {
    var $idplayers_pending,
        $email,
        $password,
        $playername,
        $updated,        
        $seed;

    function playersPending($email = 0, $seed = 0, $row = null) {
        if (!is_null($email) && $seed !== 0) {
            $this->constructFromEmailSeed($email, $seed);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }

    function checkNonconformity() {
        $players = new players(0, null, $this);
        $players->checkNonconformity();
        $this->error_string = $players->error_string;
        return $players->error_status;
    }

    function constructFromRow($row) {
        $this->idplayers_pending = $row['idplayers_pending'];
        $this->playername = $row['playername'];
        $this->password = $row['password'];
        $this->email = $row['email'];
        $this->seed = $row['seed'];
        return True;
    }

    function constructFromEmailSeed($email, $seed) {
        $seed = intval($seed);
        return $this->constructFromQuery("email = '$email' AND seed = $seed");
    }

    function constructFromQuery($where) {
        $query= "SELECT * FROM players_pending WHERE $where";
        $result = $this->queryRead($query);
        if ($result && mysql_num_rows($result) === 1)  {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct player_pending object from query");
            return False;
        }
    }

    function insert() {
        $query = sprintf("INSERT INTO `players_pending` SET `email`='%s', `password`='%s', `playername`='%s', `seed`=%d",
            $this->email,
            $this->password,
            $this->playername,
            $this->seed
            );
        return $this->queryWrite($query);
    }
    
    function delete() {
        //NB: on efface tous les pending avec le même email (quelque soit le seed)
        $query = sprintf("DELETE FROM players_pending WHERE email = '%s'", $this->email);
        return $this->queryWrite($query);
    }

    function mailValidationMessage() {
        $message  = getLocalizedString("Welcome into Virtual Loup de Mer !")."\n";
        $message  = getLocalizedString("You have requested to create an account on VLM.\nPlease, click on the link below or copy/paste it in your browser.")."\n";
        $message .= "http://".$_SERVER['HTTP_HOST']."/create_player.php?createplayer=validate&seed=".$this->seed."&emailid=".urlencode($this->email)."\n";
        return mailInformation($this->email, getLocalizedString("Validate your account"), $message);
    }

    function validate() {
        if (!$this->constructFromEmailSeed($this->email, $this->seed)) return False;
        $players = new players(0, null, $this);
        if ($players->error_status) {
            $this->set_error($players->error_string);
        }
        return !$this->error_status;
    }
    
    function create() {
        if (!$this->validate()) return False;
        $players = new players(0, null, $this);        
        $players->insert();
        if ($players->error_status) {
            $this->set_error($players->error_string);
            return !$this->error_status;
        }
        $this->delete();
        return !$this->error_status;
    } 
            
    //setters
    function setPassword($password) {
        $this->password = hash('sha256', $password);
    }
    
    function setSeed() {
        $this->seed = rand();
    }

    function dump() {
        $dump = sprintf("`email`='%s', `password`='%s', `playername`='%s', `seed`=%d",
            $this->email,
            $this->password,
            $this->playername,
            $this->seed
            );
        return $dump;
    }
}

class players extends baseClass {
    var $idplayers,
        $email,
        $password,
        $playername,
        $permissions,
        $updated,
        $created;
    var $boatsitidlist = null;
    var $ownedboatidlist = null;
    var $recentlyboatsittedidlist = null;
          
    function players($idplayers = 0, $email = null, $pending = null, $row = null) {
        if ($idplayers !== 0) {
            $this->constructFromId($idplayers);
        } else if (!is_null($email)) {
            $this->constructFromEmail($email);
        } else if (!is_null($pending)) {
            $this->constructFromPending($pending);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }        
        
    function constructFromQuery($where) {
        $query= "SELECT * FROM players WHERE ".$where;
        $result = $this->queryRead($query);
        if ($result && mysql_num_rows($result) === 1)  {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct player object from query");
            return False;
        }
    }

    function constructFromRow($row) {
        $this->idplayers = $row['idplayers'];
        $this->email = $row['email'];
        $this->password = $row['password'];
        $this->playername = $row['playername'];
//        $this->permissions = $row['permissions'];
//        $this->description = $row['description'];
        //FIXME : et les autres attributs
        return True;
    }

    function constructFromId($id) {
        $id = intval($id);
        return $this->constructFromQuery("idplayers = $id");
    }

    function constructFromEmail($email) {
        return $this->constructFromQuery("email = '$email'");
    }

    function constructFromPending($pending) {
        $this->email = $pending->email;
        $this->playername = $pending->playername;
        $this->password = $pending->password;
    }

    function checkPassword($password) {
        return hash("sha256", $password) === $this->password;
    }

    function query_addupdate() {
        $query = sprintf("SET `email`='%s', `password`='%s', `playername`='%s'",
            $this->email,
            $this->password,
            $this->playername
            );
        return $query;
    }
    
    function insert() {
        $query = sprintf("INSERT INTO `players` %s", $this->query_addupdate());
        if (!$this->queryWrite($query)) return False;
        $this->idplayers = mysql_insert_id($GLOBALS['masterdblink']);
        $this->logPlayerEvent("Player created.");
        return True;
    }

    function update() {
        $query = sprintf("UPDATE `players` %s WHERE `email` = '%s' AND `idplayers` = %d",
            $this->query_addupdate(),
            $this->email,
            intval($this->idplayers)
            );
        $this->logPlayerEvent("Player updated.");
        return $this->queryWrite($query);
    }

    function modifyPassword($password) {
        $this->setPassword($password);
        if ($this->update()) {
            $this->logPlayerEvent("Password modified");
            $this->mailInformation("Your password has been updated");
            return True;
        } 
        return False;
    }

    function mailInformation($title, $message = null) {
        //wrapper
        return mailInformation($this->email, $title, $message);
    }


    function checkNonconformity() {
        $pattern = "/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i";
        if (preg_match($pattern  ,  $this->email) < 1) {
            $this->set_error("Your email address doesn't seem to be valid");
            return False;
        }
        $query = sprintf("SELECT * FROM players WHERE `email` = '%s'", $this->email);
        $result = $this->queryRead($query);
        
        if (strlen($this->playername) < 4) $this->set_error("Your playername should have at least 4 characters.");
        
        if (!($result && mysql_num_rows($result) === 0)) $this->set_error("Your email is already in use.");
        $query = sprintf("SELECT * FROM players WHERE `playername` = '%s'", $this->playername);
        $result = $this->queryRead($query);
        if (!($result && mysql_num_rows($result) === 0)) $this->set_error("Your playername is already in use.");

        return $this->error_status;
    }

    //Convenient bundle
    function logPlayerEventError($logmsg = null, $idusers = null) {
        if (!is_null($logmsg)) $this->set_error($logmsg);
        $this->logPlayerEvent($this->error_string, $idusers = null);
    }

    function logPlayerEvent($logmsg, $idusers = null) {
        logPlayerEvent($this->idplayers, $idusers, null, $logmsg);
    } 

    //setters
    function setPassword($password) {
        $this->password = hash('sha256', $password);
    }

    //getters
    function getManageableBoatIdList() {
        return array_unique(array_merge($this->getOwnedBoatIdList(), $this->getBoatsitIdList()));
    }
    
    function getOwnedBoatIdList() {
        if (!is_null($this->ownedboatidlist)) return $this->ownedboatidlist;
        $this->ownedboatidlist = $this->getBoatIdList("linktype =".PU_FLAG_OWNER);
        return $this->ownedboatidlist;
    }

    function getBoatRecentlyBoatsittedIdList() {
        if (!is_null($this->recentlyboatsittedidlist)) return $this->recentlyboatsittedidlist;
        $boatidlist = Array();
        //FIXME : optimiser la requête ?
        $query  = "SELECT DISTINCT `idusers` FROM `user_action` WHERE `idplayers` = ".$this->idplayers." AND idusers NOT IN (SELECT DISTINCT idusers FROM playerstousers WHERE idplayers = ".$this->idplayers." AND linktype = ".PU_FLAG_OWNER." )";
        if ($res = $this->queryRead($query)) {
            while ($row = mysql_fetch_assoc($res)) {
                $boatidlist[$row['idusers']] = $row['idusers'];
                //FIXME : check result ?
            }
        }
        $this->recentlyboatsittedidlist = $boatidlist;
        return $this->recentlyboatsittedidlist;
    }

    function getBoatsitIdList() {
        if (!is_null($this->boatsitidlist)) return $this->boatsitidlist;
        $this->boatsitidlist = $this->getBoatIdList("linktype = ".PU_FLAG_BOATSIT);
        return $this->boatsitidlist;
    }


    function getDefaultBoat() {
        $boatlist = array_merge($this->getOwnedBoatIdList(), $this->getBoatsitIdList());
        //Fixme : should be configurable, and should select a racing boat
        if (count($boatlist) > 0) {
            return $boatlist[0];
        } else {
            return 0;
        }
    }

    function getBoatIdList($linkfilter) {
        $boatidlist = Array();
        $query = "SELECT DISTINCT `idusers` FROM `playerstousers` WHERE `idplayers` = ".$this->idplayers." AND ".$linkfilter;
        if ($res = $this->queryRead($query)) {
            while ($row = mysql_fetch_assoc($res)) {
                $boatidlist[$row['idusers']] = $row['idusers'];
                //FIXME : check result ?
            }
        }
        return $boatidlist;
    }
    
    function getGrantedBoatList() {
        $boatlist = Array();
        $query = "SELECT PU.`idusers`, PU.`idplayers`, PU.`linktype` "
                ."FROM `playerstousers` as PU "
                ."WHERE PU.`idusers` IN ("
                ."SELECT DISTINCT PO.`idusers` FROM `playerstousers` as PO WHERE PO.`idplayers` = ".$this->idplayers." AND PO.linktype = ".PU_FLAG_OWNER
                .") ORDER BY PU.`idusers`, PU.`linktype`, PU.`idplayers`";
        if ($res = $this->queryRead($query)) {
            while ($row = mysql_fetch_assoc($res)) {
                $boatlist[] = $row;
                //FIXME : check result ?
            }
        }
        return $boatlist;
    }


    function getBoatCandidatesList() {
        $boatidlist = Array();
        $query = "SELECT DISTINCT MAIN.`idusers` FROM `users` as MAIN WHERE `email` LIKE '%".$this->email
                ."%' AND MAIN.`idusers` NOT IN (SELECT DISTINCT `idusers` FROM `playerstousers`)";
        if ($res = $this->queryRead($query)) {
            while ($row = mysql_fetch_assoc($res)) {
                $boatidlist[] = $row['idusers'];
                //FIXME : check result ?
            }
        }
        return $boatidlist;
    }

    //html renderers
    function htmlPlayername() {
        $ret  = "<a href=\"palmares.php?type=player&amp;idplayers=";
        $ret .= $this->idplayers;
        $ret .= "\">".$this->playername."</a>";
        return $ret;
    }

    function htmlIdplayersPlayername() {
        $ret  = "<a href=\"palmares.php?type=player&amp;idplayers=";
        $ret .= $this->idplayers;
        $ret .= "\">(@".$this->idplayers.")&nbsp;".$this->playername."</a>";
        return $ret;
    }

    function htmlBoatOwnedList() {
        return $this->htmlBoatlist($this->getOwnedBoatIdList());
    }

    function htmlBoatManageableList() {
        return $this->htmlBoatlist($this->getManageableBoatIdList());
    }
    
    function htmlBoatlist($boatlist) {
        $ret = "<ul>";
        foreach ($boatlist as $id) {
            $user = getUserObject($id);
            if (is_null($user)) continue;
            $ret .= "<li>".$user->htmlIdusersUsernameLink()."&nbsp;";
            if (!in_array($user->idusers, $this->getOwnedBoatIdList())) $ret .= "(".getLocalizedString("as a boatsitter").")&nbsp;";
            $ret .= "-&nbsp;";

            if ($user->engaged > 0) {
                $raceobj = new races($user->engaged);
                $ret .= sprintf( getLocalizedString('boatengaged'), $raceobj->htmlRacenameLink(), $raceobj->htmlIdracesLink() );
            } else {
                $ret .= getLocalizedString('boatnotengaged');
            }
            $ret .= "</li>";
            
        }
        $ret .= "</ul>";
        return $ret;
    }

    function htmlBoatCandidatesList() {
        $listcandidate = $this->getBoatCandidatesList();
        $ret = "";
        
        if (count($listcandidate) > 0) {
            $ret .= "<h2>".getLocalizedString("These boats are maybe yours ?")."</h2>";
            $ret .= "<ul>\n";
            foreach ($listcandidate as $id) {
                $user = getUserObject($id);
                $ret .= "<li>".$user->htmlIdusersUsernameLink();
                $ret .= "&nbsp;<a href=\"attach_owner.php?boatpseudo=".$user->username."\">(".getLocalizedString("Attachment to this account").")</a>";
                $ret .= "</li>";
            }
            $ret .= "</ul><hr />";
        }
        return $ret;
    }

    function htmlBoatRecentlyBoatsittedList() {
        $list = $this->getBoatRecentlyBoatsittedIdList();
        return $this->htmlBoatlist($list);
    }

}

?>
