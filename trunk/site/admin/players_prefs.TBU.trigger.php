<?php
    require_once("functions.php");
    print_r($newvals);
    if (array_key_exists('permissions', $newvals)) {
        $newvals['permissions'] = array_sum(explode(',', $newvals['permissions']));
    }
    print_r($newvals);    
    return True;
?>
