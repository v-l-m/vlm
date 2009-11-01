<?php 
    include_once("includes/header.inc");
    include_once("config.php");
    include_once("includes/strings.inc");

//    header("Content-type: text/html; charset=utf-8");

    $lang=getCurrentLang();

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
include_once("includes/footer.inc");
?>

