<?php

# Contact: <paparazzia@gmail.com>

include_once('config.php');

$current_time = time();
$PAGETITLE="Module Status";
include("../includes/header-status.inc");
?>
    <div id="modulesstatus">

    <table>
    <tr><th>Time (GMT)</th><th>Module #id</th><th>Revision #id</th></tr>
<?php
    $query2 = "SELECT max(updated) as updated, moduleid, max(revid) as revid FROM modules_status "
             ."WHERE serverid = '".$_SERVER["SERVER_ADDR"]."' GROUP BY moduleid ORDER BY moduleid";
    $result2 = wrapper_mysql_db_query_reader($query2) or die("Query [$query2] failed \n");
    $odd = 0;
    while($row2 = mysql_fetch_assoc($result2)) {
        $lastupdate     = $row2['updated'];
        $moduleid          = $row2['moduleid'];
        $revision          = $row2['revid'];
     	  if ($odd == 0) {
      	    echo "<tr class=\"even\">\n";
      	    $odd = 1;
        } else {
      	    echo "<tr class=\"odd\">\n";
      	    $odd = 0;
        }
        printf("<td class=\"time\">%s</td>\n", $lastupdate );
        printf("<td class=\"text\">%s</td>", $moduleid );
        printf("<td class=\"count\">%s</td>", $revision );
        echo "</tr>\n";
    }     
?>
    </table>
  </div>

  </body>
</html>
