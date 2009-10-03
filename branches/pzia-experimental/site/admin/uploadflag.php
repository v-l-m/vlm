<?php
    $PAGETITLE = "Flag upload";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if ($_REQUEST["action"] == "upload") {
        $idflags = $_REQUEST['idflags'] ;   

        echo "<h3>Image reçue pour le pavillon $idflags.</h3>";
        //FIXME: tests here
        if (insertFlag($idflags, $_FILES['fic']['tmp_name'])) {
            echo "<h3>Insert OK</h3>";
            //flagimg.php ne sera pas utilisé en direct par le reste du code (appelé trop souvent)
            // donc on se reposera sur l'existence du cache, quitte à donner les moyens de le forcer (par serveur).
            for ($i = 1 ; $i <= WEBINSTANCE_COUNT ; $i++) {
                $webi = constant("WEBINSTANCE_$i");
                $flagurl = "http://$webi/flagimg.php?idflags=$idflags&force=yes";
                echo "<hr /><h3>Force reload on server : ". $webi . "</h3>";
                //on redimensionne à l'affichage la racemap pour garder l'affichage lisible.
                echo "<a href=\"$flagurl\"><img src=\"$racemap\" /></a>";
                } 
        } else {
            echo "ERROR: Can't insert the flag named $idflags";
        }
    } else {
?>
        <h3>Envoi d'une image</h3>
        <form enctype="multipart/form-data" action="#" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="250000" />
            <input type="hidden" name="action" value="upload" />
            ID of flag (insert or replace):&nbsp;<input type="text" name="idflags" size="20" maxlength="50" /><br />
            Png filename (30x20):&nbsp;<input type="file" name="fic" maxlength="250" size="50" /><br />
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>