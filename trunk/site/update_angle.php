<?php
//cookie for expert/beginner mode
/*
$expertcookie=htmlentities($_GET['expertcookie']);
if ($expertcookie == "yes")
{
  setcookie("expertcookie", 'yes', time()+3600*24*365); //expire in one year

}
else
{
  setcookie("expertcookie", 'no', time()+3600*24*365); //expire in one year
}
*/
//must be after cookies creation
include_once("header.inc");
include_once("config.php");

//echo "<p>";

if ( preg_match('/[#%]/', $pilotmode ) ||
     preg_match('/[#%]/', $boatheading ) ||
     preg_match('/[#%]/', $pilotparameter ) ) {

      printf ("<H1>This is not a nice thing to try this.</H1>");
      printf ("<H2>Your IP address is : " . $_SERVER["REMOTE_ADDR"] . "</H2>\n");
      sleep (1);
      printf ("<H2>It has been logged. Don't try this again.</H2>\n");
      exit;
}

if ($idusers != 0 )
{

  if ( $idusers ==  getLoginId() ) {
      if ( $boatheading >= 360 ) $boatheading -= 360;
      if ( $boatheading < 0 ) $boatheading += 360;

      if ( $pilotparameter >= 360 ) $pilotparameter -= 360;
      if ( $pilotparameter > 180 ) $pilotparameter -= 360;
      if ( $pilotparameter < -180 ) $pilotparameter += 360;

      // Tout ça pour mettre à jour la ligne de la table users... on simplifie
      $fullUsersObj = new fullUsers($idusers);
      $fullUsersObj->writeNewheading($pilotmode, $boatheading, $pilotparameter);


      echo "<H1 align=center>&nbsp;<BR>&nbsp;<BR>" . $strings[$lang]["angleupdated"] . "<BR>&nbsp;<BR>&nbsp;</H1>";
  } else {
      printf ("<H1>This is not a nice thing to try this.</H1>");
      printf ("<H2>Your IP address is : " . $_SERVER["REMOTE_ADDR"] . "</H2>\n");
      sleep (1);
      printf ("<H2>It has been logged. Don't try this again.</H2>\n");
  }
 
}

//print_r($_COOKIE);
//echo "</p>";
include_once("footer.inc");
?>
