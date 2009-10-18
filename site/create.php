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
      if ((form.pseudo.value == "") || (form.password.value == "")) {
        alert( "<?php echo $strings[$lang]["loginempty"]; ?>"); 
        return false;
      } else if (r.exec(form.pseudo.value) || form.pseudo.value.length < 3) {
        alert( "<?php echo $strings[$lang]["malformedlogin"]; ?>" ); 
        return false;
      // SkYDuST : Je pense qu'il faudrait ajouter un champ email pour pouvoir envoyer des communications (et aussi pour la securite)
      // FIXME: a fixer le jour on on demandera une confirmation par mail.
      //} else if (form.email.value == "") {
      //  alert( "vous devez entrer votre mail" );
      } else if (r.exec(form.password.value)) {
        alert( "<?php echo $strings[$lang]["malformedpassword"]; ?>" );
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

echo "<div id=\"createbox\">";

    echo "<h2>".$strings[$lang]["chooseaccount"]."</h2>"; ?>

    <form onSubmit="return submitbutton();"  action="myboat.php" method="post" name="myboat">
        <span class="texthelpers"><?echo $strings[$lang]["login_name"]?></span>
        <input size="15" maxlength="15" name="pseudo"/>
        <span class="texthelpers"><?echo $strings[$lang]["password"]?></span>
        <input size="15" maxlength="15" name="password"/>
        <input type="hidden" name="lang" value="<?echo $lang?>"/>
        <input type="hidden" name="type" value="create"/>
        <input type="submit" />
    </form> 
    <p><?php echo nl2br($strings[$lang]["createaccountrules"]); ?></p>
</div>



<?php

include_once("includes/footer.inc");
?>
