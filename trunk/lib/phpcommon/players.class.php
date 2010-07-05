<?php

include_once("functions.php");
include_once("baseClass");

class playersPending extends baseClass {
    var $idplayers_pending,
        $email,
        $password,
        $playername,
        $updated,        
        $seed;

    function playersPending($idplayers_pending = 0, $seed = 0, $row = null) {
        if ($idplayers_pending !== 0 && $seed !== 0) {
            $this->contructFromIdSeed($idplayers, $seed);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
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
          
    function players($idplayers = 0, $email = null, $row = null) {
        if ($idplayers !== 0) {
            $this->contructFromId($idplayers);
        } else if (!is_null($email) {
            $this->constructFromEmail($email);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }        
        
    function constructFromQuery($where) {
        $query= "SELECT * FROM players ".$where;
        if ($result = $this->queryRead($query) {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct player object from query");
            return False;
        }

    function constructFromRow($row) {
        $this->idplayers = $row['idplayers'];
        $this->playername = $row['playername'];
        $this->password = $row['password'];
        $this->email = $row['email'];
        $this->permissions = $row['permissions'];
        $this->description = $row['description'];
        //FIXME : et les autres attributs
    }

    function constructFromId($id) {
        $id = intval($id);
        return $this->contructFromQuery("idplayers = $id");
    }
    
    //Convenient bundle
    function logPlayerEventError($logmsg = null) {
        if (!is_null($logmsg)) $this->set_error($logmsg);
        $this->logPlayerEvent($this->error_string);
    }

    function logPlayerEvent($logmsg) {
        //FIXME : Do nothing for now
        return True
    } 

    //setters
    function setPassword($password) {
        $this->password = hash('sha256', $password);
    }
}

?>
