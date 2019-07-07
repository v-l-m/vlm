<?php

    include('config.php');
    require('racesiterators.class.php');
    
    class JsonRacesGroupsIterator extends RacesIterator {
        //FIXME: Abusive use of RacesIterator subclassing
        var $query = "SELECT * FROM racesgroups
                      ORDER BY updated DESC";
        var $jsonarray;

        function listing() {
            $this->start();
            $res = wrapper_mysql_db_query_reader($this->query) or die("Query [".$this->query."] failed \n");
            while ($row = mysqli_fetch_assoc($res) ) $this->onerow($row);
            $this->end();
        }

        function onerow($row) {
//            $row['idracesgroups'] = (int) $row['idracesgroups'];
            $this->jsonarray[$row['grouptag']]['grouptag'] = $row['grouptag'];
            $this->jsonarray[$row['grouptag']]['groupname'] = $row['groupname'];
            $this->jsonarray[$row['grouptag']]['grouptitle'] = $row['grouptitle'];
            $this->jsonarray[$row['grouptag']]['description'] = $row['description'];
            $this->jsonarray[$row['grouptag']]['updated'] = $row['updated'];
        }
            
        function start() {
            $this->jsonarray = Array();
        }
        
        function end() {
            echo json_encode($this->jsonarray);
        }

    }

    header('Content-type: application/json; charset=UTF-8');
    header("Cache-Control: max-age=". (24*3600) .", must-revalidate");

    new JsonRacesGroupsIterator();
?>
