<?php
    include_once("wslib.php");

    class WSStrings extends WSBase {
        var $lang = null;
        var $stringarray = null;

        function __construct() {
            parent::__construct();
            $this->lang = $this->check_cgi('lang', 'CORE01', getCurrentLang());
            include($_SERVER['DOCUMENT_ROOT']."/includes/strings.inc");
            $this->stringarray = $strings;
         }
    }

    $ws = new WSStrings();
    $ws->maxage = 3600*24;

    $ws->answer['request'] = Array('lang' => $ws->lang);
    if (!array_key_exists($ws->lang, $ws->stringarray)) 
    {
        $ws->answser['warn'] = "Requested lang doesn't exists in strings file";
        // Default to en if unknown or wrong language specified
        $ws->lang='en';
    }
    $ws->answer['strings'] = $ws->stringarray[$ws->lang];
    $ws->reply_with_success();
?>
