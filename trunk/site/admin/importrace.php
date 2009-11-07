<?php
    $PAGETITLE = "Race import";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $idracefrom = intval($_REQUEST['idracefrom']) ;
    $idraceto = intval($_REQUEST['idraceto']) ;
    $importserver = htmlentities(quote_smart($_REQUEST['importserver']));
    
    function sqlit(&$import, $t, $type = 'int', $coma = true) {
        switch ($type) {
            case 'string' :
                $ret = "'".addslashes($import[$t])."'";
                break;
            case 'int' :
            default :
                $ret = intval($import[$t]);
        }
        if ($coma) {
            $ret .= ", ";
        }
        return $ret;
    }
    
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
        $import = json_decode($json, True);
        //print_r($import);
        if (intval($import) == 0) die("<h1>This race doesn't seem to exist...</h1>");

        $sqlraces = "INSERT INTO races (idraces, racename, started, deptime, startlong, startlat, boattype, closetime, racetype, firstpcttime, ".
                    "depend_on, qualifying_races, idchallenge, coastpenalty, bobegin, boend, maxboats, theme, vacfreq) ".
                    " VALUES (".$idraceto.", ".
                    sqlit($import, "racename", 'string').
                    sqlit($import, "started").
                    sqlit($import, "deptime").
                    sqlit($import, "startlong").
                    sqlit($import, "startlat").
                    sqlit($import, "boattype", 'string').
                    sqlit($import, "closetime").
                    sqlit($import, "racetype").
                    sqlit($import, "firstpcttime").
                    sqlit($import, "depends_on").
                    sqlit($import, "qualifying_races",'string').
                    sqlit($import, "idchallenge",'string').
                    sqlit($import, "coastpenalty").
                    sqlit($import, "bobegin").
                    sqlit($import, "boend").
                    sqlit($import, "maxboats").
                    sqlit($import, "theme", 'string').
                    sqlit($import, "vacfreq", 'int', false).
                    " );";
        print "SQL:".$sqlraces."<br />";

        $races_waypoints = $import['races_waypoints'];
        foreach ($races_waypoints as $wporder => $wpmisc) {
            $sqlrwp = "INSERT INTO races_waypoints (idwaypoint, idraces, wporder, laisser_au, wptype) ".
                    " VALUES (".sprintf("%d%02d", $idraceto, $wpmisc['wporder']).", ".
                    $idraceto.", ".
                    sqlit($wpmisc, "wporder").
                    sqlit($wpmisc, "laisser_au").
                    sqlit($wpmisc, "wptype", 'string', false).
                    " );";
            print "SQL:".$sqlrwp."<br />";
            
            $sqlwp = "INSERT INTO waypoints (idwaypoint, latitude1, longitude1, latitude2, longitude2, libelle, maparea) ".
                    " VALUES (".sprintf("%d%02d", $idraceto, $wpmisc['wporder']).", ".
                    sqlit($wpmisc, "latitude1").
                    sqlit($wpmisc, "longitude1").
                    sqlit($wpmisc, "latitude2").
                    sqlit($wpmisc, "longitude2").
                    sqlit($wpmisc, "libelle", 'string').
                    sqlit($wpmisc, "maparea", 'int', false).
                    " );";
            print "SQL:".$sqlwp."<br />";

        }


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
