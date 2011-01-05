<?php
    include_once('config.php');

    $current_time = time();
    $PAGETITLE="VLM Status";
    include("../includes/header-status.inc");
?>
    <p id="currenttimeblurb">
      Current time: <span id="currenttime">
      <?php echo gmdate("Y-m-d \T H:i:s", $current_time) ?> GMT
    </span>
    <span class="hidden"><?php echo $current_time ?></span>
    </p>

  </body>
</html>
