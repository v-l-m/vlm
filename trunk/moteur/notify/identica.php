<?

    require('notify.class.php');
    
    class VlmNotifyIdentica extends VlmNotifyCurl {
        var $media = "identica";
        var $rate_limit = 10;
    
        function init_handle($message) {
            return $this->init_curl_handle_from_url(
                VLM_NOTIFY_IDENTICA_URL,
                Array("status" => $message),
                Array(CURLOPT_USERPWD => VLM_NOTIFY_IDENTICA_USERPWD)
                );
        }
    }
    
    $identica = new VlmNotifyIdentica();
    $identica->fetch();
    //$messages = Array('test message 1', "test deuxième");
    //$identica->messages = $messages;
    $identica->post();
    $identica->close();
?>
