<?php
    $PAGETITLE = "Race import";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    //FIXME: could be in functions.php
    //escape and quote if type is "string"
    //null => 0 if type is int
    function sqlit(&$import, $t, $type = 'int', $coma = true, $default = null) {
        if (!is_null($default)) $import[$t] = $default;
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

    //wrapper to execute and/or display the sql
    function exec_sql($sql, $printsql, $dryrun) {
        if ($printsql) print $sql."\n";
        if ($dryrun) {
            $res = true;
        } else {
            $res = wrapper_mysql_db_query_writer($sql);
        }

        if (!$res) {
            if (!$printsql) print "QUERY ERROR : $sql\n";
            die("MYSQL ERROR, import stopped.");
        } else if ($printsql && !$dryrun) {
            print "<b>=> OK</b>\n";
        }
    }
    
    
    //given a table name and a where clause, do a select count() and fail if there are matching rows
    function check_unicity($table, $where, $message = "") {
        $sql = "SELECT count(*) as uniques FROM $table WHERE $where";
        $res = wrapper_mysql_db_query($sql) or die("ERROR testing '$sql'");
        $row = mysql_fetch_assoc($res);
        if ($row['uniques'] > 0) {
            die ("ERROR: Lines in <b>$table</b> matching <b>$where</b> keys. $message");
        }
    }
            
    /* test or do the import of a race
        @idracefrom (int) : the id of the race to import
        @idraceto (int) : the id of the imported race in the current server
        @importserver (string): domain name of the import server
        @prinsql (bool) : if true, print the import operations
        @dryrun (bool) if true, only test, does nothing real
    */
    function importracefromws($idracefrom, $idraceto, $importserver, $printsql = true, $dryrun = true) {

        //Generic unicity message.
        $umessage = "Check that the race #$idraceto is not already declared.";

        //Fetching json
        //FIXME : should be a generic function in functions.php
        $fp = fopen("http://$importserver/ws/raceinfo.php?idrace=$idracefrom","r") or die("<h1>Can't reach server $importserver</h1>"); //lecture du fichier
        $json = "";
        while (!feof($fp)) { //on parcourt toutes les lignes
            $json .= fgets($fp, 4096); // lecture du contenu de la ligne
        }
        
        //Parsing json
        $import = json_decode($json, True);
        //json return 0 if the race doesn't exist.
        if (intval($import) == 0) die("<h1>This race doesn't seem to exist...</h1>");
        
        if ($printsql) echo "<pre>";
        //print_r($import);

        //Main table 'races'
        $sqlraces = "INSERT INTO races (idraces, racename, started, deptime, startlong, startlat, boattype, closetime, racetype, firstpcttime, \n                   ".
                    "depend_on, qualifying_races, idchallenge, coastpenalty, bobegin, boend, maxboats, theme, vacfreq) \n          ".
                    " VALUES (".$idraceto.", ".
                    sqlit($import, "racename", 'string').
                    sqlit($import, "started").
                    sqlit($import, "deptime").
                    sqlit($import, "startlong").
                    sqlit($import, "startlat").
                    sqlit($import, "boattype", 'string').
                    sqlit($import, "closetime").
                    sqlit($import, "racetype").
                    sqlit($import, "firstpcttime")."\n                   ".
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
        check_unicity('races', "idraces = $idraceto", $umessage);
        exec_sql($sqlraces, $printsql, $dryrun);

        //'races_waypoints' and 'waypoints' tables
        //FIXME: We should certainly have a specific webservice for that.
        $races_waypoints = $import['races_waypoints'];
        foreach ($races_waypoints as $wporder => $wpmisc) {
	    // compute the missing bits, if any
	    switch( $wpmisc['wpformat'] & 0xF) {
	    case WP_ONE_BUOY:
	        $wpmisc['latitude2'] = $wpmisc['latitude1'];
		$wpmisc['longitude2'] = $wpmisc['longitude1'];
		break;
	    case WP_TWO_BUOYS:
	    default:
	        $wpmisc['laisser_au'] = 999;
	    }
            //build the id of the waypoint - this is a convention
            $idwaypointto = sprintf("%d%02d", $idraceto, $wpmisc['wporder']);
            
            $sqlrwp = "INSERT INTO races_waypoints (idwaypoint, idraces, wporder, laisser_au, wptype, wpformat) ".
                    " VALUES (".$idwaypointto.", ".
                    $idraceto.", ".
                    sqlit($wpmisc, "wporder").
                    sqlit($wpmisc, "laisser_au").
                    sqlit($wpmisc, "wptype", 'string').
                    //default value for allowing v14 to import from v13, to remove when releasing v14.
                    sqlit($wpmisc, "wpformat", 'int', false, 0).
                    " );";
            check_unicity('races_waypoints', "idraces = $idraceto AND idwaypoint = $idwaypointto", $umessage);
            exec_sql($sqlrwp, $printsql, $dryrun);            
            $sqlwp = "INSERT INTO waypoints (idwaypoint, latitude1, longitude1, latitude2, longitude2, libelle, maparea) ".
                    " VALUES (".$idwaypointto.", ".
                    sqlit($wpmisc, "latitude1").
                    sqlit($wpmisc, "longitude1").
                    sqlit($wpmisc, "latitude2").
                    sqlit($wpmisc, "longitude2").
                    sqlit($wpmisc, "libelle", 'string').
                    sqlit($wpmisc, "maparea", 'int', false).
                    " );";
            check_unicity('waypoints', "idwaypoint = $idwaypointto", $umessage);
            exec_sql($sqlwp, $printsql, $dryrun);
        }
        
        //'races_instructions' table
        $races_instructions = $import['races_instructions'];
        foreach ($races_instructions as $ri) {
            $sqlri = "INSERT INTO races_instructions (idraces, instructions, flag) ".
                    " VALUES (".
                    $idraceto.", ".
                    sqlit($ri, "instructions", 'string').
                    sqlit($ri, "flag", 'int', false).
                    " );";
            exec_sql($sqlri, $printsql, $dryrun);
        }
        
        //Fetching racemap directly from the importserver
        $fp = fopen("http://$importserver/racemap.php?idraces=$idracefrom","rb") or die("<h1>Can't reach server $importserver</h1>"); //lecture du fichier
        $racemapcontent = "";
        //FIXME : this is not optimal
        while (!feof($fp)) { //on parcourt toutes les lignes
            $racemapcontent .= fgets($fp, 4096); // lecture du contenu de la ligne
        }
        if (substr($racemapcontent, 0, 2) == "No") {
            print "No racemap in $importserver for race #$idracefrom";
        } else if ($dryrun) {
            print "Found racemap for race #$idracefrom.\n";
        } else {
            //do the insert of the racemap
            insertRacemapContent($idraceto, $racemapcontent);
            print "Racemap inserted";
        }
 
        if ($printsql) echo "</pre>";
    }

// Main code

    if (get_cgi_var("action") != "import") {
        //Display the import form, import not started
?>
        <h3>Ready to import a race, please input following values</h3>
        <form action="#" method="post">
            <input type="hidden" name="action" value="import" />
            From server:&nbsp;<input type="text" name="importserver" size="50" value="testing.virtual-loup-de-mer.org" /><br />
            From Idrace:&nbsp;<input type="text" name="idracefrom" size="12"/><br />
            To Idrace:&nbsp;<input type="text" name="idraceto" size="12"/><br />
            <input type="submit" value="Test import" />
        </form>
<?php
    } else {
        //import started

        //check from input
        $idracefrom = intval(get_cgi_var('idracefrom')) ;
        $idraceto = intval(get_cgi_var('idraceto')) ;
        $importserver = htmlentities(get_cgi_var('importserver'));

        //Default is to print sql
        $printsql = true;    

        if ($idracefrom <1  || $idraceto < 1 ) {
            die("<h1>ERROR : idrace malformed</h1>");
            }

        //FIXME: we should 'ping' the import server and check availability

        if (get_cgi_var('confirm') != 'yes') {
            //import first pass, checking in dryrun mode
            echo "<h3>Testing import of race #<b>$idracefrom</b> from server <b>$importserver</b> to race id #<b>$idraceto</b></h3>";
            
            importracefromws($idracefrom, $idraceto, $importserver, $printsql);
            
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="import" />
            <input type="hidden" name="confirm" value="yes" />
            <input type="hidden" name="importserver" value="<?php echo $importserver; ?>" />
            <input type="hidden" name="idracefrom" value="<?php echo $idracefrom; ?>"/>
            <input type="hidden" name="idraceto" value="<?php echo $idraceto; ?>"/>
            <input type="submit" value="Import this race" />
        </form>
<?php
        } else {
            //Real import
            importracefromws($idracefrom, $idraceto, $importserver, $printsql, false);

            //We're done.
            insertAdminChangelog(Array("operation" => "import from $importserver", "tab" => "races", "rowkey" => $idraceto));
            echo "<h3>OK</h3>";
        }
    }    
    include ("htmlend.php");
?>
