<?php
    $PAGETITLE = "Flag upload";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if ($_REQUEST["action"] == "upload") {
        $idflag = $_REQUEST['idflags'] ;   

        echo "<h3>Image reçue pour le pavillon $idflags.</h3>";
        //FIXME: tests here
        $img_blob = file_get_contents ($_FILES['fic']['tmp_name']);
        $req = "REPLACE INTO flags ( idflags, flag ".
                  ") VALUES ( ".
                  "".$idflags." , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
        echo "<h3>OK</h3>";
        //flagimg.php ne sera pas utilisé en direct par le reste du code (appelé trop souvent)
        // donc on se reposera sur l'existence du cache, quitte à donner les moyens de le forcer (par serveur). 
        echo "<img src=\"/flagimg.php?idflagss=$idflags&force=yes\" />";
    } else {
?>
        <h3>Envoi d'une image</h3>
        <form enctype="multipart/form-data" action="#" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="250000" />
            <input type="hidden" name="action" value="upload" />
            ID of flag:&nbsp;<input type="text" name="idnewrace" size="12" /><br />
            Fichier png (30x20):&nbsp;<input type="file" name="fic" maxlength="250" size="50" /><br />
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>