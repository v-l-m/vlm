<?php
    $PAGETITLE = "Boat with unknown polar";
    include ("htmlstart.php");
    include_once ("functions.php");
    
    $polarlist = get_polar_list_array();
    print_r($polarlist);
    $defpolar = $polarlist[0];
    die();
        
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) {
        wrapper_mysql_db_query_writer("DELETE FROM players_pending WHERE updated < DATE_SUB(NOW(), INTERVAL $pending_limit DAY)");
        insertAdminChangelog(Array("operation" => "Deleting too old pending players"));

        echo "<h3>Done, following results should be empty.</h3>";
    }
    $query  = "SELECT email, playername, updated AS `request date` ";
    $query .= "FROM players_pending ";
    $query .= "WHERE updated < DATE_SUB(NOW(), INTERVAL $pending_limit DAY)";
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
