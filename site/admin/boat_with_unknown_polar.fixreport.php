<?php
    $PAGETITLE = "Boat with unknown polar";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $polarlist = get_polar_list_array();
    $insql = "( ";
    foreach ($polarlist as $p) {
        $insql .= "'boat_$p', ";
    }
    $insql .= "'bidon' )";
    $defpolar = "boat_".$polarlist[0];
        
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) {
        wrapper_mysql_db_query_writer("UPDATE users SET boattype = '$defpolar' WHERE boattype NOT IN $insql");
        insertAdminChangelog(Array("operation" => "Fix unknown polars"));

        echo "<h3>Done, following results should be empty.</h3>";
    }
    $query  = "SELECT idusers, username, trim(boatname), engaged, boattype ";
    $query .= "FROM users ";
    $query .= "WHERE boattype NOT IN $insql";
    htmlQuery($query);
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="checkbox" name="confirm">
            <input type="submit" value="Clean it ?" />
        </form>
<?php
    include ("htmlend.php");
?>
