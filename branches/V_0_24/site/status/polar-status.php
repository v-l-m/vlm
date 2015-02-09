<?php
include_once('config.php');
include_once('vlmc.php');
include_once("functions.php");

$current_time = time();
$PAGETITLE="Polars status check";
include("../includes/header-status.inc");
?>
<p>Please take a look at the <a href="http://wiki.virtual-loup-de-mer.org/index.php/Les_bateaux">boat descriptions</a>.</p>

<p id="currenttimeblurb">
  Current time: <span id="currenttime">
  <?php echo gmdate("Y-m-d \T H:i:s", $current_time) ?> GMT
</span>
<span class="hidden"><?php echo $current_time ?></span>
</p>
<?php

  $temp_vlmc_context = new vlmc_context();

shm_lock_sem_construct_polar_context($temp_vlmc_context, 1); 
$nb_polars = get_nb_polars_context($temp_vlmc_context);
?>
<p>Currently in use: <?php echo $nb_polars ?> polars</p>
<table class="polartable">
<tr><th>Boat Type</th><th>Description</th><th>pol</th><th>csv</th></tr>
<?php
for ($i=0; $i<$nb_polars; $i++) {
  echo "  <tr>\n";
  $pname = get_polar_name_index_context($temp_vlmc_context, $i); 
  echo "    <td class=\"racename\"><a href=\"".speedchart_link($pname, "www")."\">";
  echo "".$pname."</a></td>\n";
  echo "    <td><a href=\"".DOC_SERVER_URL.$pname."\">";
  echo "Wiki</a></td>\n";
  echo "    <td><a href=\"".speedchart_link($pname, "pol")."\">";
  echo "(pol)</a></td>\n";
  echo "    <td><a href=\"/Polaires/".get_polar_name2csv($pname)."\">(csv)</a></td>\n";
  echo "  </tr>\n";
}
shm_unlock_sem_destroy_polar_context($temp_vlmc_context, 1);  
?>
</table>
</body>
</html>
