<?php
    $PAGETITLE = "Racemap upload";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $idnewrace = $_REQUEST['idnewrace'] ;   
    
    if ($_REQUEST["action"] == "upload") {
        echo "<h3>Image reçue pour la course $idnewrace.</h3>";
        //FIXME: tests here
        $img_blob = file_get_contents ($_FILES['fic']['tmp_name']);
        $req = "REPLACE INTO racesmap ( idraces, racemap ".
                  ") VALUES ( ".
                  "".$idnewrace." , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
        echo "<h3>OK</h3>";
        echo "<img src=\"/racemap.php?idraces=$idnewrace&force=yes\" />";
        echo "<img src=\"/minimap.php?idraces=$idnewrace&force=yes\" />";
    } else {
?>
        <h3>Envoi d'une image</h3>
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