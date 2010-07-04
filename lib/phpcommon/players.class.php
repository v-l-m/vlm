<?php

include_once("functions.php");

class players {
    var $idplayers,
        $email,
        $playername,
        $update,
        $created;
          
    var $error_status = False;
    var $error_string = "";

    function players($id = 0, $email = null, $row = null) {
        if ($id !== 0) {
            $this->contructFromId($id);
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
        $this->email = $row['email'];
        //FIXME : other attributes.    function logPlayerEvent($logmsg) {
        logUserEvent($this->idusers , $this->engaged, $logmsg);
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

    function constructFromId($id) {
        $id = intval($id);
        return $this->contructFromQuery("idplayers = $id");
    }

/* *********************** HELPER - should be in a base class */

    function queryWrite($query) {
        $result = wrapper_mysql_db_query_writer($query);
        if ($result) {
            return $result;
        } else {
            $this->set_error_with_mysql_query($query)
            return False;
        }            $this->idplayers = $row['idplayers'];
    }

    function queryRead($query) {
        $result = wrapper_mysql_db_query_reader($query);
        if ($result) {
            return $result;
        } else {
            $this-> set_error_with_mysql_query($query)
            return False;
        }            
    }


    //Save error string - concat all error strings
    function set_error($error_string) {
        $this->error_status = True;
        $this->error_string .= $error_string."\n";
    }
    
    //Convenient with mysql errors
    function set_error_with_mysql_query($query) {
        $msg = "MySql error ".mysql_errno()." :".mysql_error()."\n".
               "Query was :".$query;
        $this->set_error($msg);
    }

?>
