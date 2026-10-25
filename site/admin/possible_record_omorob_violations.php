<?php
    $PAGETITLE = "Report : Possible OMOROB violations in record races";
    include ("htmlstart.php");
    include_once ("functions.php");

    echo '<table><tr><td>Player id |</td><td>Player name |</td><td>Boat id |</td><td>Race id |</td><td>Race name |</td></td><td>Already arrived boat</td></tr></table>'; 
    $q = "select distinct inscrits.IDP as player, players.playername as playername, inscrits.IDU as user, inscrits.IDR, inscrits.RN as racename, arrives.ADU as alreadyarrived
    from
    (select playerstousers.idplayers as IDP, users.idusers as IDU, users.engaged as IDR, races.racename as RN 
        from users inner join races on users.engaged=races.idraces inner join playerstousers on users.idusers=playerstousers.idusers 
        where races.racetype=1 and users.engaged<>0 and races.started<>-1 and playerstousers.linktype=1) as inscrits
    inner join 
    (select playerstousers.idplayers as ADP, races_results.idusers as ADU, races_results.idraces as ADR 
        from races_results join races on races_results.idraces=races.idraces join playerstousers on races_results.idusers=playerstousers.idusers 
        where races_results.position=1 and races.racetype=1 and races.started<>-1 and playerstousers.linktype=1) as arrives
    on inscrits.IDP = arrives.ADP and inscrits.IDR = arrives.ADR
    inner join 
    players on inscrits.IDP=players.idplayers 
    where inscrits.IDU<>arrives.ADU;
    ";

    htmlQuery($q);

    echo '<br />';

    echo 'Only boats which have crossed the starting line';

    $q = "select distinct inscrits.IDP as player, players.playername as playername, inscrits.IDU as user, inscrits.IDR, inscrits.RN as racename, arrives.ADU as alreadyarrived
    from
    (select playerstousers.idplayers as IDP, users.idusers as IDU, users.engaged as IDR, races.racename as RN 
        from users inner join races on users.engaged=races.idraces inner join playerstousers on users.idusers=playerstousers.idusers 
        where races.racetype=1 and users.engaged<>0 and users.userdeptime<>-1 and races.started<>-1 and playerstousers.linktype=1) as inscrits
    inner join 
    (select playerstousers.idplayers as ADP, races_results.idusers as ADU, races_results.idraces as ADR 
        from races_results join races on races_results.idraces=races.idraces join playerstousers on races_results.idusers=playerstousers.idusers 
        where races_results.position=1 and races.racetype=1 and races.started<>-1 and playerstousers.linktype=1) as arrives
    on inscrits.IDP = arrives.ADP and inscrits.IDR = arrives.ADR
    inner join 
    players on inscrits.IDP=players.idplayers 
    where inscrits.IDU<>arrives.ADU;
    ";

    htmlQuery($q);

    echo '<br />';

    echo '<h2>Possible violations on 3 mers record races </h2>';
    echo 'Players lauching a boat different from the one which has already arrived on one of the 3 mers record races';

    echo '<br />';
    echo '<br />';

    echo '<table><tr><td>Player id |</td><td>Player name |</td><td>Boat id |</td><td>Race name |</td><td>Race id |</td></td><td>Already arrived boat</td></tr></table>';    


    $q = "select distinct inscrits.IDP as player, players.playername as playername, inscrits.IDU as user, inscrits.RN as racename, inscrits.IDR as idraces, arrives.ADU as arrived
    from
    (select playerstousers.idplayers as IDP, users.idusers as IDU, users.engaged as IDR, races.racename as RN 
        from users inner join races on users.engaged=races.idraces inner join playerstousers on users.idusers=playerstousers.idusers 
        where (races.idraces=240101 or races.idraces=240102 or races.idraces=240103) and users.engaged<>0 and races.started<>-1 and playerstousers.linktype=1) as inscrits
    inner join 
    (select playerstousers.idplayers as ADP, races_results.idusers as ADU, races_results.idraces as ADR 
        from races_results join races on races_results.idraces=races.idraces join playerstousers on races_results.idusers=playerstousers.idusers 
        where races_results.position=1 and races.racetype=1 and races.started<>-1 and playerstousers.linktype=1) as arrives
    on inscrits.IDP = arrives.ADP and (arrives.ADR=240101 or arrives.ADR=240102 or arrives.ADR=240103)
    inner join 
    players on inscrits.IDP=players.idplayers 
    where inscrits.IDU<>arrives.ADU ORDER BY inscrits.IDP;
    ";

    htmlQuery($q);

    echo '<br />';
    echo 'Only boats which have crossed the starting line';

    $q = "select distinct inscrits.IDP as player, players.playername as playername, inscrits.IDU as user, inscrits.RN as racename, inscrits.IDR as idraces, arrives.ADU as arrived
    from
    (select playerstousers.idplayers as IDP, users.idusers as IDU, users.engaged as IDR, races.racename as RN 
        from users inner join races on users.engaged=races.idraces inner join playerstousers on users.idusers=playerstousers.idusers 
        where (races.idraces=240101 or races.idraces=240102 or races.idraces=240103) and users.engaged<>0 and users.userdeptime<>-1 and races.started<>-1 and playerstousers.linktype=1) as inscrits
    inner join 
    (select playerstousers.idplayers as ADP, races_results.idusers as ADU, races_results.idraces as ADR 
        from races_results join races on races_results.idraces=races.idraces join playerstousers on races_results.idusers=playerstousers.idusers 
        where races_results.position=1 and races.racetype=1 and races.started<>-1 and playerstousers.linktype=1) as arrives
    on inscrits.IDP = arrives.ADP and (arrives.ADR=240101 or arrives.ADR=240102 or arrives.ADR=240103)
    inner join 
    players on inscrits.IDP=players.idplayers 
    where inscrits.IDU<>arrives.ADU ORDER BY inscrits.IDP;
    ";

    htmlQuery($q);

    echo '<br />';
    echo 'Players lauching several boats at the same time on several of the 3 mers record races';

    $q= "select R1.player, players.playername, R1.user, R1.race, R2.user, R2.race
    from
    (select playerstousers.idplayers as player, users.idusers as user, users.engaged as race
    from users inner join playerstousers on users.idusers=playerstousers.idusers inner join races on users.engaged = races.idraces
    where (races.idraces=240101 or races.idraces=240102 or races.idraces=240103) and playerstousers.linktype=1) as R1
    join
    (select playerstousers.idplayers as player, users.idusers as user, users.engaged as race
    from users inner join playerstousers on users.idusers=playerstousers.idusers inner join races on users.engaged = races.idraces
    where (races.idraces=240101 or races.idraces=240102 or races.idraces=240103) and playerstousers.linktype=1) as R2
    on R1.player=R2.player
    inner join players on players.idplayers=R1.player
    where R1.user<R2.user;
    ";

    htmlQuery($q);

    echo '<br />';

    echo '<h2>Possible violations on SNSM record races </h2>';
    echo 'Players lauching a boat different from the one which has already arrived on one of the SNSM record races';


    echo '<br />';
    echo '<br />';
    
    echo '<table><tr><td>Player id |</td><td>Player name |</td><td>Boat id |</td><td>Race name |</td><td>Race id |</td></td><td>Already arrived boat</td></tr></table>';    


    $q = "select distinct inscrits.IDP as player, players.playername as playername, inscrits.IDU as user, inscrits.RN as racename, inscrits.IDR as idraces, arrives.ADU as arrived
    from
    (select playerstousers.idplayers as IDP, users.idusers as IDU, users.engaged as IDR, races.racename as RN 
        from users inner join races on users.engaged=races.idraces inner join playerstousers on users.idusers=playerstousers.idusers 
        where (races.idraces=20244401 or races.idraces=20244402 or races.idraces=20244403) and users.engaged<>0 and races.started<>-1 and playerstousers.linktype=1) as inscrits
    inner join 
    (select playerstousers.idplayers as ADP, races_results.idusers as ADU, races_results.idraces as ADR 
        from races_results join races on races_results.idraces=races.idraces join playerstousers on races_results.idusers=playerstousers.idusers 
        where races_results.position=1 and races.racetype=1 and races.started<>-1 and playerstousers.linktype=1) as arrives
    on inscrits.IDP = arrives.ADP and (arrives.ADR=20244401 or arrives.ADR=20244402 or arrives.ADR=20244403)
    inner join 
    players on inscrits.IDP=players.idplayers 
    where inscrits.IDU<>arrives.ADU ORDER BY inscrits.IDP;
    ";

    htmlQuery($q);

    echo '<br />';
    echo 'Players lauching several boats at the same time on several of the SNSM record races';

    $q= "select R1.player, players.playername, R1.user, R1.race, R2.user, R2.race
    from
    (select playerstousers.idplayers as player, users.idusers as user, users.engaged as race
    from users inner join playerstousers on users.idusers=playerstousers.idusers inner join races on users.engaged = races.idraces
    where (races.idraces=20244401 or races.idraces=20244402 or races.idraces=20244403) and playerstousers.linktype=1) as R1
    join
    (select playerstousers.idplayers as player, users.idusers as user, users.engaged as race
    from users inner join playerstousers on users.idusers=playerstousers.idusers inner join races on users.engaged = races.idraces
    where (races.idraces=20244401 or races.idraces=20244402 or races.idraces=20244403) and playerstousers.linktype=1) as R2
    on R1.player=R2.player
    inner join players on players.idplayers=R1.player
    where R1.user<R2.user;
    ";

    htmlQuery($q);

    include ("htmlend.php");
?>
