<?php

session_start();
include_once("config.php");
include_once("functions.php");
require_once("externals/xmpp-prebind-php/lib/XmppPrebind.php");

if (!isPlayerLoggedIn() or !VLM_XMPP_ON) die503("Not logged");
$p = getLoggedPlayerObject();

$res = get_cgi_var('res', strtolower($_SERVER['HTTP_HOST']).'-'.md5($_SERVER['HTTP_USER_AGENT']));

$xmppPrebind = new XmppPrebind(VLM_XMPP_HOST, VLM_XMPP_HTTP_BIND_URL, $res, false, false);
$xmppPrebind->connect($p->getJid(), $p->password);
$xmppPrebind->auth();

$sessionInfo = $xmppPrebind->getSessionInfo(); // array containing sid, rid and jid

header("Content-type: application/json; charset=UTF-8");
echo json_encode($sessionInfo);

?>
