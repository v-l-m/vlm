<?php
/*
** Page pilototo re-entrante : gestion de la table auto_pilot pour l'utilisateur connecté
*/
session_start();
include_once("_include/strings.inc");
include_once("config.php");
include_once("functions.php");



//all GET and POST variables
isset($_REQUEST['lang']) ? $lang=quote_smart($_REQUEST['lang']) : $lang="en";

// Les entêtes
echo "<html><head>";
echo "<title>VLM Programmable Auto Pilot (" . $lang . ")</title>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"_style/new-style.css\" />";

///   CODE JAVASCRIPT
?>

<script type="text/javascript">

function majhrdate(i)
{

     var da = eval(document.forms[i].time.value);
         da*=1000;
     var d = new Date(da);
     document.forms[i].gmtdate.value=d.toGMTString();

}

function checkpip(i)
{

     var pim = eval(document.forms[i].pim.value);
     if ( pim == 3 || $pim == 4 ) {
          //document.forms[i].pip.disabled=true;
          document.forms[i].pip.disabled=false;
     } else {
          document.forms[i].pip.disabled=false;
     }

}



</script>

<?php
echo "</head></html>";

// Test si connecté ou pas.
$idusers = getLoginId() ;
if ( empty($idusers) ) {
     echo "<h4>You should not do that...your IP : " . $_SERVER["REMOTE_ADDR"] . "</h4>";
     exit;
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
$action=$_POST['action'];
if ( !empty($action)) {
   // Action donnée, on exécute l'action
       switch ($action) {
            case $strings[$lang]["pilototo_prog_add"]:
	         $time=$_POST['time'];
	         $pim=$_POST['pim'];
                 $pip=$_POST['pip'];
//		 if ( $pim == 3 || $pim == 4 ) {
//		      $pip=0;
//		 } else {
//	              $pip=$_POST['pip'];
//		 }
		 if ( !empty($time) && !(empty($pim)) && ( !empty($pip) || $pip == 0 ))  {
		      if ( $pim <1 || $pim >4) {
		         echo "ERROR ADD : PIM between 1 and 4 please.";
		      //} else if ( $time < time() ) {
		      //   echo "ERROR ADD : TIME is passed...(" .$time . "/" . gmdate(time()) . ")" ;
		      } else if ( ( $pim == 1 ) && ($pip <0 or $pip >359)  ) {
		         echo "ERROR ADD : With PIM=1, PIP should be between 0 and 359 please";
		      } else if ( ( $pim == 2 ) && ($pip <-180 or $pip >180)  ) {
		         echo "ERROR ADD : With PIM=2, PIP should be between -180 and 180 please";
		      } else if (  ( $pim == 3 or $pim ==4 ) 
		             &&    ( strlen($pip)==0 or strpos($pip, ',')==false or eregi(",.*,", $pip) )  
			        ) {
		         echo "ERROR ADD : With PIM=3 or 4, PIP should be 0,0 or LATITUDE,LONGITUDE (',' between lat and long, and '.' between units and decimals)";
		      } else {
                         $rc=$usersObj->pilototoAdd($time, $pim, $pip);
		      }
		 } else {
		      printf ("ERROR ADD: Mandatory Param missing... time=%s, pim=%s, pip=%s\n", $time, $pim, $pip);
		 }
                 break;
            case $strings[$lang]["pilototo_prog_upd"]:
	         $taskid=$_POST['taskid'];
	         $time=$_POST['time'];
	         $pim=$_POST['pim'];
	         $pip=$_POST['pip'];
//		 if ( $pim == 3 || $pim == 4 ) {
//		      $pip=0;
//		 } else {
//	              $pip=$_POST['pip'];
//		 }
		 if ( !empty($taskid) && !empty($time) && !(empty($pim)) && ( !empty($pip) || $pip ==0 ) ) {
		      if ( $pim <1 || $pim >4) {
		         echo "ERROR : PIM between 1 and 4 please.";
		      //} else if ( $time < gmdate(time()) ) {
		      //   echo "ERROR ADD : TIME is passed...(" .$time . "/" . gmdate(time()) . ")" ;
		      } else if ( ( $pim == 1 ) && ($pip <0 or $pip >359)  ) {
		         echo "ERROR : With PIM=1, PIP should be between 0 and 359 please";
		      } else if ( ( $pim == 2 ) && ($pip <-180 or $pip >180)  ) {
		         echo "ERROR : With PIM=2, PIP should be between -180 and 180 please";
		      } else if ( ( $pim == 3 or $pim ==4 ) && ( strlen($pip)==0 or strpos($pip, ',')==false )  ) {
		         echo "ERROR : With PIM=3 or 4, PIP should be 0,0 or LATITUDE,LONGITUDE";
		      } else {
                         $rc=$usersObj->pilototoUpdate($taskid, $time, $pim, $pip);
		      }
		 } else {
		      printf ("ERROR UPD: Mandatory Param missing... taskid=%d, time=%s, pim=%s, pip=%s\n", $taskid, $time, $pim, $pip);
		 }
                 break;
            case $strings[$lang]["pilototo_prog_del"]:
	         $taskid=$_POST['taskid'];
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

echo "<table>
     <th>&nbsp</th><th>Epoch Time</th><th>PIM</th><th>PIP</th><th>Status</th><th>Human Readable date</th><th>N&deg;</th>";
if ( count($usersObj->pilototo) != 0) {
    $numligne=0;
    foreach ($usersObj->pilototo as $pilototo_row) {
        echo "\n<form action=pilototo.php method=post>";
	echo "<input type=hidden name=lang value=$lang>";
             echo "<tr>";
	     echo "<input type=hidden name=taskid value=$pilototo_row[0]>";
	     echo "<td>
		      <input type=submit name=action value=" . $strings[$lang]["pilototo_prog_upd"]  .">
		   </td>";
	     echo "<td><input type=text name=time onKeyup=\"majhrdate($numligne);\" width=15 size=15 value=$pilototo_row[1]></td>";
	     // SELECT LIST pour le type de pilote
	     echo "<td><input type=text name=pim onKeyup=\"checkpip($numligne);\" width=1 size=1 value=$pilototo_row[2]></td>";
	     echo "<td><input type=text name=pip width=20 size=20 value=$pilototo_row[3]></td>";
	     echo "<td>$pilototo_row[4]
	              <input type=submit name=action value=" . $strings[$lang]["pilototo_prog_del"] . ">
                   </td>";
	     //taskid, time, pilotmode, pilotparameter, status .. + Human readable date
	     echo "<td><input type=text size=25 name=gmtdate disabled value=\"" .gmdate("Y/m/d H:i:s", $pilototo_row[1]) . " GMT\"></td>";
	     echo "<td>" . $pilototo_row[0] . "</td>";
             echo "</tr>";
        echo "</form>";
        $numligne++;
    }
} else {
        echo  "<BR>" . $strings[$lang]["pilototo_no_event"] ;
}

if ( $numligne < PILOTOTO_MAX_EVENTS ) {
    echo "\n<form action=pilototo.php method=POST>";
    echo "<input type=hidden name=lang value=$lang>";
    echo "<tr>
	     <td align=center><input type=submit name=action 
	                             value=" . $strings[$lang]["pilototo_prog_add"] . "></td>
	     <td><input type=text name=time onKeyup=\"majhrdate($numligne);\" value=" . time() . " width=15 size=15></td>
	     <td><input type=text name=pim  onKeyup=\"checkpip($numligne);\" width=1  size=1 ></td>
	     <td><input type=text name=pip  width=20  size=20 ></td>
	     <td>this line to add</td>
	     <td><input name=gmtdate size=25 type=text disabled value=\"\"></td>
	     <td>&nbsp;</td>
          </tr>";
    echo "</form>";
} else {
    echo "<tr>
          <td colspan=7 align=center><B>MAX " . PILOTOTO_MAX_EVENTS . " events</B></td>
          </tr>";
}

echo "</table>";
echo "<hr>";
echo "<B>TIME</B> : GMT, in seconds since 01/01/1970 00:00<BR>";
echo "<B>PIM</B> : pilotmode : 1/Constant Heading, 2/Constant Angle 3/Ortho Pilot 4/Best VMG<BR>";
echo "<B>PIP</B> : pilotparameter : For PIM=1:boatheading, For PIM=2:angle with wind, for PIM=3 or 4: Lat<B>,</B>Long<li>Please give <B>0,0</B> for your or nextrace WP, <li><B>LATITUDE,LONGITUDE</B>(<0 for South and West) to target a new WP and when reached, target next WP in the race. Ex:47.899,-3.973 for Port Laforet<li><B>LATITUDE,LONGITUDE@HEADING</B> : same but when reached, set boatheading to HEADING (0..360)<BR><hr>";

$time=time();
echo "server(s) time is now <B>" . $time  . " (" .gmdate("Y/m/d H:i:s", $time). " GMT)</B><BR>";
echo "tip1 : server_time + 3600 is in one hour, server_time+5*3600 is in 5 hours... <BR>" ;
echo "tip2 : for an update, modify a value, then click on " . $strings[$lang]["pilototo_prog_upd"]."<br>" ;
echo "tip3 : status is 'pending' if an order is not yet executed, 'done' otherwise"  ;
echo "<br><hr><INPUT TYPE=BUTTON VALUE=\"Close\" ONCLICK=\"javascript:self.close();\">";
echo "<INPUT TYPE=BUTTON VALUE=\"Refresh\" ONCLICK=\"javascript:location.reload();\">";


exit;
