<?php
    $PAGETITLE = "Race import";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $idracefrom = intval($_REQUEST['idracefrom']) ;
    $idraceto = intval($_REQUEST['idraceto']) ;
    $importserver = htmlentities(quote_smart($_REQUEST['importserver']));
    
    if ($_REQUEST["action"] == "import") {
        if ($idracefrom <1  || $idraceto < 1 ) {
            die("<h1>ERROR : idrace malformed</h1>");
            }

        echo "<h3>Starting import of race #<b>$idracefrom</b> from server <b>$importserver</b> to race id #<b>$idraceto</b></h3>";

        //Fetching json        
        $fp = fopen("http://$importserver/ws/raceinfo.php?idrace=$idracefrom","r") or die("<h1>Can't reach server $importserver</h1>"); //lecture du fichier
        $json = "";
        while (!feof($fp)) { //on parcourt toutes les lignes
            $json .= fgets($fp, 4096); // lecture du contenu de la ligne
        }

        //Do the import work
        print_r(json_decode($json, True));

//        insertAdminChangelog(Array("operation" => "Import", "tab" => "racesmap", "rowkey" => $idnewrace));
 
        //We're done.
        echo "<h3>OK</h3>";

    } else {
        
?>
        <h3>Ready to import a race</h3>
        <form action="#" method="post">
            <input type="hidden" name="action" value="import" />
            From server:&nbsp;<input type="text" name="importserver" size="50" value="testing.virtual-loup-de-mer.org" /><br />
            From Idrace:&nbsp;<input type="text" name="idracefrom" size="12"/><br />
            To Idrace:&nbsp;<input type="text" name="idraceto" size="12"/><br />
            <input type="submit" value="Import" />
        </form>
<?php
    }
    
    include ("htmlend.php");
?>
