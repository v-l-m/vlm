<?php
    $PAGETITLE = "Strange case : Results for unknown races";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) {
        wrapper_mysql_db_query_writer("DELETE FROM races_results WHERE races_results.idraces NOT IN (SELECT DISTINCT idraces FROM races)");
        insertAdminChangelog(Array("operation" => "Delete results for unknown races"));

        echo "<h3>Done, following results should be empty.</h3>";
    }
    htmlQuery("SELECT * FROM races_results as rr WHERE rr.idraces NOT IN (SELECT DISTINCT idraces FROM races)");
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="checkbox" name="confirm">
            <input type="submit" value="Clean it ?" />
        </form>
<?php
    include ("htmlend.php");
?>
