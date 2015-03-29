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
    require 'src/OptionsAwareInterface.php';
    require "src/Event/EventManagerAwareInterface.php";
    require 'src/Protocol/ImplementationInterface.php';
    require 'src/EventListener/EventListenerInterface.php';
    require 'src/EventListener/AbstractEventListener.php';
    require 'src/EventListener/BlockingEventListenerInterface.php';
    require 'src/EventListener/Stream/Stream.php';
    require 'src/EventListener/Stream/StreamError.php';
    require 'src/EventListener/Stream/StartTls.php';
    require 'src/EventListener/Stream/Authentication/AuthenticationInterface.php';
    require 'src/EventListener/Stream/Authentication/Plain.php';
    require 'src/EventListener/Stream/Authentication.php';
    require 'src/EventListener/Stream/AbstractSessionEvent.php';
    require 'src/EventListener/Stream/Bind.php';
    require 'src/EventListener/Stream/Session.php';
    require 'src/EventListener/Stream/Roster.php';
    require 'src/EventListener/Logger.php';
    require 'src/Protocol/DefaultImplementation.php';
    require 'src/Options.php';
    require 'src/Connection/ConnectionInterface.php';
    require 'src/Event/EventInterface.php';
    require 'src/Event/Event.php';
    require 'src/Event/XMLEventInterface.php';
    require 'src/Event/XMLEvent.php';
    require 'src/Stream/XMLStream.php';
    require 'src/Connection/AbstractConnection.php';
    require 'src/Connection/SocketConnectionInterface.php';
    require 'src/Util/ErrorHandler.php';
    require 'src/Util/XML.php';
    require 'src/Stream/SocketClient.php';
    require 'src/Connection/Socket.php';
    require 'src/Event/EventManagerInterface.php';
    require 'src/Event/EventManager.php';
    require 'src/Client.php';
    require 'src/Protocol/ProtocolImplementationInterface.php';
    require 'src/Protocol/Presence.php';
    require 'src/Protocol/Message.php';

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
            'username' => VLM_XMPP_POSTMAN_USER,
            'password' => VLM_XMPP_POSTMAN_PASS
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
            $channel->setTo(VLM_XMPP_CHAT_JID_MAIN);
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
            $message->setTo(VLM_XMPP_CHAT_JID_MAIN);
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
