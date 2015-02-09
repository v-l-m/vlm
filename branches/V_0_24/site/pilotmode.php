      echo "<b>".getLocalizedString("pilotmode")."</b>";
      if ( $usersObj->users->pilotmode == 1 )
  echo getLocalizedString("autopilotengaged")." : ".$usersObj->users->boatheading." ".getLocalizedString("degrees");
      else if ( $usersObj->users->pilotmode == 2 )
  {
    echo getLocalizedString("constantengaged") ;
    if ( $usersObj->users->pilotparameter > 0 ) echo "+ "; 
    echo $usersObj->users->pilotparameter;
  }
      else if ( $usersObj->users->pilotmode == 3 )
  echo getLocalizedString("orthoengaged");
