<?
include_once("includes/header.inc");
include_once("config.php");
if (isLoggedIn)
{ 
?>

<p>

<?echo $strings[$lang]["boatupdated"]?>
</p>
<?
if ($submittype=="change")
{
  $query = 'UPDATE users SET `boatname` = "'.addslashes($boatname).'", `color` = "'.$color
    .'" WHERE idusers = '.getLoginId();
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
}
else if ($submittype = "subscribe")
{
  $query = 'UPDATE users SET `engaged` = "'.$idraces
    .'" WHERE idusers = '.getLoginId();
  mysql_db_query(DBNAME,$query) or die("Query failed : " . mysql_error." ".$query);
}
}
else

{
  echo $strings[$lang]["belogin"];
}
 
  include_once("includes/footer.inc");
?>

