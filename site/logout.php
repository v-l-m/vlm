<?php
include_once("includes/header.inc");
include_once("config.php");

session_destroy();?>
<p>
<?echo getLocalizedString("loggedout")?>
</p>
<?php
include_once("includes/footer.inc");
?>
