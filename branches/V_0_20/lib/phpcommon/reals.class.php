<?php

include_once("functions.php");
include_once("base.class.php");

//define("VLM_PLAYER_ADMIN", 1);


class reals extends baseClass {
    //DB attributes
    var $idreals = null;
    var $boatname = null;
    var $shortname = null;
    var $flag = null;
    var $color = null;
    var $updated = null;
    var $description = null;
    var $engaged = null;

    //computed attributes
          
    function reals($idreals = 0, $row = null) {
        if ($idreals !== 0) {
            $this->constructFromId($idreals);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }        
        
    function constructFromQuery($where) {
        $query= "SELECT * FROM users WHERE ".$where;
        $result = $this->queryRead($query);
        if ($result && mysql_num_rows($result) === 1)  {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct real object from query");
            return False;
        }
    }

    function constructFromRow($row) {
        //For now, mapping inside users class
        $this->idreals = -$row['idusers']; //Note the minus before
        $this->boatname = $row['boatname'];
        $this->shortname = trim(substr($row['boatname'], 0, 3));
        $this->engaged = $row['engaged'];
        $this->color = $row['color'];
        $this->description = $row['blocnote'];
        $this->flag = $row['country'];
        $this->updated = $row['lastchange'];
        //FIXME : et les autres attributs
        return True;
    }

    function constructFromId($id) {
        $id = intval($id);
        return $this->constructFromQuery("idusers = -$id");
    }


    //Convenient bundle
    function logRealEventError($logmsg = null, $idusers = null) {
        if (!is_null($logmsg)) $this->set_error($logmsg);
        $this->logRealEvent($this->error_string, $idusers = null);
    }

    function logRealEvent($logmsg, $idusers = null) {
//        logRealEvent($this->idreals, $idusers, null, $logmsg);
    } 


/*
    A CREER : realsprefs !


    function unsetPref($key) {
    
        $query = sprintf("DELETE FROM `players_prefs` WHERE `idreals` = %d AND `pref_name` = '%s';",
            intval($this->idreals), $key);
        if ($this->queryWrite($query)) {
            $this->logRealEvent("Player prefs(".$key.') deleted');
            //$this->prefs[$key] = $value;
            return True;
        } else {
            return False;
        }
    }          
    
    function setPref($key, $val, $perm = null) {
        //FIXME : Should not be used except from playersPrefs class ?
        if (is_null($val)) return $this->unsetPref($key);
        $query = sprintf("REPLACE `players_prefs` SET `idreals` = %d, `pref_name` = '%s', `pref_value` = '%s'",
            intval($this->idreals), $key, mysql_real_escape_string($val) );
        if (!is_null($perm)) $query .= sprintf(", `permissions` = %d", $perm);
        if ($this->queryWrite($query)) {
            $this->logRealEvent("Player prefs(".$key.') updated');
            //$this->prefs[$key] = $value;
            return True;
        } else {
            return False;
        }
    }
    
    function setPrefPerm($key, $perm) {
        $query = sprintf("UPDATE `players_prefs` SET `permissions` = %d WHERE `idreals` = %d AND `pref_name` = '%s'",
            $perm, intval($this->idreals), $key);
        if ($this->queryWrite($query)) {
            $this->logRealEvent("Player prefs(".$key.') updated');
            //$this->prefs[$key] = $value;
            return True;
        } else {
            return False;
        }
    }
    
    //getters
    function getLang($deflang = 'en') {
        $val = $this->getPref("lang_ihm");
        if (is_null($val)) return $deflang;
        return $val['pref_value'];
    }
    
    function getPref($key) {
        $query = sprintf("SELECT `pref_name`, `pref_value`, `permissions` FROM `players_prefs` WHERE `idreals` = %d AND `pref_name` = '%s'",
            intval($this->idreals), $key);
        $result = $this->queryRead($query);
        if ($result && mysql_num_rows($result) === 1)  {
            $ret = mysql_fetch_array($result, MYSQL_ASSOC);
            $ret['permissions'] = intval($ret['permissions']);
            return $ret;
        } else {
            return null;
        }
    }
    
    function getPrefGroup($prefix = "") {
        $query = sprintf("SELECT `pref_name`, `pref_value`, `permissions` FROM `players_prefs` WHERE `idreals` = %d AND `pref_name` LIKE '%s%%'",
            intval($this->idreals), $prefix);
        $result = $this->queryRead($query);
        $grouplist = array();

        if ($result) {
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                if (!is_null($row["pref_value"]) && $row["pref_value"] != "") $grouplist[$row["pref_name"]] = $row;
            }
        }
        return $grouplist;
    }
    
    */
    
}
?>
