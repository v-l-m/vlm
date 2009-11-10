<?php

include_once("functions.php");

$noHeader=quote_smart($_REQUEST['noHeader']);
$boattype=quote_smart($_REQUEST['boattype']);
$format=quote_smart($_REQUEST['format']);

include_once("config.php");

// first the headers
switch ($format) {
case "pol":
  header("Content-Type: text/pol");
  header('Content-Disposition: attachment; filename="' . $boattype . '.pol"');
  break;
case "csv":
  header("Content-Type: text/csv");
  header('Content-Disposition: attachment; filename="' . $boattype . '.csv"');
  break;
default:
  header("Cache-Control: max-age=300");
  header("Content-Type: text/html;charset=utf8");
  break;
}

// then the content
switch ($format) {
case "pol":
  printf ("TWA\\TWS\t");
  for ($wspeed = 0; $wspeed <= 60 ; $wspeed+=5) {
    printf ("%d\t", $wspeed);
  }
  printf ("\n");
  // Display .pol
  for ($wheading = 0; $wheading <= 180 ; $wheading+=5) {  
    printf ("%d\t", $wheading);
    for ($wspeed = 0; $wspeed <= 60 ; $wspeed+=5) {
      $boatspeed = findboatspeed ($wheading, $wspeed, $boattype);
      printf ("%.2f\t", $boatspeed);
    }
    printf ("\n");
  }
  break;
case "csv":
  printf ("TWA\\TWS");
  for ($wspeed = 0; $wspeed <= 60 ; $wspeed+=5) {
    printf (";%d", $wspeed);
  }
  printf ("\n");
  // Display .pol
  for ($wheading = 0; $wheading <= 180 ; $wheading+=5) {  
    printf ("%d", $wheading);
    for ($wspeed = 0; $wspeed <= 60 ; $wspeed+=5) {
      $boatspeed = findboatspeed ($wheading, $wspeed, $boattype);
      printf (";%.2f", $boatspeed);
    }
    printf ("\n");
  }
  break;
default:
  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="icon" type="image/png" href="images/site/favicon.png" />
    <title>Virtual Loup-De-Mer - Speedchart</title>
  </head>
  <body>
';
  echo "<h3>";
  echo "<a href=\"".DOC_SERVER_URL.$boattype."\">".$boattype."</a>";       
  echo " - <a href=\"/speedchart.php?boattype=".$boattype."&amp;format=pol\">(pol)</a>";
  echo " - <a href=\"/Polaires/".$boattype.".csv\">(csv)</a>";
  echo "</h3><p>";
  
  $pas=15;
  $minws=0;
  $maxws=60;
  //printf("MAXWS=%s",$maxws);
  for ($wspeed = $minws; $wspeed<=$maxws; $wspeed+=$pas) {  
    echo "<img src=\"scaledspeedchart.php?boattype=". 
      $boattype . "&amp;minws=". $minws ."&amp;maxws=" . ($wspeed+$pas) . "&amp;pas=2\" alt=\"speedchart\" />";
    
    $minws=$wspeed+$pas;
  }
  echo '</p>
  </body>
</html>';
}
?>
