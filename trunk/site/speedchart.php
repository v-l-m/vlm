<?php
include_once("functions.php");

$noHeader=quote_smart($_REQUEST['noHeader']);
$boattype=quote_smart($_REQUEST['boattype']);

header("Cache-Control: no-store, no-cache, must-revalidate");

include_once("config.php");

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
for ($wspeed = $minws; $wspeed<=$maxws; $wspeed+=$pas) 
{  
   echo "<IMG src=scaledspeedchart.php?boattype=".$boattype."&minws=".$minws."&maxws=".($wspeed+$pas) . "&pas=2>";
   $minws=$wspeed+$pas;
}


