<?php
    $PAGETITLE = "Racemap upload";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $idnewrace = intval(get_cgi_var('idnewrace', 0)) ;  
    
    if (get_cgi_var("action") == "upload") {
        if ($idnewrace <1 ) {
            die("<h1>ERROR : Racemap id malformed</h1>");
            }
        if (function_exists("exif_imagetype") and exif_imagetype($_FILES['fic']['tmp_name']) != IMAGETYPE_PNG) {
            die("<h1>ERROR : Not a Png file...</h1>");
        }

        echo "<h3>Image uploaded for race : $idnewrace.</h3>";
        insertRacemap($idnewrace, $_FILES['fic']['tmp_name']);
        insertAdminChangelog(Array("operation" => "Insert", "tab" => "racesmap", "rowkey" => $idnewrace));

        echo "<h3>OK</h3>";
        for ($i = 1 ; $i <= WEBINSTANCE_COUNT ; $i++) {
            $webi = constant("WEBINSTANCE_$i");
            $racemap = "http://$webi/racemap.php?idraces=$idnewrace&force=yes";
            $minimap = "http://$webi/minimap.php?idraces=$idnewrace&force=yes";
            echo "<hr /><h3>Force reload on server : ". $webi . "</h3>";
            //on redimensionne à l'affichage la racemap pour garder l'affichage lisible.
            echo "<a href=\"$racemap\"><img style=\"width:180px ;\" src=\"$racemap\" /></a>";
            echo "<a href=\"$minimap\"><img src=\"$minimap\" /></a>";
        }
    } else {
        
?>
        <h3>Sending a racemap</h3>
        <form enctype="multipart/form-data" action="#" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="2500000" />
            <input type="hidden" name="action" value="upload" />
            Idraces:&nbsp;<input type="text" name="idnewrace" size="12" <?php if ($idnewrace > 0) echo "value=\"$idnewrace\""; ?> /><br />
            Fichier png:&nbsp;<input type="file" name="fic" maxlength="250" size="50" /><br />
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>
