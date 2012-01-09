<?
include_once("includes/header.inc");
include_once("config.php");
if (isLoggedIn)
{ 
?>

<p>

<?echo getLocalizedString("boatupdated")?>
</p>
<?
if ($submittype=="change")
{
  $query = 'UPDATE users SET `boatname` = "'.addslashes($boatname).'", `color` = "'.$color
    .'" WHERE idusers = '.getLoginId();
  mysql_query($query) or die("Query failed : " . mysql_error." ".$query);
}
else if ($submittype = "subscribe")
{
  $query = 'UPDATE users SET `engaged` = "'.$idraces
    .'" WHERE idusers = '.getLoginId();
  mysql_query($query) or die("Query failed : " . mysql_error." ".$query);
}
}
else

{
  echo getLocalizedString("belogin");
}
 
  include_once("includes/footer.inc");
?>

