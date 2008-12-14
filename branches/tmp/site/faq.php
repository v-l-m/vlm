<?
include_once("_include/header.inc");
include_once("config.php");
?>

<h1>
<?echo $strings[$lang]["faqtitle"]?>
</h1>

<?for ($i =1; $i<20; $i++)
{?>
<h3>
   <?echo $strings[$lang]["q".$i];?>
</h3>

<p>
   <?echo $strings[$lang]["a".$i];?>
</p>

<?
   }
include_once("_include/footer.inc");
?>
