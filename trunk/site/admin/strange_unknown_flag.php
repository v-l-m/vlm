<?php
    $PAGETITLE = "Strange case : Boat with unknown flag";
    include ("htmlstart.php");
    include_once ("functions.php");
        
    if ($_REQUEST["action"] == "go" and $_REQUEST['confirm'] == "on" ) {
        wrapper_mysql_db_query("update users SET country='' where country not in (select distinct idflags from flags)");
        echo "<h3>Done, following results should be empty.</h3>";
    }
    htmlQuery("select idusers, username, boatname, class, country, from_unixtime(lastchange) as lastchange from users where country not in (select distinct idflags from flags)");
?>
        <form action="#" method="post">
            <input type="hidden" name="action" value="go" />
            <input type="checkbox" name="confirm">
            <input type="submit" value="Clean it ?" />
        </form>
<?php
    include ("htmlend.php");
?>
