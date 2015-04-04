<?
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyFacebook extends VlmNotifyCurl {
        var $media = "facebook";
        var $rate_limit = 2;
        var $sleep = 1;
    
        function init_handle($message) {
            if ($message['longstory'] != '') {
                $text = $message['longstory'];
            } else {
                $text = $message['summary'];
            }
            $param = Array(
                  "message" => $text,
                  "access_token" => VLM_NOTIFY_FACEBOOK_ACCESSTOKEN
                  );
            if ($message['url'] != "") {
                $param['link'] = $message['url'];
            }
            return $this->init_curl_handle_from_url(
                VLM_NOTIFY_FACEBOOK_URL,
                $param
                );
        }
    }
    
    
    $facebook = new VlmNotifyFacebook();
    $facebook->fetch();
    $facebook->post();
    $facebook->close();
?>
