<?php
// Now important call to phpMyEdit
if (!isset($pmeinstance)) {
    require_once('../externals/phpMyEdit/extensions/phpMyEdit-mce-cal.class.php');
    new phpMyEdit_mce_cal($opts);
}
include('htmlend.php');
?>
