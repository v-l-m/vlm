      echo "<B>".$strings[$lang]["pilotmode"]."</B>";
      if ( $usersObj->users->pilotmode == 1 )
	echo $strings[$lang]["autopilotengaged"]." : ".$usersObj->users->boatheading." ".$strings[$lang]["degrees"];
      else if ( $usersObj->users->pilotmode == 2 )
	{
	  echo $strings[$lang]["constantengaged"] ;
	  if ( $usersObj->users->pilotparameter > 0 ) echo "+ "; 
	  echo $usersObj->users->pilotparameter;
	}
      else if ( $usersObj->users->pilotmode == 3 )
	echo $strings[$lang]["orthoengaged"];
