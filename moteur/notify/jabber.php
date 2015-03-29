<?
    require("config.php");
    require('notify.class.php');
    //require 'vendor/autoload.php';
    //We don't want to use Composer for now, so loading by hand
    require 'Psr/Log/LoggerInterface.php';
    require 'Psr/Log/LogLevel.php';
    require 'Monolog/Formatter/FormatterInterface.php';
    require 'Monolog/Formatter/NormalizerFormatter.php';
    require 'Monolog/Formatter/LineFormatter.php';
    require 'Monolog/Logger.php';
    require 'Monolog/Handler/HandlerInterface.php';
    require 'Monolog/Handler/AbstractHandler.php';
    require 'Monolog/Handler/AbstractProcessingHandler.php';
    require 'Monolog/Handler/StreamHandler.php';
    require 'xmppphp/src/OptionsAwareInterface.php';
    require 'xmppphp/src/Event/EventManagerAwareInterface.php';
    require 'xmppphp/src/Protocol/ImplementationInterface.php';
    require 'xmppphp/src/EventListener/EventListenerInterface.php';
    require 'xmppphp/src/EventListener/AbstractEventListener.php';
    require 'xmppphp/src/EventListener/BlockingEventListenerInterface.php';
    require 'xmppphp/src/EventListener/Stream/Stream.php';
    require 'xmppphp/src/EventListener/Stream/StreamError.php';
    require 'xmppphp/src/EventListener/Stream/StartTls.php';
    require 'xmppphp/src/EventListener/Stream/Authentication/AuthenticationInterface.php';
    require 'xmppphp/src/EventListener/Stream/Authentication/Plain.php';
    require 'xmppphp/src/EventListener/Stream/Authentication.php';
    require 'xmppphp/src/EventListener/Stream/AbstractSessionEvent.php';
    require 'xmppphp/src/EventListener/Stream/Bind.php';
    require 'xmppphp/src/EventListener/Stream/Session.php';
    require 'xmppphp/src/EventListener/Stream/Roster.php';
    require 'xmppphp/src/EventListener/Logger.php';
    require 'xmppphp/src/Protocol/DefaultImplementation.php';
    require 'xmppphp/src/Options.php';
    require 'xmppphp/src/Connection/ConnectionInterface.php';
    require 'xmppphp/src/Event/EventInterface.php';
    require 'xmppphp/src/Event/Event.php';
    require 'xmppphp/src/Event/XMLEventInterface.php';
    require 'xmppphp/src/Event/XMLEvent.php';
    require 'xmppphp/src/Stream/XMLStream.php';
    require 'xmppphp/src/Connection/AbstractConnection.php';
    require 'xmppphp/src/Connection/SocketConnectionInterface.php';
    require 'xmppphp/src/Util/ErrorHandler.php';
    require 'xmppphp/src/Util/XML.php';
    require 'xmppphp/src/Stream/SocketClient.php';
    require 'xmppphp/src/Connection/Socket.php';
    require 'xmppphp/src/Event/EventManagerInterface.php';
    require 'xmppphp/src/Event/EventManager.php';
    require 'xmppphp/src/Client.php';
    require 'xmppphp/src/Protocol/ProtocolImplementationInterface.php';
    require 'xmppphp/src/Protocol/Presence.php';
    require 'xmppphp/src/Protocol/Message.php';

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Fabiang\Xmpp\Options;
    use Fabiang\Xmpp\Client;

    use Fabiang\Xmpp\Protocol\Roster;
    use Fabiang\Xmpp\Protocol\Presence;
    use Fabiang\Xmpp\Protocol\Message;
    date_default_timezone_set('UTC');
    
    class VlmNotifyJabber extends VlmNotify {
        var $media = "jabber";
        var $rate_limit = 10;
        var $logger = null;
        var $client = null;
        var $config = array(
            'hostname' => VLM_XMPP_HOST,
            'port' => 5222,
            'connectionType' => 'tcp',
            'username' => VLM_NOTIFY_JABBER_USER,
            'password' => VLM_NOTIFY_JABBER_PASS
        );

        function __construct() {
            parent::__construct();
            $this->logger = new Logger('xmpp');
            $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
            $address = $this->config['connectionType']."://".$this->config['hostname'].':'.$this->config['port'];

            $options = new Options($address);
            $options->setLogger($this->logger)
                ->setUsername($this->config['username'])
                ->setPassword($this->config['password']);

            $this->client = new Client($options);
            $this->client->connect();

            // join a channel
            $channel = new Presence;
            $channel->setTo(VLM_NOTIFY_JABBER_MAIN);
            $channel->setNickName('Postman'); //FIXME : Add servername ?
            $client->send($channel);

        }
        
        function postone($m) {
            if ($m['url'] != '') {
                $status = sprintf("%s - %s", $m['summary'], $m['url']);
            } else {
                $status = $m['summary'];
            }

            $message = new Message($status);
            $message->setTo(VLM_NOTIFY_JABBER_MAIN);
            $message->setType(Message::TYPE_GROUPCHAT);
            $this->client->send($message);
            return True;
        }
        
        function close($m) {
            parent::close();
            sleep(5);
            $this->client->disconnect();
        }
                        
    }    
    
    $jabber = new VlmNotifyJabber();
    $jabber->fetch();
    $jabber->post();
    $jabber->close();
?>
