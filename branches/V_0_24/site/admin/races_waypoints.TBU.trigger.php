<?php
    require_once("functions.php");
    if (array_key_exists('wpformat', $newvals)) {
        $newvals['wpformat'] = array_sum(explode(',', $newvals['wpformat']));
    }
    return True;
?>
