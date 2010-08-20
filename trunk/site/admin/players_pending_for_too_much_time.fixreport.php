<?php
    $pending_limit = 7;
    $PAGETITLE = "Players pending since more than $pending_limit days";
    include ("htmlstart.php");
    include_once ("functions.php");
    
        
    if ($_REQUEST["action"] == "go" and $_REQUEST['confirm'] == "on" ) {
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
