<?php 
include_once("includes/header.inc");
include_once("config.php");

?>

<h3><?php echo $strings[$lang]["author"];?></h3>
<p><?php echo $strings[$lang]["authorinfo"];?></p>

<h3><?php echo $strings[$lang]["mainteneur"];?></h3>
<p><?php echo $strings[$lang]["mainteneurinfo"];?></p>


<!--
	<h3><?php  echo $strings[$lang]["contact"];?></h3>
-->

	<h3><?php  echo $strings[$lang]["contributors"];?></h3> 
	<p><?php  echo $strings[$lang]["contributorstext"];?></p>

<h3><?php   echo $strings[$lang]["howtohelp"];?></h3> 
<?php   echo $strings[$lang]["howtohelptext"];?>


<!--
<h3>Doc</h3>
<p>
	<a href="http://loupdev.tuxfamily.org/wiki/wakka.php?wiki=ChangeLog">CHANGELOG</a>
	<a href="http://loupdev.tuxfamily.org/wiki/wakka.php?wiki=ListeDesTachesAutresVersions">ROADMAP</a> 
	<a href="http://loupdev.tuxfamily.org/wiki/wakka.php?wiki=InstallationVLM">INSTALL</a>
	<a href="HOWTOREAD_GRIB_FILE">HOWTOREAD_GRIB_FILE</a>
	<a href="COPYING">COPYING</a>
</p>
-->

<!--
	<h3><?php  echo $strings[$lang]["sourcecode"];?></h3>
	<p><?php  echo $strings[$lang]["sourcecodetext"];?> <a href="vlm.0.4.2.tar.gz">package source</a></p>
-->


<h3><?php  echo $strings[$lang]["webstandard"];?></h3>
<p>
<?echo $strings[$lang]["webstandardtext"];?>
</p>

<h3><?php  echo $strings[$lang]["tools"];?></h3>
<p>
<?php echo $strings[$lang]["toolstext"];?>
</p>

<?php 
include_once("includes/footer.inc");
?>
