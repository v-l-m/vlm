<?php
    include_once("config.php");
    include_once("wslib.php");
    include_once("racesgroups.class.php");

    header("content-type: text/plain; charset=UTF-8");

    $ws = new WSBaseRaceGroup();
    $ws->require_idg();
    
    $ws->maxage = 24*3600; //client cache duration
    $rgo = $ws->rgo;
    
    $ws->answer['group'] = Array(
        'idg' => $rgo->grouptag, 'grouptag' => $rgo->grouptag,
        'title' => $rgo->grouptitle, 'name' => $rgo->groupname, 'description' => $rgo->description, 'updated' => $rgo->updated
        );
    $ws->answer['races'] = $rgo->getRaces();
    $ws->reply_with_success();

?>
