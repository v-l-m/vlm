<?
    require_once("base.class.php");


    class NewRacesEvents extends baseClass {
        var $timetoevent = 0;
        var $timedelta = 600;
        
        function __construct($timetoevent, $timedelta = 600) {
            $this->timetoevent = intval($timetoevent);
            $this->timedelta = intval($timedelta);
        }

        function feed() {
            $query = "SELECT * FROM races ".
                     "WHERE started = 0 ".
                     "AND racetype = 0 ".
                     "AND ABS(deptime - UNIX_TIMESTAMP(NOW()) - ".$this->timetoevent.") < ".$this->timedelta." ";
            $res = $this->queryRead($query);
            
            while ($line = mysql_fetch_assoc($res)) {
                $this->feedone($line);
            }
        }
        
        function secs_to_h($secs) {
                $units = array(
                        "week"   => 7*24*3600,
                        "day"    =>   24*3600,
                        "hour"   =>      3600,
                        "minute" =>        60,
                        "second" =>         1,
                );

      	        // specifically handle zero
                if ( $secs == 0 ) return "0 seconds";

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
            $timedelay = $this->timetoevent; #$line['deptime'] - intval(time());
            $timetarget = $line['deptime'] - $this->timetoevent;
            $t = sprintf("%s (~%s) starts in %s", $line['racename'], $line['idraces'], $this->secs_to_h($timedelay));
            $medias = explode(",", VLM_NOTIFY_LIST);
            foreach ($medias as $media) {
                $sql = sprintf("INSERT IGNORE INTO `news` SET media='%s', summary='%s', timetarget=%d ;", $media, $t, $timetarget);
                $this->queryWrite($sql);
            }
            print $t."\n";
        }

    }

    if ($_SERVER['argc'] != 3) die("Bad arguments");
    eval("\$timetoevent = intval(".$_SERVER['argv'][1].");");
    eval("\$timedelta = intval(".$_SERVER['argv'][2].");");

    print "Incoming races in ".$timetoevent."s +-".$timedelta."\n";

    
    $re = new NewRacesEvents($timetoevent, $timedelta);
    $re->feed();
    
        
?>
