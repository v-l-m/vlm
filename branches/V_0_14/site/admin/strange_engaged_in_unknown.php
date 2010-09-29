<?php
    $PAGETITLE = "Strange case : Boat engaged in unknown race";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if (get_cgi_var("action") == "go" and get_cgi_var('confirm') == "on" ) {
        wrapper_mysql_db_query_writer("UPDATE users SET engaged=0 WHERE engaged != 0 AND engaged NOT IN (SELECT DISTINCT idraces FROM races)");
        insertAdminChangelog(Array("operation" => "Update users engaged in unknown race"));

        echo "<h3>Done, following results should be empty.</h3>";
    }
    htmlQuery("SELECT idusers, username, boatname, class, engaged, from_unixtime(lastchange) AS lastchange FROM users WHERE engaged != 0 AND engaged NOT IN (SELECT DISTINCT idraces FROM races)");
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="checkbox" name="confirm">
            <input type="submit" value="Clean it ?" />
        </form>
<?php
    include ("htmlend.php");
?>
