<?
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyFacebook extends VlmNotifyCurl {
        var $media = "facebook";
        var $rate_limit = 2;
    
        function init_handle($message) {
            return $this->init_curl_handle_from_url(
                VLM_NOTIFY_FACEBOOK_URL,
                Array(
                  "message" => $message['summary'],
                  "access_token" => VLM_NOTIFY_FACEBOOK_ACCESSTOKEN
                  )
                );
        }
    }
    
    
    $facebook = new VlmNotifyFacebook();
    $facebook->fetch();
    $facebook->post();
    $facebook->close();
?>
