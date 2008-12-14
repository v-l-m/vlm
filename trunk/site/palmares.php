<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="styles/new-style.css" />
<link rel="shortcut icon" type="image/png" href="images/site/favicon.png" />
<title>Virtual Loup-De-Mer </title>
<?php
//include_once("includes/header.inc");
include_once("config.php");
include_once("includes/strings.inc");

$lang=htmlentities(quote_smart($_REQUEST['lang']));
if ( htmlentities(quote_smart($_REQUEST['type'])) == 'palmares' ) {
        $idusers=htmlentities(quote_smart($_REQUEST['idusers']));
	echo "<H4>" ; printf ($strings[$lang]["palmares"],$idusers); echo "</H4>";
	displayPalmares($idusers);
	echo "<INPUT TYPE=BUTTON VALUE=\"Close\" ONCLICK=\"javascript:self.close();\">";
}

//include_once("includes/footer.inc");

?>
