<?php
include_once("vlmc.php");

$coast_xingratio = new doublep();
$coast_xinglat   = new doublep();
$coast_xinglong  = new doublep();

echo "\n\tChecking for coast crossing (vlm-c)\n ";
echo "\tUsing: ".GSHHS_FILENAME."\n ";

$crosses_the_coast = VLM_check_cross_coast($latAvant, $lonAvant, $latApres, $lonApres, 
					   $coast_xinglat, $coast_xinglong, $coast_xingratio);

if ($crosses_the_coast) {
  echo "\t*** YES player " . $fullUsersObj->users->idusers . " CROSSED (vlmc), ";
  echo "\n\t\tCoast has been crossed : \n\t\t\t" ; 
  printf ("BOAT : %f,%f <----> %f,%f",
	  $latAvant/1000,$longApres/1000 , $latAvant/1000,$longApres/1000);
  
  echo "\n\t\t\tEncounterCoordinates " . 
    doublep_value($coast_xinglat)/1000 . ", " . doublep_value($coast_xinglong)/1000 . 
    "\n\nGoogleMap http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
    doublep_value($coast_xinglat)/1000 . "," . doublep_value($coast_xinglong)/1000 .
    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";
  
  /* NOTE the encounter coordinates are the real ones */
  
  echo "\nVLMMAP http://s9.virtual-loup-de-mer.org/mercator.img.php?idraces=" . $fullUsersObj->users->engaged ;
  echo "&lat=" . $latAvant/1000;  
  echo "&long=" .$lonAvant/1000;
  echo "&maparea=18&tracks=on&age=6";
  echo "&list=" . $fullUsersObj->users->idusers ;
  echo "&x=1000&y=600&proj=mercator&text=right"; /* ahem, we have a point instead of the segment now :) */
  echo "&seg1=".$latAvant/1000 . "," . $longAvant/1000 . ":" . $latApres/1000 . "," . $longApres/1000;
  echo "&seg2=".doublep_value($coast_xinglat)/1000 . "," . doublep_value($coast_xinglong)/1000 . ":" . doublep_value($coast_xinglat)/1000 . "," . doublep_value($coast_xinglong)/1000;
  echo "\n\n";
  /*
    echo "\n\t ==> Position Avant " . 
    $latAvant/1000 . ", " . $lonAvant/1000 . 
    "\n http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
    $latAvant/1000 . "," . $lonAvant/1000 .
    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";
    
    echo "\n\t ==> Position Apres " . 
    $latApres/1000 . ", " . $lonApres/1000 . 
    "\n http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=".
    $latApres/1000 . "," . $lonApres/1000 .
    "&ie=UTF8&spn=0.0191,0.082998&t=p&z=11&iwloc=addr. \n";
  */

  // We will stop the player after checking the waypoint, but first we need to 
  // compute the real endpoint (using linear interpolation)
  // We can tune the 0.9 to whatever we want, to avoid putting the boat on the line
  // and have rounding errors having fun with us.
  $latApres = $latAvant + ($latApres - $latAvant) * 0.9 * doublep_value($coast_xingratio);
  $lonApres = $lonAvant + ($lonApres - $lonAvant) * 0.9 * doublep_value($coast_xingratio);
}
?>
