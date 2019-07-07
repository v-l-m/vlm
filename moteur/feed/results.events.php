<?
    require_once("base.class.php");

    class RacesResultsEvents extends baseClass {
        var $idraces = 0;
        var $timedelta = 600;
        function __construct($idraces, $timedelta = 600) {
            if (intval($idraces) < 1) die("Bad idraces");
            $this->idraces = intval($idraces);
            $this->timedelta = intval($timedelta);
            $this->now = intval(time());
        }

        function feed() {
            $query = "SELECT R.`idraces`, R.`racename`, U.`idusers`, `username`, `boatname`, RR.`deptime`, `duration` ".
                     "FROM `races_results` RR LEFT JOIN `races` R ON (R.idraces = RR.idraces) LEFT JOIN `users` U ON (RR.`idusers` = U.`idusers`) ".
                     "WHERE R.`idraces` = '".$this->idraces."' ".
                     "AND `position` = 1 ".
                     "ORDER BY `duration` ASC LIMIT 4";
            $res = $this->queryRead($query);
            
            $c = 0;
            while ($line = mysqli_fetch_assoc($res)) {
                $c += 1;
                $line['rank'] = $c;
                if ($this->now - intval($line['deptime']+$line['duration']) < $this->timedelta) $this->feedone($line);
            }
        }
        
        function secs_to_h($secs) {
                $units = array(
                        "semaine" => 7*24*3600,
                        "jour"    =>   24*3600,
                        "h"   =>      3600,
                        "min"  =>        60,
                        "sec" =>         1,
                );

      	        // specifically handle zero
                if ( $secs == 0 ) return "0 sec";

                $s = "";

                foreach ( $units as $name => $divisor ) {
                        if ( $quot = intval($secs / $divisor) ) {
                                $s .= "$quot $name";
                                $s .= (abs($quot) > 1 ? "s" : "") . ", ";
                                $secs -= $quot * $divisor;
                        }
                }
                return substr($s, 0, -2);
        }        

        function feedone($line) {
            $timetarget = intval($line['deptime']+$line['duration']);
            if ($line['boatname'] != $line['username']) $line['boatname'] = sprintf("%s - %s", $line['username'], $line['boatname']);
            switch ($line['rank']) {
                case 1 :
                    $t = sprintf("Victoire pour %s (#%s) dans %s (~%s) en %s", $line['boatname'], $line['idusers'], $line['racename'], $line['idraces'], $this->secs_to_h(intval($line['duration'])));
                    break;
                case 2 :
                    $t = sprintf("2nde marche du podium pour %s (#%d) dans %s (~%s)", $line['boatname'], $line['idusers'], $line['racename'], $line['idraces']);
                    break;
                case 3 :
                    $t = sprintf("%s (#%d) termine 3ème dans %s (~%s)", $line['boatname'], $line['idusers'], $line['racename'], $line['idraces']);
                    break;
                case 4 :
                    $t = sprintf("Accessit pour %s (#%d), 4eme dans %s (~%s)", $line['boatname'], $line['idusers'], $line['racename'], $line['idraces']);
                    break;
            }                    
                
            $medias = explode(",", VLM_NOTIFY_LIST);
            foreach ($medias as $media) {
                $sql = sprintf("INSERT IGNORE INTO `news` SET media='%s', summary='%s', timetarget=%d ;", $media, mysqli_real_escape_string($t), $timetarget);
                $this->queryWrite($sql);
            }
            print $t."\n";
        }

    }

    $query = "SELECT idraces FROM races WHERE started > 0 AND !(racetype & ".RACE_TYPE_RECORD. ") ";
    $query .= " ORDER BY idraces ASC";
    $result = wrapper_mysql_db_query_reader($query);

    while($row = mysqli_fetch_assoc($result)) {
        print "--Watching results for ".$row['idraces']."\n";
        $re = new RacesResultsEvents($row['idraces']);
        $re->feed();
    }
        
?>
