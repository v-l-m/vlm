<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
include_once("config.php");
include_once("includes/strings.inc");

$lang=htmlentities(quote_smart($_REQUEST['lang']));
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<? echo $lang ?>">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style/new-style.css" />
  <link rel="shortcut icon" type="image/png" href="images/site/favicon.png" />
  <title>Virtual Loup-De-Mer </title>
</head>
<body>
<?php
if ( htmlentities(quote_smart($_REQUEST['type'])) == 'palmares' ) {
  $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
  echo "<h4>" ; printf ($strings[$lang]["palmares"],$idusers); echo "</h4>";
  displayPalmares($idusers);
?>
<form>
  <input type="button" value="Close" onclick="javascript:self.close();" />
</form>
<?php
}
//include_once("includes/footer.inc");
?>
</body>
</html>