<?
    require_once("base.class.php");
/*
  CREATE TABLE `modules_status` (
  `autoid` bigint(20) NOT NULL AUTO_INCREMENT,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `serverid` varchar(50) DEFAULT NULL,
  `moduleid` varchar(50) DEFAULT NULL,
  `revid` int(11) DEFAULT NULL,
*/

    class NewModulesEvents extends baseClass {
        var $timedelta = 60;
        var $media = 'irc';

        function __construct($media, $timedelta = 60) {
            $this->timedelta = intval($timedelta);
            $this->media = $media;
        }

        function feed() {
            $query = "SELECT * FROM modules_status ".
                     "WHERE ABS(UNIX_TIMESTAMP(updated) - UNIX_TIMESTAMP(NOW()) ) < ".$this->timedelta." ;";

            $res = $this->queryRead($query);

            while ($line = mysql_fetch_assoc($res)) {
                $this->feedone($line);
            }
        }

        function feedone($line) {
            $t = sprintf("Deploiement du module VLM %s (r%s) sur %s", $line['moduleid'], $line['revid'], $line['serverid']);
            $sql = sprintf("INSERT IGNORE INTO `news` SET media='%s', summary='%s', timetarget=UNIX_TIMESTAMP(NOW()) ;", $this->media, $t);
            $this->queryWrite($sql);
            print $t."\n";
        }

    }

    if ($_SERVER['argc'] != 3) die("Bad arguments");
    eval("\$media = \"".$_SERVER['argv'][1]."\";");
    eval("\$timedelta = intval(".$_SERVER['argv'][2].");");

    print "Updates for modules to $media ($timedelta)\n";

    $re = new NewModulesEvents($media, $timedelta);
    $re->feed();

?>
