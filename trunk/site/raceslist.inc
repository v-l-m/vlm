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
$q=intval(htmlentities(quote_smart($_REQUEST['type']))) ;

//change that, nothing SQL shoud appear directly in scripts
$query1 = "SELECT deptime,idraces,racename,closetime,boattype,firstpcttime,maxboats FROM races  ";

$query1 .= " WHERE racetype = $q ";
$query1 .=   " AND (  ( started = 0 AND DEPTIME > " . time() . " )
	              OR ( closetime > " . time() .  " ) ) " ;

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

    if ( $dep < $time AND $closetime <$time ) //no race planned
    {
      printf("      ".$strings[$lang]["norace"]."\n");
    }
    else
    {

      echo "<table width=100%><tr><td align=left class=boat>";
      printf("<H1>%d (%s) - %s</H1>",$idraces, $boattype, $racename);
      printf ("<UL>");
      if ( $dep > $time ) {
          $duration = duration2string($duration);
          printf("<LI>".$strings[$lang]["nextrace"] . " " , $duration[0],$duration[1],$duration[2]);
          printf("<LI>".$strings[$lang]["startrace"]. gmdate("Y/m/d H:i:s", $dep)." GMT.\n");
      } else {
          $duration = duration2string(-$duration);
          printf("<LI>".$strings[$lang]["openrace"] . " ", $duration[0],$duration[1],$duration[2]);
      }

      if ( $closetime > $dep ) {
          printf("<LI>".$strings[$lang]["closerace"]. gmdate("Y/m/d H:i:s", $closetime)." GMT\n");
      }

      if ( $firstpcttime >0 ) {
          printf("<LI>".$strings[$lang]["endrace"]. "%d %%\n", 100 + $firstpcttime );
      } else {
          printf("<LI>".$strings[$lang]["endrace"]." no limit\n");
      }
      if ( $maxboats != 0 ) {
           printf ("<LI>Engaged / Total : " . $num_engaged . "/" . $maxboats . "\n" );
      } else {
           printf ("<LI>Engaged : " . $num_engaged . " (unlimited)\n" );
      }
      printf ("</UL>");
      echo "</td>";

      echo "<td align=right class=boat>";
      // Carte de la course
      $href = "racemaps/regate".$idraces.".jpg";
      if ( file_exists($href) ) {
      	$status_content = "<img src=$href>";
      	list($xSize, $ySize, $type, $attr) = getimagesize($href);
      	echo "<img src=minimap.php?idraces=" . $idraces . 
                 " onmouseover=\"showDivTopLeft('infobulle','$status_content', $xSize, $ySize);\" " .
                 " onmouseout=\"hideDiv('infobulle');\" " .
              " alt=\"" .$strings[$lang]["racemap"]. "\">";
      }
      echo "</td></tr></table>";


        if ( $display_submit == "true" and in_array($idraces, $av_races) ) {
            // Subscribe button/form
            $sub_button= "
    		<form action=\"myboat.php\">
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
    echo "<hr>";
}

if ( $q == RACE_TYPE_CLASSIC ) {
	$URL=$_SERVER["PHP_SELF"] . "?lang=" . $lang . "&amp;type=" . RACE_TYPE_RECORD;
	echo "<input type=button value=\"Click here to see Permanent-Record races\" onClick=\"document.location='" . $URL . "'\">";
} else {
	$URL=$_SERVER["PHP_SELF"] . "?lang=" . $lang . "&amp;type=" . RACE_TYPE_CLASSIC;
	echo "<input type=button value=\"Click here to see Classical &amp; Qualification races\" onClick=\"document.location='" . $URL . "'\">";
}

?>
