<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBasePlayer();
    $ws->maxage = WS_PLAYER_LIST_CACHE_DURATION;

    $q = $ws->check_cgi('q', 'PLAYERLIST01');
    if (is_null($q)) $ws->reply_with_error('PLAYERLIST02');

    $ws->answer['list'] = Array();

    //FIXME : Strip space !
    $query = sprintf("SELECT Concat(playername, '@', idplayers) AS fullname, playername AS id FROM players WHERE UPPER(playername) LIKE '%%%s%%' ORDER BY playername LIMIT 20", strtoupper($q));
    $result = $ws->queryRead($query);
    if ($result)  {
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ws->answer['list'][] = $row;
        }
    }

    //Start custom reply for conversejs
    $ws->answer = $ws->answer['list'];
    $ws->reply();

?>

