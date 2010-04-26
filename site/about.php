<?php 
include_once("includes/header.inc");
include_once("config.php");

?>

<h3><?php echo getLocalizedString("author");?></h3>
<p><?php echo getLocalizedString("authorinfo");?></p>

<h3><?php echo getLocalizedString("mainteneur");?></h3>
<p><?php echo getLocalizedString("mainteneurinfo");?></p>


<!--
  <h3><?php  echo getLocalizedString("contact");?></h3>
-->

  <h3><?php  echo getLocalizedString("contributors");?></h3> 
  <p><?php  echo getLocalizedString("contributorstext");?></p>

<h3><?php   echo getLocalizedString("howtohelp");?></h3> 
<?php   echo getLocalizedString("howtohelptext");?>


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
  <h3><?php  echo getLocalizedString("sourcecode");?></h3>
  <p><?php  echo getLocalizedString("sourcecodetext");?> <a href="vlm.0.4.2.tar.gz">package source</a></p>
-->


<h3><?php  echo getLocalizedString("webstandard");?></h3>
<p>
<?echo getLocalizedString("webstandardtext");?>
</p>

<h3><?php  echo getLocalizedString("tools");?></h3>
<p>
<?php echo getLocalizedString("toolstext");?>
</p>

<?php 
include_once("includes/footer.inc");
?>
