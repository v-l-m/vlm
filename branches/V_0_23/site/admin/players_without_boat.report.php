<?php
    $pending_limit = 180;
    $PAGETITLE = "Players without boats since more than $pending_limit days";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) {
        $query  = "DELETE ";
        $query .= "FROM players ";
        $query .= "WHERE updated < DATE_SUB(NOW(), INTERVAL $pending_limit DAY)";
        $query .= " AND idplayers NOT IN (SELECT DISTINCT idplayers FROM playerstousers WHERE linktype = ".PU_FLAG_OWNER.") ";
        wrapper_mysql_db_query_writer($query);
        insertAdminChangelog(Array("operation" => "Deleting too old players without boats"));

        echo "<h3>Done, following results should be empty.</h3>";
    }
        $query  = "SELECT P.idplayers, P.playername, P.updated ";
        $query .= "FROM players as P ";
        $query .= "WHERE updated < DATE_SUB(NOW(), INTERVAL $pending_limit DAY)";
        $query .= " AND P.idplayers NOT IN (SELECT DISTINCT idplayers FROM playerstousers WHERE linktype = ".PU_FLAG_OWNER.") ";
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
