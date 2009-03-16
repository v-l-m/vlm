<?php 
// Le user peut-t'il s'engager dans les courses ou est il déjà engagé
$display_submit = "false";
if ( isLoggedIn() ) {
     $users = new Users(getLoginId());
     if ( $users->engaged == 0 ) {
          $display_submit = "true";
	  $av_races=availableRaces($users->idusers);
     }
}

// Type of race (permanent / one shot )
$q=htmlentities(quote_smart($_REQUEST['type'])) ;

//change that, nothing SQL shoud appear directly in scripts
$query1 = "SELECT deptime,idraces,racename,closetime,boattype,firstpcttime,maxboats,racetype FROM races  ";

$query1 .= " WHERE ( ( started = 0 AND DEPTIME > " . time() . " )
	              OR ( closetime > " . time() .  " ) ) " ;


if ( $q != "" ) { 
        $query1 .= " AND racetype = " . intval($q). " " ;
}
$query1 .= " ORDER BY started ASC, deptime ASC, closetime ASC ";

$result1 = mysql_db_query(DBNAME,$query1) or die("Query [$query1] failed \n");

// Recherche de toutes les courses prévues
while($row = mysql_fetch_array($result1, MYSQL_NUM))
{
    $dep = $row[0];
    $idraces = $row[1];
    $racename = $row[2];
    $closetime = $row[3];
    $boattype = strtoupper(ereg_replace('^.*_', '' ,$row[4]));
    $firstpcttime = $row[5];
    $maxboats = $row[6];

    list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);

    $time = time();
    $duration = $dep - time();

    if ( $dep < $time AND $closetime <$time ) { //no race planned
        printf("      ".$strings[$lang]["norace"]."\n");
    } else {
        echo "<table width=\"100%\">\n<tr>\n";
        echo "<td width=200 align=\"left\" class=\"boat\">";
        // Carte de la course
        $href = "images/racemaps/regate".$idraces.".jpg";
        if ( file_exists($href) ) {
       	    $status_content = "&lt;img src=&quot;$href&quot; alt=&quot;".$strings[$lang]["racemap"]."&quot; />";
       	    list($xSize, $ySize, $type, $attr) = getimagesize($href);
        	  echo "<img src=\"minimap.php?idraces=" . $idraces . 
                 "\" onmouseover=\"showDivLeft('infobulle','$status_content', $xSize, $ySize);\" " .
                 " onmouseout=\"hideDiv('infobulle');\" " .
                 " alt=\"" .$strings[$lang]["racemap"]. "\" />";
        }
        echo "</td>\n";
  
        echo "<td align=\"left\" class=\"boat\">";

        printf("<h1>%d (%s) - %s</h1>\n",$idraces, $boattype, $racename); /* FIXME use style there */
        printf ("  <ul>");
        if ( $dep > $time ) {
            $duration = duration2string($duration);
            printf("    <li>".$strings[$lang]["nextrace"] . "</li>\n" , $duration[0],$duration[1],$duration[2]);
            printf("    <li>".$strings[$lang]["startrace"]. gmdate("Y/m/d H:i:s", $dep)." GMT.</li>\n");
        } else {
            $duration = duration2string(-$duration);
            printf("    <li>".$strings[$lang]["openrace"] . "</li>\n", $duration[0],$duration[1],$duration[2]);
        }
  
        if ( $closetime > $dep ) {
            printf("    <li>".$strings[$lang]["closerace"]. gmdate("Y/m/d H:i:s", $closetime)." GMT</li>\n");
        }
  
        if ( $firstpcttime >0 ) {
            printf("    <li>".$strings[$lang]["endrace"]. "%d %%</li>\n", 100 + $firstpcttime );
        } else {
            printf("    <li>".$strings[$lang]["endrace"]." no limit</li>\n");
        }
        if ( $maxboats != 0 ) {
             printf ("    <li>Engaged / Total : " . $num_engaged . "/" . $maxboats . "</li>\n" );
        } else {
             printf ("    <li>Engaged : " . $num_engaged . " (unlimited)</li>\n" );
        }
        printf("    <li><a href=\"/ics.php?lang=%s&idraces=%s\">WP(s)</a></li>\n" , $lang,$idraces);
        // Affichage des IC destinées à la racelist
        $fullRacesObj = new fullRaces($idraces);
        foreach ( $fullRacesObj->races->ics as $ic) {
            if (($ic['flag'] & IC_FLAG_VISIBLE) and (IC_FLAG_RACELIST & $ic['flag']) ) {
                if ($ic['flag'] & IC_FLAG_LINKFORUM) {
                    printf ("    <li><a href=\"".$ic['instructions']."\" target=_ic><b>INSTRUCTIONS DE COURSE SUR LE FORUM / RACE INSTRUCTIONS ON THE FORUM</b></a></li>\n");
                } else {
                    printf ("    <li>".$ic['instructions']."</li>\n");
                }
            }
        }

      printf ("  </ul>");
      echo "</td>\n";

      echo "</tr>\n</table>\n";


        if ( $display_submit == "true" and in_array($idraces, $av_races) ) {
            // Subscribe button/form
            $sub_button= "
    		<form action=\"myboat.php\" method=\"post\">
    		<input type=\"hidden\" name=\"idraces\" value=" . $idraces . ">
    		<input type=\"hidden\" name=\"idusers\" value=" . $fullUsersObj->users->idusers . ">
    		<input type=\"hidden\" name=\"type\" value=\"subscribe\">
    		<input type=\"hidden\" name=\"lang\" value=\"" . $lang . "\">
    		<input type=\"submit\" value=\"" . $strings[$lang]["subscribe"] . "\" >
    		</form>
	      ";
	    echo $sub_button;
        } 


    }
    echo "<hr />";
}


if ( $q == RACE_TYPE_CLASSIC ) {
	$URL=$_SERVER["PHP_SELF"] . "?lang=" . $lang . "&amp;type=" . RACE_TYPE_RECORD;
	echo "<input type=\"button\" value=\"Click here to see Permanent-Record races\" onclick=\"document.location='" . $URL . "'\" />";
} else {
	$URL=$_SERVER["PHP_SELF"] . "?lang=" . $lang . "&amp;type=" . RACE_TYPE_CLASSIC;
	echo "<input type=\"button\" value=\"Click here to see Classical &amp; Qualification races\" onclick=\"document.location='" . $URL . "'\" />";
}

?>
