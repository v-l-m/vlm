<?php
    require("config.php");
    require('notify.class.php');
    
    class VlmNotifyIrc extends VlmNotify {
        var $media = "irc";
        var $rate_limit = 4;
        var $socket = null;
        
        function open() {
            if (!is_null($this->socket)) return;
            $strServeur = VLM_NOTIFY_IRC_SERVER; // serveur IRC
            $intPort = 6667; // port..
            $strNickCMD = "NICK ".VLM_NOTIFY_IRC_USER;
            $strNick = ":postman";
            $strInfo = 'USER vlmpostman 0 * :bot';
            $strChannel = "JOIN ".VLM_NOTIFY_IRC_CHAN; // channel IRC

            $this->socket = @fsockopen($strServeur, $intPort); // ouverture socket sur le serveur
            if (feof ($this->socket)) {
                die ("Couldn't connect to IRC" );
            } else {
                $this->send_data ($strInfo);
                $this->send_data ($strNickCMD);
                $this->read_some_data();                
                $this->send_data ($strChannel);
                $this->read_some_data();
            }
        }

        function close() {
            parent::close();
            if (is_null($this->socket)) return;
            fputs($this->socket, "QUIT\r\n" );
            /* fermeture sock */
            fclose($this->socket);
        }    

        function send_data ($message) {
           fputs ($this->socket, "$message\r\n", 4096);
        }
        function read_ping ($in) {
            if (strpos ($in, 'PING :') !== FALSE ) {
                $this->send_data ('PONG '.substr ($in, 6));
            }
        }
        function read_data ($data) {
            $in = fgets ($data, 4096);
            $this->read_ping ($in);
            return $in;
        }
        
        function read_some_data() {
            for ($i = 0; $i < 5; $i++) {
                $this->read_data($this->socket);
            }
        }

        function postone($message) {
            if ($message['url'] != '') {
                $status = sprintf("%s - %s", $message['summary'], $message['url']);
            } else {
                $status = $message['summary'];
            }
            $this->open(); //Ouvre le chan s'il n'est pas encore ouvert
            $this->read_some_data();
            $this->send_data("PRIVMSG ".VLM_NOTIFY_IRC_CHAN." :".$status);
            $this->read_some_data();
            echo $status."\n";
            sleep(1);
            return True;
        }
    }
    
    $irc = new VlmNotifyIrc();
    $irc->fetch();
    $irc->post();
    sleep(2); //do not rush to end the connection to allow clean closing
    $irc->close();

?>
