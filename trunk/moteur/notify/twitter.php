<?
    require("config.php");
    require_once('notify.class.php');
    require_once('TwitterOAuth/TwitterOAuth.php');
    require_once('TwitterOAuth/Exception/TwitterException.php');

    use TwitterOAuth\TwitterOAuth;
    date_default_timezone_set('UTC');
    
    class VlmNotifyTwitter extends VlmNotify {
        var $media = "twitter";
        var $rate_limit = 1;
        var $handle = null;
        var $config = array(
            'consumer_key' => VLM_NOTIFY_TWITTER_CONSUMER_KEY,
            'consumer_secret' => VLM_NOTIFY_TWITTER_CONSUMER_SECRET,
            'oauth_token' => VLM_NOTIFY_TWITTER_OAUTH_TOKEN,
            'oauth_token_secret' => VLM_NOTIFY_TWITTER_OAUTH_TOKEN_SECRET,
            'output_format' => 'object'
        );

        function __construct() {
            parent::__construct();
            //create the twitter handle
            $this->handle = new TwitterOAuth($config);
        }
        
        function postone($m) {
            $response = $this->handle->post('statuses/update', array('status' => $m));
            return $response;
        }
                        
    }    
    
    $twitter = new VlmNotifyTwitter();
    $twitter->fetch();
    $twitter->post();
    $twitter->close();
?>
