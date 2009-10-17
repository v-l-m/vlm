<?php
    $PAGETITLE = "Strange case : Boat engaged in unknown race";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if ($_REQUEST["action"] == "go" and $_REQUEST['confirm'] == "on" ) {
        wrapper_mysql_db_query_writer("update users SET engaged=0 where engaged != 0 and engaged not in (select distinct idraces from races)");
        insertAdminChangelog($operation = "Update users engaged in unknown race");

        echo "<h3>Done, following results should be empty.</h3>";
    }
    htmlQuery("select idusers, username, boatname, class, engaged, from_unixtime(lastchange) as lastchange from users where engaged != 0 and engaged not in (select distinct idraces from races)");
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="checkbox" name="confirm">
            <input type="submit" value="Clean it ?" />
        </form>
<?php
    include ("htmlend.php");
?>
