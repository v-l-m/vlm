<?php

# $Id: windtable-status.php,v 1.2 2009-01-07 15:09:48 ylafon Exp $
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

$current_time = time();
header('Content-Type: application/xhtml+xml; charset=UTF-8');
header('Cache-Control: max-age=1');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>VLM-C windtable status (GRIB using shared memory)</title>
    <style type="text/css">
      <![CDATA[
             .after {
                 background-color: #99FF99;
                 }
             .before {
                 background-color: #FFFF99;
                 }
             li, li > p {
                 margin-top: 0px;
                 margin-bottom: 0px;
             }
       .hidden {
     display: none;
       }
         ]]>
    </style>
  </head>
  <body>
    <h1>Windtable status check</h1>
    <p id="currenttimeblurb">
      Current time: <span id="currenttime">
      <?php echo gmdate("Y-m-d \T H:i:s", $current_time) ?> GMT
    </span>
    <span class="hidden"><?php echo $current_time ?></span>
    </p>
    <?php
      shm_lock_sem_construct_grib(1);

$nb_grib = get_prevision_count();
if ( $nb_grib == 0 ) {
  printf ("<p id=\"alertgrib\">NO GRIB FOUND</p>");
} else {
    ?>
    <p id="nbgribblurb">
      Found <span id="nbgrib">
      <?php echo $nb_grib ?>
    </span> GRIB entries.
    </p>
    <ol id="gribdates">
      <?php
        for ($i=0; $i < $nb_grib; $i++) { 
          $grib_time = get_prevision_time_index($i);
      ?>
      <li class="gribtime">
        <p id="dump_<?php echo $i ;?>">Grib time for entry number <span class="gribnum"><?php
        if ($i>9) {
          echo $i;
        } else {
          printf ("&nbsp;&nbsp;%d", $i);
        } 
        ?></span>:
          <span id="gribtimeentry_<?php echo $i; ?>" class="<?php echo ($grib_time < $current_time) ? "before" : "after"; ?>">
          <?php echo gmdate("Y-m-d \T H:i:s", $grib_time); ?> GMT
    </span> <span class="hidden" id="gribrawtimeentry_<?php echo $i; ?>"><?php echo $grib_time ?></span>
        </p>
      </li>
      <?php
        }
      ?>
    </ol>
    <?php
      }
shm_unlock_sem_destroy_grib(1);
    ?>
  </body>
</html>
