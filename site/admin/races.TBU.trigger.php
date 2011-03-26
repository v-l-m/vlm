<?php
    require_once("functions.php");
    if (array_key_exists('racetype', $newvals)) {
        $newvals['racetype'] = array_sum(explode(',', $newvals['racetype']));
    }
    return True;
?>
