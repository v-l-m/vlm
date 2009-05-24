<?php

include_once("functions.php");

$noHeader=quote_smart($_REQUEST['noHeader']);
$boattype=quote_smart($_REQUEST['boattype']);
$format=quote_smart($_REQUEST['format']);

include_once("config.php");

if ( $format != "pol" ) {

        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo '
              <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
              <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
               <head>
   
                  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
                  <link rel="icon" type="image/png" href="images/site/favicon.png" />
                  <title>Virtual Loup-De-Mer - Speedchart</title>
               </head>
               <body>
               <p>
              ';
} else {
        header("Content-Type: text/pol");
        header('Content-Disposition: attachment; filename="' . $boattype . '.pol"');
}


//find all wind values like 4, 6, 10, 16, 24, 32
$query = "SELECT max(wspeed) from ".$boattype ;
$result = mysql_query($query) or die("Query [$query] failed \n");
$row = mysql_fetch_array($result, MYSQL_NUM);
//  print_r($result2);

// On sort un .pol ou les polaires graphiques
if ( $format == "pol" ) {

       printf ("TWA\\TWS\t");
       for ($wspeed = 0; $wspeed <= 40 ; $wspeed+=5) {
             printf ("%d\t", $wspeed);
       }
       printf ("\n");
       // Display .pol
       for ($wheading = 0; $wheading <= 180 ; $wheading+=5) {  

               printf ("%d\t", $wheading);
               for ($wspeed = 0; $wspeed <= 40 ; $wspeed+=5) {
                      $boatspeed = findboatspeed ($wheading, $wspeed, $boattype );
                      printf ("%.2f\t", $boatspeed);
               }
               printf ("\n");

       }

} else {

       $pas=15;
       $minws=0;
       $maxws=$row[0];
       //printf("MAXWS=%s",$maxws);
       for ($wspeed = $minws; $wspeed<=$maxws; $wspeed+=$pas) {  
            echo "<img src=\"scaledspeedchart.php?boattype=". 
            $boattype . "&amp;minws=". $minws ."&amp;maxws=" . ($wspeed+$pas) . "&amp;pas=2\" alt=\"speedchart\" />";

            $minws=$wspeed+$pas;
       }
}


if ( $format != "pol" ) {
     echo '
          </p>
          </body>
          </html>
          ';
}
?>
