<?php

/**
 * Comment here for explanation of the options.
 *
 * Create a new XMPP Object with the required params
 *
 * @param string $jabberHost Jabber Server Host
 * @param string $boshUri    Full URI to the http-bind
 * @param string $resource   Resource identifier
 * @param bool   $useSsl     Use SSL (not working yet, TODO)
 * @param bool   $debug      Enable debug
 */
 
include_once("config.php"); 
require_once('externals/xmpp-prebind-php/lib/XmppPrebind.php');

#FIXME: Should throw correct http error
if (!isPlayerLoggedIn()) die("Not logged");
$p = getLoggedPlayerObject();

#FIXME: should go to config-defines
define("VLM_XMPP_HOST", "ir.testing.v-l-m.org");
define("VLM_XMPP_HTTP_BIND", WWW_SERVER_URL."/http-bind");

#FIXME: Debug
print_r($_REQUEST);

$xmppPrebind = new XmppPrebind(VLM_XMPP_HOST, VLM_XMPP_HTTP_BIND, 'site', false, false);
$xmppPrebind->connect($p->playername, $p->password);
$xmppPrebind->auth();
$sessionInfo = $xmppPrebind->getSessionInfo(); // array containing sid, rid and jid

#FIXME: Debug
print_r($sessionInfo);

#FIXME: Should output json

?>
