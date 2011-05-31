<div class="info">

<?php
    
    echo "<h1 class=\"error\">".getLocalizedString("authfailed")."</h1>";

    if (checkAccount($pseudo, $password)) {
        echo "<span class=\"warnmessage\">";
        echo "<p>".getLocalizedString("You seem to try to use an old account with boat")." <b>".$pseudo."</b></p>";
        echo "<p>".getLocalizedString("You should do following steps")."&nbsp;:</p>";
        echo "<ol>";
        echo "<li><a href=\"http://wiki.virtual-loup-de-mer.org/index.php/Le_rattachement_d%27un_bateau_ant%C3%A9rieur_%C3%A0_v0.14\">".getLocalizedString("Read the documentation")."</a></li>";
        echo "<li><a href=\"create_player.php\">".getLocalizedString("Create a new account")."</a></li>";
        echo "<li><a href=\"attach_owner.php\">".getLocalizedString("You may then attach the pre-existing boat")."</a></li>";
        echo "</ol>";
        echo "</span>";
        echo "<hr />";
    }
    echo "<p><a href=\"reset_password.php\">".getLocalizedString("You can recover a lost password")."</a></p>";
    echo "<p><a href=\"create_player.php\">".getLocalizedString("Create a new account")."</a> ".getLocalizedString("if you don't have one")."</p>";
?>

</div>
