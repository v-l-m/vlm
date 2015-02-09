<?php
    require_once('functions.php');
    require_once('players.class.php');

    abstract class PlayersIterator {
        var $query = "SELECT * FROM players";
        var $fields = Array('idplayers' => '@id', 'playername' => 'playername'); //, 'created' => 'created');

        function __construct() {
            $this->listing();
        }

        function listing() {
            $this->start();
            $res = wrapper_mysql_db_query_reader($this->query) or die("Query [".$this->query."] failed \n");
            while ($row = mysql_fetch_assoc($res) ) $this->onerow($row);
            $this->end();
        }

        abstract function onerow($row);
        abstract function start();
        abstract function end();
    }

    class PlayersHtmlList extends PlayersIterator {
        function __construct() {
            $this->query = "SELECT players.*, count(*) as nboats FROM players ".
                     "LEFT JOIN playerstousers ON players.idplayers = playerstousers.idplayers ".
                     "WHERE linktype = ".PU_FLAG_OWNER." ".
                     "GROUP BY players.idplayers ".
                     "HAVING count(*) > 0";
            $this->fields = Array('idplayers' => '@id', 'playername' => 'playername', 'nboats' => 'nboats'); //, 'created' => 'created');
            parent::__construct();
        }
    
        function start() {
            echo "<table class=\"playerlist\">";
            echo "<tr>";
            foreach ($this->fields as $v) {
                echo "<th>".$v."</th>";
            }
            echo "</tr>";
        }
        
        function onerow($row) {
            echo "<tr>";
            foreach ($row as $k => $v) {
                if (array_key_exists($k, $this->fields)) {
                    echo "<td>";
                    if (method_exists($this, "onefield_".$k)) {
                        echo call_user_func(array(&$this, "onefield_$k"), $v, $row);
                    } else {
                        echo $v;
                    } 
                    echo "</td>";
                }
            }
            echo "<tr>";
        }

        function end() {
            echo "</table>";
        }

        function onefield_playername($v, $row) {
            echo htmlPlayername($row['idplayers'], $v);
        }
    }

?>
