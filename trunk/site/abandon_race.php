    <div id="abandon">

<script type="text/javascript">
  function confirmation() {
      var answer = confirm("<?php echo $strings[$lang]["unsubscribe"]; echo $usersObj->races->idraces; ?>. Confirmation ?")
      if (answer){
          alert("Bye bye !")
          document.abandon.submit();
      } else {
          alert("Ouf !")
      }
  }
</script>
<?php

  $fullUsersObj = new fullUsers($users->idusers, $users);

  echo "<p>". $strings[$lang]["warning"]."</p>";

  $racesObj = $fullUsersObj->races;
  echo   "<p>Engaged on race : <b>" .   $fullUsersObj->users->engaged .  " (" .
          $racesObj->racename    .  ") " .
    "</b>";

  // The user may want to unsubscribe from this race
  ?>
  <form id="abandonform" name="abandon" action="subscribe.php">
     <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
     <input type="hidden" name="type" value="unsubscribe"/>
     <input type="hidden" name="lang" value="<?php echo $lang?>"/>

     <input type="button" onclick="confirmation();" value="<?php echo $strings[$lang]["unsubscribe"]; ?>" />
  </form>
  </p>
</div>