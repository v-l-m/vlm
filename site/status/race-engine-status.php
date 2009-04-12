<?php

# Contact: <paparazzia@gmail.com>

include_once('config.php');

$current_time = time();
header('Content-Type: application/xhtml+xml; charset=UTF-8');
header('Cache-Control: max-age=1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Races Engine Status</title>
    <style type="text/css">
      <![CDATA[
             .intime {
                 background-color: #99FF99;
                 }
             .maybelate {
                 background-color: #FFFF99;
                 }
             li, li > p {
                 margin-top: 0px;
                 margin-bottom: 0px;
             }
       .hidden {
     display: none;
       }
             td {
                 text-align: center;
                 }
         ]]>
    </style>
  </head>
  <body>
    <h1>Races Engine status check</h1>
    <div id="racesglobalstatus">
    <p id="currenttimeblurb">
      Current time: <span id="currenttime">
      <?php echo gmdate("Y-m-d \T H:i:s", $current_time) ?> GMT
    </span>
    <span class="hidden"><?php echo $current_time ?></span>
    </p>
    </div>
    <div id="racesdetailstatus">
    <h2>Race detailled status</h2>
    <ul>
<?php

    $query = "SELECT idraces, racename , vacfreq FROM races WHERE started > 0 ";
    $query .= " order by vacfreq ASC, deptime DESC, idraces ASC";
    $result = wrapper_mysql_db_query(DBNAME,$query);
    while($row = mysql_fetch_assoc($result)) {
        $idraces = $row['idraces'];
        $racename = $row['racename'];
        $vacfreq = $row['vacfreq'];
        $query2 = "SELECT `time`, update_comment FROM updates WHERE update_comment LIKE '% ".$idraces."%' OR update_comment LIKE '".$idraces."%' ORDER BY `time` DESC LIMIT 1";
        $result2 = wrapper_mysql_db_query(DBNAME,$query2) or die("Query [$query2] failed \n");
        $row2 = mysql_fetch_assoc($result2);
        $delay = $current_time - (int)$row2['time'];
        if ($delay > 60*(int)$vacfreq) {
            $cssklass = "maybelate";
        } else {
            $cssklass = "intime";
        }
        echo "<li>\n";
        echo "<span  class=\"$cssklass\">".sprintf("%03d", $delay)." seconds ago</span>\n";
        echo " - LUP : ".gmdate("H:i:s", $row2['time'])." - (<b>$idraces</b>) $racename\n";
        echo "</li>\n";
    } // Foreach race

?>
  </ul>
  </div>
  <hr />
  <div id="updatedetailstatus">
    <h2>Last 10 update status</h2>
    <table>
    <tr><th>Time (GMT)</th><th>Duration (s)</th><th>Races</th><th>Boats</th><th>b/s</th></tr>
    <?php
      $query2 = "SELECT `time`,races,boats,duration,update_comment FROM updates ORDER BY `time` DESC LIMIT 10";
      $result2 = wrapper_mysql_db_query(DBNAME,$query2) or die("Query [$query2] failed \n");
      while($row2 = mysql_fetch_assoc($result2)) {
          echo "<tr>\n";
          $lastupdate = $row2['time'];
          $races = $row2['races'];
          $boats = $row2['boats'];
          $duration = max($row2['duration'],1);
          $update_comment = $row2['update_comment'];
          printf("<td>%s</td>\n", gmdate('Y-m-d \T H:i:s', time()) );
          printf("<td>%d</td>", $duration );
          printf("<td title=\"%s\">%d</td>", $update_comment, $races );
          printf("<td>%d</td>", $boats );
          printf("<td>%.3f</td>", (float)$boats/$duration );          
          echo "</tr>\n";
      }     
     ?>
    </table>
  </div>

  </body>
</html>
