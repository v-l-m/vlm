<?php
include_once("functions.php");

$noHeader=quote_smart($_REQUEST['noHeader']);
$boattype=quote_smart($_REQUEST['boattype']);

header("Cache-Control: no-store, no-cache, must-revalidate");

include_once("config.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <link rel="icon" type="image/png" href="images/site/favicon.png" />
   <title>Virtual Loup-De-Mer - Speedchart</title>
 </head>
 <body>
 <p>
<?php
//find all wind values like 4, 6, 10, 16, 24, 32
$query = "SELECT max(wspeed) from ".$boattype ;
$result = mysql_query($query) or die("Query [$query] failed \n");
$row = mysql_fetch_array($result, MYSQL_NUM);
//  print_r($result2);

//foreach ($row2 as $wspeed)
$pas=15;
$minws=0;
$maxws=$row[0];
//printf("MAXWS=%s",$maxws);
for ($wspeed = $minws; $wspeed<=$maxws; $wspeed+=$pas) {  
    echo "<img src=\"scaledspeedchart.php?boattype=". 
    $boattype . "&amp;minws=". $minws ."&amp;maxws=" . ($wspeed+$pas) . "&amp;pas=2\" alt=\"speedchart\" />";

    $minws=$wspeed+$pas;
}
?>
   </p>
 </body>
</html>
