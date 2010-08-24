<?php
    include_once("config.php");
    include_once("wslib.php");

    header("content-type: text/plain; charset=UTF-8");

    $ws = new WSBaseBoat();

    $idr = $ws->check_cgi_int('idr', 'IDR01', 'IDR02');

    $query_race = "SELECT deptime FROM races WHERE idraces = ".$idr;
    $result = wrapper_mysql_db_query_reader($query_race) or $ws->reply_with_error('CORE01');
    if ($row = mysql_fetch_assoc($result)) {
        $deptime = $row['deptime'];
    } else {
       $ws->reply_with_error('IDR03');
    }
    //FIXME : blackout ???
    $query =  "(".
              "SELECT histpos.time AS t, histpos.lat AS lt, histpos.long AS lg FROM histpos" .
              " WHERE histpos.idusers=" . $ws->idu. 
              " AND histpos.race=" . $idr . 
              " AND histpos.time >= ".$deptime.
              " ORDER BY time ASC".
              ") UNION (".
              "SELECT positions.time AS t, positions.lat AS lt, positions.long AS lg FROM positions" .
              " WHERE positions.idusers=" . $ws->idu .
              " AND positions.race=" . $idr .
              " AND positions.time >= $deptime".
              " ORDER BY time ASC".
              ")";

    $result = wrapper_mysql_db_query_reader($query) or $ws->reply_with_error('CORE01');
    $nbresults = mysql_num_rows($result);

    $ws->answer['tracks'] = Array();

    while(  $row = mysql_fetch_array($result, MYSQL_NUM) ) {
        $ws->answer['tracks'][] = $row;
    }

    $ws->reply_with_success();

?>
