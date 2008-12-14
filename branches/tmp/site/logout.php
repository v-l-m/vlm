<?
include_once("_include/header.inc");
include_once("config.php");

session_destroy();?>
<p>
<?echo $strings[$lang]["loggedout"]?>
</p>
<?
include_once("_include/footer.inc");
?>
