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

    function playersPending($idplayers_pending = 0, $seed = 0, $row = null) {
        if ($idplayers_pending !== 0 && $seed !== 0) {
            $this->contructFromIdSeed($idplayers_pending, $seed);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }

    function constructFromRow($row) {
        $this->idplayers = $row['idplayers'];
        $this->playername = $row['playername'];
        $this->password = $row['password'];
        $this->email = $row['email'];
        $this->seed = $row['seed'];
    }

    function constructFromIdSeed($id, $seed) {
        $id = intval($id);
        $seed = intval($seed);
        return $this->constructFromQuery("idplayers_pending = $id and seed = $seed");
    }

    function constructFromQuery($where) {
        $query= "SELECT * FROM players_pending WHERE $where";
        if ($result = $this->queryRead($query) && mysql_num_rows($result) === 1)  {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct player object from query");
            return False;
        }
    }

    function insert() {
        $query = sprintf("INSERT INTO `players_pending` %s SET `email`='%s', `password`='%s', `playername`='%s', `seed`=%d",
            $this->email,
            $this->password,
            $this->playername,
            $this->seed
            );
        return $this->queryWrite($query);
    }
    
    function delete() {
        $query = sprintf("DELETE players_pending WHERE idplayers_pending = %d", $this->idplayers_pending);
        return $this->queryWrite($query);
    }

    function validate($id, $seed) {
        if (!$this->contructFromIdSeed($id, $seed)) return False;
        $players = new players();
        $players->email = $this->email;
        $players->playername = $this->playername;
        $players->password = $this->password;
        if (!$players->insert()) return False;
        return $this->delete();
    } 
            
    //setters
    function setPassword($password) {
        $this->password = hash('sha256', $password);
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
        } else if (!is_null($email)) {
            $this->constructFromEmail($email);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }        
        
    function constructFromQuery($where) {
        $query= "SELECT * FROM players WHERE ".$where;
        if ($result = $this->queryRead($query) &&  mysql_num_rows($result) === 1)  {
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
        return $this->contructFromQuery("idplayers = $id");
    }

    function constructFromEmail($email) {
        return $this->contructFromQuery("email = $email");
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
        $query = sprintf("SELECT * FROM players WHERE `email` = '%s'", $this->email);
        $result = $this->queryRead($query);
        if (!($result && mysql_num_rows($result) === 0)) $this->set_error("Your email is already in use.");            
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
}

?>
