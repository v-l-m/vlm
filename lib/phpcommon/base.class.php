<?php

include_once("functions.php");

abstract class baseClass {
    var $error_status = False;
    var $error_string = "";

    function __construct() {}

    function queryWrite($query) {
        $result = wrapper_mysql_db_query_writer($query);
        if ($result) {
            return $result;
        } else {
            $this->set_error_with_mysql_query($query,$GLOBALS['masterdblink']);
            return False;
        }
    }

    function queryRead($query) {
        $result = wrapper_mysql_db_query_reader($query);
        if ($result) {
            return $result;
        } else {
            $this->set_error_with_mysql_query($query);
            return False;
        }            
    }

    //Save error string - concat all error strings
    function set_error($error_string) {
        $this->error_status = True;
        $this->error_string .= $error_string."\n";
    }
    
    //Convenient with mysql errors
    function set_error_with_mysql_query($query,$connexion =null) 
    {
      if (!$connexion)
      {
        $connexion=$GLOBALS['slavedblink'];
      }
      $msg = "MySql error ".mysqli_errno($connexion)." :".mysqli_error($connexion)."\n".
              "Query was :".$query;
      $this->set_error($msg);
    }
}

?>
