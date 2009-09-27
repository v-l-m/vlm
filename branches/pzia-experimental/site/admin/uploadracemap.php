<?php

    include ("htmlstart.php");
    include_once ("functions.php");
    
    if ($_REQUEST["action"] == "upload") {
        echo "<h3>Image reçue</h3>";
        echo "<br />";
        print_r($_REQUEST);
        echo "<br />";
        print_r($_FILES);
        //FIXME: tests here
        $img_blob = file_get_contents ($_FILES['fic']['tmp_name']);
        $req = "REPLACE INTO racesmap ( idraces, racemap ".
                  ") VALUES ( ".
                  "".$_REQUEST['idnewrace']." , ".
                  "'".addslashes($img_blob)."') ";
        $ret = wrapper_mysql_db_query (DBNAME, $req) or die (mysql_error ());
        
    } else {
?>
        <h3>Envoi d'une image</h3>
        <form enctype="multipart/form-data" action="#" method="post">
            <input type="hidden" name="MAX_FILE_SIZE" value="2500000" />
            <input type="hidden" name="action" value="upload" />
            <input type="text" name="idnewrace" size="12" />
            <input type="file" name="fic" maxlength="250" size="50" />
            <input type="submit" value="Envoyer" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>