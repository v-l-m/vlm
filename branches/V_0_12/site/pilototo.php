<?php

    /*
    ** Page pilototo re-entrante : gestion de la table auto_pilot pour l'utilisateur connecté
    */
    
    session_start();
    include_once("includes/strings.inc");
    include_once("config.php");
    include_once("functions.php");

    header("Content-type: text/html; charset=utf-8");

    //helper pour construire la page
    
    function echoPilototoRow($numline, $row = 0, $ts = "", $pim = "", $pip = "", $status = "") {
        global $strings, $lang, $pilotmodeList;
        if ($row === 0) {
            $klass = "blank";
            $ts = time();
            $firstcolaction = "pilototo_prog_add";
            $statusstring = "";
        } else {
            $klass = $status;
            $firstcolaction = "pilototo_prog_upd";
            $statusstring = "$status&nbsp;<input type=\"submit\" name=\"action\" value=" . $strings[$lang]["pilototo_prog_del"] . " />";
        }
        $timestring = gmdate("Y/m/d H:i:s", $ts)." GMT";

        echo "<form action=\"pilototo.php\" method=\"post\">\n";
        echo "  <input type=\"hidden\" name=\"lang\" value=\"$lang\" />\n";
        echo "  <input type=\"hidden\" name=\"taskid\" value=\"$row\" />\n";
        echo "  <tr class=\"linepilototobox-$klasssuffix\">\n";
        echo "    <td><input type=\"submit\" name=\"action\" value=" . $strings[$lang][$firstcolaction]  ." /></td>\n";
        echo "    <td><input id=\"ts_value_$numline\" type=\"text\" name=\"time\" onChange=\"majhrdate($numline);\" width=\"15\" size=\"15\" value=\"$ts\" /></td>\n";
        echo "    <td><img src=\"".DIRECTORY_JSCALENDAR."/img.gif\" id=\"trigger_jscal_$numline\" class=\"calendarbutton\" title=\"Date selector\" onmouseover=\"this.style.background='red';\" onmouseout=\"this.style.background=''\" /></td>\n";
        
        echo "    <td><select onKeyup=\"checkpip($numline);\" name=\"pim\">\n"; 
        for ($i = 1; $i <= count($pilotmodeList); $i++) {
            echo "    <option ";
            if ($i == $pim) {
                echo "selected=\"selected\" ";
            }
            echo "value=\"$i\">$i:".$strings[$lang][$pilotmodeList[$i]]."</option>";
        }
        echo "    </select></td>\n";
        echo "    <td><input type=\"text\" name=\"pip\" width=\"20\" size=\"20\" value=\"$pip\" /></td>\n";
        echo "    <td>$statusstring</td>\n";
        //taskid, time, pilotmode, pilotparameter, status .. + Human readable date
        echo "    <td><input type=\"text\" size=\"22\" width=\"22\" name=\"gmtdate\" disabled=\"disabled\" value=\"" . $timestring . "\" /></td>\n";
        echo "    <td>" . $row . "</td>\n";
        echo "  </tr>\n";
        echo "</form>\n";
    }
    
    //all GET and POST variables
    $lang = getCurrentLang();
    
    // Les entêtes
    // FIXME : disposer d'un fichier d'en tête commun plus complet !
    include("includes/doctypeheader.html");
    echo "\n<title>VLM Programmable Auto Pilot (" . $lang . ")</title>";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style/" . getTheme() . "/style.css\" />";

///   CODE JAVASCRIPT
?>
<!-- widget calendrier -->
<script type="text/javascript" src="<?php echo DIRECTORY_JSCALENDAR; ?>/calendar.js"></script>
<script type="text/javascript" src="<?php echo DIRECTORY_JSCALENDAR; ?>/lang/calendar-<?php echo $lang; ?>.js"></script>
<script type="text/javascript" src="<?php echo DIRECTORY_JSCALENDAR; ?>/calendar-setup.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo DIRECTORY_JSCALENDAR; ?>/calendar-system.css">

