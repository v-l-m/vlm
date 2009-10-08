<?php
include_once("includes/header.inc");
include_once("config.php");

/**
* ADD  verif form - 11/05/2005 11:20:32 - SkYDuST
*/
?>
<script language="javascript" type="text/javascript">
    function submitbutton() {
      var form = document.myboat;
      var r = new RegExp("[^a-zA-Z0-9_]", "i");

      // do field validation
      if (form.pseudo.value == "") {
        alert( "Vous devez entrer un pseudo !" ); // voir ensuite pour le choix lang - translation !
        return false;
      } else if (r.exec(form.pseudo.value) || form.pseudo.value.length < 3) {
        alert( "caractere non-autorise ou pseudo trop court (mini 4)" ); // voir ensuite pour le choix lang - translation !
        return false;
      // SkYDuST : Je pense qu'il faudrait ajouter un champ email pour pouvoir envoyer des communications (et aussi pour la securite)
      //} else if (form.email.value == "") {
      //  alert( "vous devez entrer votre mail" );
      } else if (form.password.value == "") {
        alert( "Mot de passe obligatoire !" ); // voir ensuite pour le choix lang - translation !
        return false;
      } else if (r.exec(form.password.value)) {
        alert( "votre mot de passe doit contenir des caracteres non-autorises" ); // voir ensuite pour le choix lang - translation !
        return false;
      } else {
        return true;
      }
    }
  </script>
<?
/**
* END ADD  verif form - 11/05/2005 11:20:32 - SkYDuST
*/

echo "<p>";

 echo $strings[$lang]["chooseaccount"];?>

<form onSubmit="return submitbutton();"  action="myboat.php" method="post" name="myboat"> <!-- modif SkYDuST -->
<?echo $strings[$lang]["login_name"]?><br/>
<input size="15" maxlength="15" name="pseudo"/><br/>
<?echo $strings[$lang]["password"]?><br/>
<input size="15" maxlength="15" name="password"/><br/>
<input type="hidden" name="lang" value="<?echo $lang?>"/>
<input type="hidden" name="type" value="create"/>
<!--<input type="button" value="<?echo $strings[$lang]["create"]?>" onclick="submitbutton()" />  doesnot work, check why-->
<input type="submit" />
</form> 


</p>



<?php

include_once("includes/footer.inc");
?>
