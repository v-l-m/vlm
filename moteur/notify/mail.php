<?
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyMail extends VlmNotify {
        var $media = "mail";
        var $rate_limit = 3;

        function postone($message) {
            $text = "";
            if ($message['url'] != "") {
                $text = "Lien : ".$message['url']."\n\n";
            }
            if ($message['longstory'] != '') {
                $text .= $message['longstory'];
            } else {
                $text .= $message['summary'];
            }
            mailInformation(VLM_NOTIFY_MAIL, $message['summary'], $text, False);
            return True;
        }
    }
        
    $identica = new VlmNotifyMail();
    $identica->fetch();
    $identica->post();
    $identica->close();
?>
