<?php

session_start();
include_once("config.php");
include_once("functions.php");
require_once("externals/xmpp-prebind-php/lib/XmppPrebind.php");

#FIXME: Should throw correct http error
if (!isPlayerLoggedIn() or !VLM_XMPP_ON) die("Not logged");
$p = getLoggedPlayerObject();

$res = get_cgi_var('res', 'site');

$xmppPrebind = new XmppPrebind(VLM_XMPP_HOST, VLM_XMPP_HTTP_BIND, $res, false, false);
$xmppPrebind->connect($p->playername, $p->password);
$xmppPrebind->auth();

$sessionInfo = $xmppPrebind->getSessionInfo(); // array containing sid, rid and jid

header("Content-type: application/json; charset=UTF-8");
echo json_encode($sessionInfo);

?>
