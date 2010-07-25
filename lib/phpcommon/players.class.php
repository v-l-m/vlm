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
        $query = sprintf("DELETE FROM players_pending WHERE email = '%s' AND seed = %d", $this->email, $this->seed);
        return $this->queryWrite($query);
    }

    function mailValidationMessage() {
        $message = "You have requested to create an account on VLM.\nPlease, click on the link below or copy/paste it in your browser.\n";
        $message .= "http://".$_SERVER['SERVER_NAME']."/create_player.php?createplayer=validate&seed=".$this->seed."&emailid=".urlencode($this->email)."\n";
        $message .= "Thanks for playing VLM";
         $headers = 'From: '.EMAIL_COMITE_VLM. "\r\n" .
         'Reply-To: '.EMAIL_COMITE_VLM. "\r\n" .
         'X-Mailer: PHP/' . phpversion();
        $res = mail($this->email , "VLM Validate your account" , $message, $headers);
        return $res;
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
    var $boatlist = null;
          
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
        return $this->queryWrite($query);
    }

    function update() {
        $query = sprintf("UPDATE `players` %s WHERE `email` = '%s' AND `idplayers` = %d",
            $this->query_addupdate(),
            $this->email,
            intval($this->idplayers)
            );
        return $this->queryWrite($query);
    }

    function checkNonconformity() {
        $pattern = "/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i";
        if (preg_match($pattern  ,  $this->email) < 1) {
            $this->set_error("Your email address doesn't seem to be valid");
            return False;
        }
        $query = sprintf("SELECT * FROM players WHERE `email` = '%s'", $this->email);
        $result = $this->queryRead($query);
        if (!($result && mysql_num_rows($result) === 0)) $this->set_error(getLocalizedString("Your email is already in use."));
        return $this->error_status;
    }

    //Convenient bundle
    function logPlayerEventError($logmsg = null) {
        if (!is_null($logmsg)) $this->set_error($logmsg);
        $this->logPlayerEvent($this->error_string);
    }

    function logPlayerEvent($logmsg) {
        //FIXME : Do nothing for now
        return True;
    } 

    //setters
    function setPassword($password) {
        $this->password = hash('sha256', $password);
    }

    //getters
    function getBoatlist() {
        if (!is_null($this->boatlist)) return $this->boatlist;
        $boatlist = Array();
        $query = "SELECT idusers FROM playerstousers WHERE idplayers = ".$this->idplayers." AND linktype = ".PU_FLAG_OWNER;
        if ($res = $this->queryRead($query)) {
            while ($row = mysql_fetch_assoc($res)) {
                $boatlist[$row['idusers']] = new users($row['idusers']);
                //FIXME : check result ?
            }
            $this->boatlist = $boatlist;
        }
        return $this->boatlist;
    }
    
    //html renderers
    function htmlPlayername() {
        $ret  = "<a href=\"palmares.php?type=player&amp;idplayers=";
        $ret .= $this->idplayers;
        $ret .= "\">".$this->playername."</a>";
        return $ret;
    }
    
    function htmlBoatlist() {
        $boatlist = $this->getBoatlist();
        $ret = "<ul>";
        foreach ($boatlist as $id => $user) {
            $ret .= "<li>".$user->htmlIdusersUsernameLink(getCurrentLang())."</li>";
        }
        $ret .= "</ul>";
        return $ret;
    }
}

?>
