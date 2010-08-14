<?php
    include_once("includes/header.inc");
    include_once("config.php");

    if ($idraces != 0) {
        $ro = new Races($idraces);
        echo $ro->htmlRaceDescription();
    }

    include_once("includes/footer.inc");
?>