<script type="text/javascript">
    var calendars = new Array();

    function calbuttonsetup(n) {
    
        for (i=0;i<=n;i++) {
            calendars[i] = Calendar.setup({
                inputField     :    "ts_value_"+i,     // id of the input field
                ifFormat       :    "%s",      // format of the input field
                button         :    "trigger_jscal_"+i,  // trigger for the calendar (button ID)
                align          :    "Br",           // alignment 
                singleClick    :    false,
                showsTime      :    true,
                firstDay       :    1,
                timeFormat     :    "24"
            });
        }
    }
    
    function dateobj(i) {
        var da = eval(document.forms[i].time.value);
        document.forms[i].time.value = da;
        da*=1000;
        return(new Date(da));
    }

    function majhrdate(i) {
        var d = dateobj(i);    
        document.forms[i].gmtdate.value=d.toGMTString();
        //FIXME : Risque de réentrance ?
        //calendars[i].setDate(d);
    }
    
    function checkpip(i) {
    
        var pim = eval(document.forms[i].pim.value);
        if ( pim >= 3 && pim <= 5 ) {
            //document.forms[i].pip.disabled=true;
            document.forms[i].pip.disabled=false;
        } else {
            document.forms[i].pip.disabled=false;
        }
    }

</script>

<?php
    echo "</head><body>";

    // Test si connecté ou pas.
    $idusers = getLoginId() ;
    if ( empty($idusers) ) {
        echo "<h4>You should not do that...your IP : " . $_SERVER["REMOTE_ADDR"] . "</h4>";
        exit();
    } 

    echo "<h4>" . $strings[$lang]["pilototo_prog_title"] . "</h4>" ;
    $usersObj = new users($idusers);

    /* PILOTO (class users) Functions
        function pilototoCheck()
        function pilototoList($status = PILOTOTO_PENDING)
        function pilototoDelete($taskid)
        function pilototoAdd($time, $pim, $pip)
        function pilototoUpdate($taskid, $time, $pim, $pip)
    */

    $action=quote_smart($_POST['action']);
    if ( !empty($action)) {
        // Action donnée, on exécute l'action
        switch ($action) {
            case $strings[$lang]["pilototo_prog_add"]:
                $time=quote_smart($_POST['time']);
                $pim=quote_smart($_POST['pim']);
                $pip=quote_smart($_POST['pip']);
                
//     if ( $pim == 3 || $pim == 4 ) {
//          $pip=0;
//     } else {
//                $pip=quote_smart($_POST['pip']);
//     }
                if ( !empty($time) && !(empty($pim)) && ( !empty($pip) || $pip == 0 ))  {
                    if ( $pim <1 || $pim >5) {
                        echo "ERROR ADD : PIM between 1 and 5 please.";
                        //} else if ( $time < time() ) {
                        //   echo "ERROR ADD : TIME is passed...(" .$time . "/" . gmdate(time()) . ")" ;
                    } else if ( ( $pim == 1 ) && ($pip <0 or $pip >359)  ) {
                        echo "ERROR ADD : With PIM=1, PIP should be between 0 and 359 please";
                    } else if ( ( $pim == 2 ) && ($pip <-180 or $pip >180)  ) {
                        echo "ERROR ADD : With PIM=2, PIP should be between -180 and 180 please";
                    } else if (  ( $pim == 3 or $pim == 4 or $pim == 5) 
                            &&    ( strlen($pip)==0 or strpos($pip, ',')==false or eregi(",.*,", $pip) )  
                        ) {
                        echo "ERROR ADD : With PIM=3, 4 or 5, PIP should be 0,0 or LATITUDE,LONGITUDE (',' between lat and long, and '.' between units and decimals)";
                    } else {
                        $rc=$usersObj->pilototoAdd($time, $pim, $pip);
                    }
                } else {
                    printf ("ERROR ADD: Mandatory Param missing... time=%s, pim=%s, pip=%s\n", $time, $pim, $pip);
                }
                break;
            case $strings[$lang]["pilototo_prog_upd"]:
                $taskid=quote_smart($_POST['taskid']);
                $time=quote_smart($_POST['time']);
                $pim=quote_smart($_POST['pim']);
                $pip=quote_smart($_POST['pip']);

//     if ( $pim == 3 || $pim == 4 ) {
//          $pip=0;
//     } else {
//                $pip=quote_smart($_POST['pip']);
//     }

                if ( !empty($taskid) && !empty($time) && !(empty($pim)) && ( !empty($pip) || $pip ==0 ) ) {
                if ( $pim <1 || $pim >5) {
                    echo "ERROR : PIM between 1 and 5 please.";
                    //} else if ( $time < gmdate(time()) ) {
                    //   echo "ERROR ADD : TIME is passed...(" .$time . "/" . gmdate(time()) . ")" ;
                    } else if ( ( $pim == 1 ) && ($pip <0 or $pip >359)  ) {
                        echo "ERROR : With PIM=1, PIP should be between 0 and 359 please";
                    } else if ( ( $pim == 2 ) && ($pip <-180 or $pip >180)  ) {
                        echo "ERROR : With PIM=2, PIP should be between -180 and 180 please";
                    } else if ( ( $pim == 3 or $pim == 4 or $pim == 5 ) && ( strlen($pip)==0 or strpos($pip, ',')==false )  ) {
                        echo "ERROR : With PIM=3 or 4, PIP should be 0,0 or LATITUDE,LONGITUDE";
                    } else {
                        $rc=$usersObj->pilototoUpdate($taskid, $time, $pim, $pip);
                    }
                } else {
                    printf ("ERROR UPD: Mandatory Param missing... taskid=%d, time=%s, pim=%s, pip=%s\n", $taskid, $time, $pim, $pip);
                }
                break;
            case $strings[$lang]["pilototo_prog_del"]:
                $taskid=quote_smart($_POST['taskid']);
                if ( !empty($taskid) ) {
                    $rc=$usersObj->pilototoDelete($taskid);
                } else {
                    printf ("ERROR DEL: Task id should not be empty to delete it");
                }
                break;
            default:
        }
    }


    // On affiche la liste des actions
    $rc=$usersObj->pilototoList();
    
    echo "<div id=\"pilototolistbox\"><table class=\"pilotolist\">\n
         <th>&nbsp</th><th>Epoch Time</th><th></th><th>PIM</th><th>PIP</th><th>Status</th><th>Human Readable date</th><th>N&deg;</th>\n";
    $numligne=0;
    if ( count($usersObj->pilototo) != 0) {
        foreach ($usersObj->pilototo as $pilototo_row) {
            echoPilototoRow($numligne, $pilototo_row[0], $pilototo_row[1], $pilototo_row[2], $pilototo_row[3], $pilototo_row[4]);  
            $numligne++;
        }
    } else {
        echo  "<tr id=\"pilototo-no-event\" class=\"pilototoinfo\"><td  colspan=\"8\">" . $strings[$lang]["pilototo_no_event"] . "</td></tr>\n" ;
    }
    
    if ( $numligne < PILOTOTO_MAX_EVENTS ) {
        echoPilototoRow($numligne);
        echo "<script type=\"text/javascript\">calbuttonsetup($numligne);</script>\n";
    } else {
        echo "<tr id=\"pilototo-max-event\" class=\"pilototoinfo\">
              <td colspan=8>MAX " . PILOTOTO_MAX_EVENTS . " events</td>
              </tr>\n";
    }
    
    echo "</table></div>\n";
    
    echo "<div id=\"helpvaluespilototobox\">\n";
    echo "<b>TIME</b> : GMT, in seconds since 01/01/1970 00:00<br />";
    echo "<b>PIM</b> : pilotmode : 1/Constant Heading, 2/Constant Angle 3/Ortho Pilot 4/Best VMG 5/VBVMG<br />\n";
    echo "<b>PIP</b> : pilotparameter : For PIM=1:boatheading, For PIM=2:angle with wind, for PIM=3,4 or 5: Lat<b>,</b>Long<li>Please give <b>0,0</b> for nextrace WP, <li><b>LATITUDE,LONGITUDE</b>(<0 for South and West) to target a new WP and when reached, target next WP in the race. Ex:47.899,-3.973 for Port Laforet<li><b>LATITUDE,LONGITUDE@HEADING</b> : same but when reached, set boatheading to HEADING (0..360)\n";
    echo "</div>\n";
    $time=time();
    echo "<div id=\"helptimepilototobox\">\n";
    echo "Server(s) time is now <b>" . $time  . " (" .gmdate("Y/m/d H:i:s", $time). " GMT)</b><br />\n";
    echo "Tip1 : server_time + 3600 is in one hour, server_time+5*3600 is in 5 hours... <br />\n" ;
    echo "Tip2 : for an update, modify a value, then click on " . $strings[$lang]["pilototo_prog_upd"]."<br />\n" ;
    echo "Tip3 : status is 'pending' if an order is not yet executed, 'done' otherwise\n"  ;
    echo "</div>\n";
    echo "<div id=\"buttonspilototobox\">\n";
    echo "<input type=\"button\" value=\"Close\" onClick=\"javascript:self.close();\" />\n";
    echo "<input type=\"button\" value=\"Refresh\" onClick=\"javascript:location.reload();\" />\n";
    echo "</div>\n";
    
    echo "</body></html>";
?>
