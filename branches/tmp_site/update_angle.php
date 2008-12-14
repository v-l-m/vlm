<?php
include_once("_include/header.inc");
include_once("config.php");

// Controle de saisie / soumission
$pilotmodes=array("autopilot", "windangle", "orthodromic", "bestvmg", "bestspeed");
if ( ! in_array($pilotmode, $pilotmodes) ) {
      printf ("<h1>PILOTMPDE = $pilotmode IS UNSUPPORTED.");
      printf ("<h2>Your IP address is : " . $_SERVER["REMOTE_ADDR"] . "</h2>\n");
      printf ("<h2>It has been logged. Don't try this again.</h2>\n");
      sleep (1);
      exit;
} 

if (
     strspn($boatheading, "-+.0123456789") != strlen($boatheading)  ||
     strspn($pilotparameter, "-+.0123456789") != strlen($pilotparameter)  ) {

      printf ("<h1>This is not a nice thing to try this.</h1>");
      printf ("<h2>Your IP address is : " . $_SERVER["REMOTE_ADDR"] . "</h2>\n");
      sleep (1);
      printf ("<h2>It has been logged. Don't try this again.</h2>\n");
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


      echo "<h1 align=\"center\">&nbsp;<br />&nbsp;<br />" . $strings[$lang]["angleupdated"] . "<br />&nbsp;<br />&nbsp;</h1>";
  } else {
      printf ("<h1>This is not a nice thing to try this.</h1>");
      printf ("<h2>Your IP address is : " . $_SERVER["REMOTE_ADDR"] . "</h2>\n");
      sleep (1);
      printf ("<h2>It has been logged. Don't try this again.</h2>\n");
  }
 
}

//print_r($_COOKIE);
//echo "</p>";
include_once("_include/footer.inc");
?>
