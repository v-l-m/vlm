<?php
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyTest extends VlmNotify {
        var $media = "test";
        var $rate_limit = 2;
    
        function postone($message) {
            print "ECHO : ${message['summary']}\n";
            return True;
        }
    }
        
    $identica = new VlmNotifyTest();
    $identica->fetch();
    $identica->post();
    $identica->close();
?>
