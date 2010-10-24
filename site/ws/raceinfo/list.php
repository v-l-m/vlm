<?php

    include('config.php');
    require('racesiterators.class.php');
    
    class JsonRacesIterator extends RacesIterator {
        var $query = "SELECT * FROM races
                      WHERE ( ( started = 0 AND deptime > UNIX_TIMESTAMP() ) OR ( closetime > UNIX_TIMESTAMP() ) )
                      ORDER BY started ASC, deptime ASC, closetime ASC ";
        var $jsonarray;

        function listing() {
            $this->start();
            $res = wrapper_mysql_db_query_reader($this->query) or die("Query [".$this->query."] failed \n");
            while ($row = mysql_fetch_assoc($res) ) $this->onerow($row);
            $this->end();
        }

        function onerow($row) {
            $row['idraces'] = (int) $row['idraces'];
            $row['started'] = (int) $row['started'];
            $row['deptime'] = (int) $row['deptime'];
            $row['closetime'] = (int) $row['closetime'];
            $row['startlong'] = (float) $row['startlong']/1000.;
            $row['startlat'] = (float) $row['startlat']/1000.;
            $row['racetype'] = (int) $row['racetype'];
            $row['firstpcttime'] = (int) $row['firstpcttime'];
            $row['coastpenalty'] = (int) $row['coastpenalty'];
            $row['bobegin'] = (int) $row['bobegin'];
            $row['boend'] = (int) $row['boend'];
            $row['maxboats'] = (int) $row['maxboats'];
            $row['vacfreq'] = (int) $row['vacfreq'];

            $this->jsonarray[$row['idraces']] = $row;
        }
            
        function start() {
            $this->jsonarray = Array();
        }
        
        function end() {
            echo json_encode($this->jsonarray);
        }

    }

    header('Content-type: application/json; charset=UTF-8');

    new JsonRacesIterator();
?>
