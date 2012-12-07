<?
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyMail extends VlmNotify {
        var $media = "mail";
        var $rate_limit = 3;
    
        function postone($message) {
            mailInformation(VLM_NOTIFY_MAIL, $message['summary'], $message['summary'], False);
            return True;
        }
    }
        
    $identica = new VlmNotifyMail();
    $identica->fetch();
    $identica->post();
    $identica->close();
?>
