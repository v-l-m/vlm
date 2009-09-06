<?php
include_once('config.php');
include_once('vlmc.php');

$current_time = time();
$PAGETITLE="Polars status check";
include("../includes/header-status.inc");
?>

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
<ol>
<?php
for ($i=0; $i<$nb_polars; $i++) {
  $pname = get_polar_name_index_context($temp_vlmc_context, $i); 
  echo "  <li><a href=\"/speedchart.php?boattype=\"".$pname."\">";
  echo "".$pname."</a></li>";
}
shm_unlock_sem_destroy_polar_context($temp_vlmc_context, 1);  
?>
</ol>
</body>
</html>
