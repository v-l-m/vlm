<?php

include_once("functions.php");
include_once("base.class.php");

class racesgroups extends baseClass {
    //DB attributes
    var $grouptag = null;
    var $groupname = null;
    var $grouptitle = null;
    var $updated = null;
    var $description = null;
    var $admincomments = null;

    //computed attributes
          
    function __construct($grouptag, $row = null) {
        if (!is_null($grouptag)) {
            $this->constructFromId($grouptag);
        } else if (!is_null($row) && is_array($row)) {
            $this->constructFromRow($row);
        }
    }        
        
    function constructFromQuery($where) {
        $query= "SELECT * FROM racesgroups WHERE ".$where;
        $result = $this->queryRead($query);
        if ($result && mysqli_num_rows($result) === 1)  {
            $row =  mysqli_fetch_array($result, MYSQLI_ASSOC);
            return $this->constructFromRow($row);
        } else {
            $this->set_error("FAILED : Construct racesgroups object from query");
            return False;
        }
    }

    function constructFromRow($row) {
        //For now, mapping inside users class
        $this->grouptag = $row['grouptag']; //Note the minus before
        $this->groupname = $row['groupname'];
        $this->grouptitle = $row['grouptitle'];
        $this->description = $row['description'];
        $this->admincomments = $row['admincomments'];
        $this->updated = $row['updated'];
        return True;
    }

    function constructFromId($id) {
        return $this->constructFromQuery("grouptag = '".mysqli_real_escape_string($GLOBALS['slavedblink'], $id)."'");
    }

    function getRaces($where = null) {
        $raceslist = Array();
        $query = "SELECT R.`idraces`, racename, started, deptime, startlong, startlat, boattype, closetime, racetype ".
                 "FROM racesgroups AS RRG LEFT JOIN racestogroups AS RG ON (RRG.grouptag = RG.grouptag) LEFT JOIN races as R ON (R.idraces = RG.idraces) WHERE RG.`grouptag` = '".$this->grouptag."'";
        if (!is_null($where)) $query = $query." ".$where;
        $result = $this->queryRead($query);
        if ($result) {
            while($row =  mysqli_fetch_array($result, MYSQLI_ASSOC)) $raceslist[$row['idraces']] = $row;
        } 
        return $raceslist;
    }
    
    function getCurrentRaces() {
        return $this->getRaces("started != 1");
    }

    function htmlGrouptagLink() {
        return "<a href=\"/racesgroups.php?grouptag=".$this->grouptag."\">!".$this->grouptag."</a>";
    }

    function htmlSummary() {
        $ret = "";
        
        $ret .= "<div>";
        $ret .= "<h2>".$this->grouptitle."</h2>";
        $ret .= "<ul>";
        $ret .= "<li>".getLocalizedString("Group Title")."&nbsp;:&nbsp;".$this->grouptitle."</li>";
        $ret .= "<li>".getLocalizedString("Group Tag")."&nbsp;:&nbsp;".$this->htmlGrouptagLink()."</li>";
        $ret .= "<li>".getLocalizedString("Group Name")."&nbsp;:&nbsp;".$this->groupname."</li>";
        $ret .= "<li>".getLocalizedString("Group Description")."&nbsp;:&nbsp;".$this->description."</li>";

        $ret .= "</ul>";
        $ret .= "</div>";
        
        return $ret;
    }  
}


?>
