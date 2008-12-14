<?
include_once("includes/header.inc");
include_once("config.php");

session_destroy();?>
<p>
<?echo $strings[$lang]["loggedout"]?>
</p>
<?
include_once("includes/footer.inc");
?>
