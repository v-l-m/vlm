<?php
    $PAGETITLE = "Racemap upload";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $idnewrace = intval($_REQUEST['idnewrace']) ;   
    
    if ($_REQUEST["action"] == "upload") {
        if ($idnewrace <1 ) {
            die("<h1>Error, racemap malformed</h1>");
            }
        echo "<h3>Image reçue pour la course $idnewrace.</h3>";
        //FIXME: tests here
        $img_blob = file_get_contents ($_FILES['fic']['tmp_name']);
        $req = "REPLACE INTO racesmap ( idraces, racemap ".
                  ") VALUES ( ".
                  "".$idnewrace." , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
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
            Idraces:&nbsp;<input type="text" name="idnewrace" size="12" /><br />
            Fichier jpeg:&nbsp;<input type="file" name="fic" maxlength="250" size="50" /><br />
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>