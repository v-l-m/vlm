<?php

# $Id: windtable-status.php,v 1.1 2009-01-07 14:36:37 ylafon Exp $
#
# (c) 2008 by Yves Lafon
#      See COPYING file for copying and redistribution conditions.
#
#      This program is free software you can redistribute it and/or modify
#      it under the terms of the GNU General Public License as published by
#      the Free Software Foundation version 2 of the License.
#
#      This program is distributed in the hope that it will be useful,
#      but WITHOUT ANY WARRANTY without even the implied warranty of
#      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#      GNU General Public License for more details.
#
# Contact: <yves@raubacapeu.net>

include("vlmc.php");

$global_vlmc_context = new vlmc_context();
global_vlmc_context_set($global_vlmc_context);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>VLM-C windtable status (GRIB using shared memory)</title>
  </head>
  <body>
    <p id="currenttime">
      <?php echo gmdate("Y-m-d:H:i:s", time()) ?>
    </p>
    <?php
      shm_lock_sem_construct_grib(1);

$nb_grib = get_prevision_count();
if ( $nb_grib == 0 ) {
  printf ("<p id=\"alertgrib\">NO GRIB FOUND</p>");
} else {
    ?>
    <p id="nbgrib">
      <?php echo $nb_grib ?>
    </p>
    <ol id="gribdates">
      <?php
	for ($i=0; $i < $nb_grib; $i++) { 
      ?>
      <li class="gribtime" id="<?php echo "gribtimeentry_".$i ?>">
      <?php echo  gmdate("Y-m-d:H:i:s", time()) ?>
      </li>
    <?php
	}
    ?>
    </ol>
  <?php
	shm_unlock_sem_destroy_grib(1);
  ?>
  </body>
</html>
