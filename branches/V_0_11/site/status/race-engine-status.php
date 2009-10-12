<?php

# Contact: <paparazzia@gmail.com>

include_once('config.php');

$current_time = time();
$PAGETITLE="Race engine status check";
include("../includes/header-status.inc");
?>
    <p id="currenttimeblurb">
      Current time: <span id="currenttime">
      <?php echo gmdate("Y-m-d \T H:i:s", $current_time) ?> GMT
    </span>
    <span class="hidden"><?php echo $current_time ?></span>
    </p>
    <div id="racesdetailstatus">
    <h2>Race detailled status</h2>
    <table>
    <th>id</th><th>Racename</th><th>Last update</th><th>Crank frequency</th><th>Time from update</th>
<?php

    $query = "SELECT idraces, racename , vacfreq FROM races WHERE started > 0 ";
    $query .= " order by vacfreq ASC, deptime DESC, idraces ASC";
    $result = wrapper_mysql_db_query($query);
    $odd = 0;
    while($row = mysql_fetch_assoc($result)) {
        $idraces  = $row['idraces'];
        $racename = $row['racename'];
        $vacfreq  = $row['vacfreq'];
        $query2   = "SELECT `time`, update_comment FROM updates WHERE update_comment LIKE '% ".$idraces."%' OR update_comment LIKE '".$idraces."%' ORDER BY `time` DESC LIMIT 1";
        $result2  = wrapper_mysql_db_query($query2) or die("Query [$query2] failed \n");
        $row2     = mysql_fetch_assoc($result2);
        $delay    = $current_time - (int)$row2['time'];
        if ($delay > 60*(int)$vacfreq) {
            $cssklass = "maybelate";
        } else {
            $cssklass = "intime";
        }
	if ($odd == 0) {
	  echo "<tr class=\"even\">\n";
	  $odd = 1;
	} else {
	  echo "<tr class=\"odd\">\n";
	  $odd = 0;
	}	  
        echo "<td class=\"idraces\">$idraces</td>";
        echo "<td class=\"racename\">$racename</td>";
        echo "<td class=\"time\">".gmdate("H:i:s", $row2['time'])."</td>";
        echo "<td class=\"duration\">".($vacfreq*60)."</td>";
        echo "<td class=\"$cssklass\">".sprintf("%03d", $delay)." sec. ago</td>\n";
        echo "</tr>\n";
    } // Foreach race

?>
  </table>
  </div>
  <div id="updatedetailstatus">
    <h2>Last 20 update status</h2>
    <table>
    <tr><th>Time (GMT)</th><th>Duration (s)</th><th>Nb. races</th><th>Nb. boats</th><th>boat/sec</th></tr>
    <?php
      $query2 = "SELECT `time`,races,boats,duration,update_comment FROM updates ORDER BY `time` DESC LIMIT 20";
      $result2 = wrapper_mysql_db_query($query2) or die("Query [$query2] failed \n");
      $odd = 0;
      while($row2 = mysql_fetch_assoc($result2)) {
          $lastupdate     = (int)$row2['time'];
          $races          = $row2['races'];
          $boats          = $row2['boats'];
          $duration       = max($row2['duration'],1);
          $update_comment = $row2['update_comment'];
          if ($duration > 60) {
              $cssklass = "maybelate";
          } else {
              $cssklass = "intime";
          }
	  if ($odd == 0) {
	    echo "<tr class=\"even\">\n";
	    $odd = 1;
	  } else {
	    echo "<tr class=\"odd\">\n";
	    $odd = 0;
	  }
          printf("<td class=\"time\">%s</td>\n", gmdate('Y-m-d \T H:i:s', $lastupdate) );
          printf("<td class=\"$cssklass\">%d sec.</td>", $duration );
          printf("<td class=\"count\" title=\"%s\">%d</td>", $update_comment, $races );
          printf("<td class=\"count\">%d</td>", $boats );
          printf("<td class=\"float\">%.3f</td>", (float)$boats/$duration );          
          echo "</tr>\n";
      }     
     ?>
    </table>
  </div>

  </body>
</html>
