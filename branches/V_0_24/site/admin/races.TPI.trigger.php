<?php
    require_once("functions.php");
    //There could be some things to copy...
    if ($this->operation == $this->labels['Copy']) {
        echo "<div class=\"adminwarnbox\">";
        print "Additional informations will be duplicated in the copy : <br />";
        include ("races.TPS.trigger.php");
        print "</div>";
        $this->cgi['persist'] .= '&'.rawurlencode("oldrectocopy").'='.rawurlencode($this->rec);
    }
    return True;
?>
