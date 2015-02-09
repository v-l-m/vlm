<?php
  include_once("config.php");
  $idraces=htmlentities(quote_smart($_REQUEST['race']));
  if (  round($idraces) == 0 ) {
     echo "usage : http://virtual-loup-de-mer.org?race=X\n";
     echo "\nX = numero d'une course";
     exit;
  }

  list ($num_arrived , $num_racing, $num_engaged) = getNumOpponents($idraces);
  printf ('NA=%d;', $num_arrived) ;
  printf ('NR=%s;', $num_racing) ;
  printf ('NE=%s;', $num_engaged) ;
  printf ("\n");

?>
  
