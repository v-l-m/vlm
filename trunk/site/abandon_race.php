
<script type="text/javascript">
function confirmation() {
   var answer = confirm("ABANDON de la course <? echo $usersObj->races->idraces; ?>. Confirmation ?")
	if (answer){
	  alert("Bye bye !")
	  document.abandon.submit();
	} else {
	  alert("Ouf !")
	}
}
</script>
<?php
$fullUsersObj = new fullUsers(getLoginId());

echo "<H2>". $strings[$lang]["warning"]."</H2>";

  $racesObj = new races($fullUsersObj->users->engaged);
  echo 	"<P>Engaged on Race : <B>" . 	$fullUsersObj->users->engaged .  " (" .
					$racesObj->racename    .  ") " .
		"</B></P>";

  // The user may want to unsubscribe from this race
  ?>
  <form name=abandon action="subscribe.php">
     <input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
     <input type="hidden" name="type" value="unsubscribe"/>
     <input type="hidden" name="lang" value="<?php echo $lang?>"/>
     <input type="button" onclick="confirmation();" value="<?php echo $strings[$lang]["unsubscribe"]?>" />
     <p>
     <?php echo $strings[$lang]["wanttosubscribe"]?>
     </p>
  </form>

