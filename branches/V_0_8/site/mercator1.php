<?php
include_once("includes/header.inc");
include_once("config.php");
//include_once("includes/strings.inc");
//TODO read from cookie and populate form
//TODO if no cookie found, put default values in the form
?>
<?php //print_r($_COOKIE);?>
<div>
<h2><?php echo $strings[$lang][mymap]; ?></h2>
<BR>
</div>
<?php 
$fullUsersObj = new fullUsers(getLoginId());
if ($fullUsersObj->users->engaged != 0)
{  
$fullUsersObj = new fullUsers (getLoginId());
$fullRacesObj = new fullRaces ($fullUsersObj->users->engaged);
$bounds = $fullRacesObj->getRacesBoundaries();
?>
<form id="mercator" action="mercator.page.php" method="get">
<input type="hidden" name="idraces" value="<?php echo $fullUsersObj->users->engaged; ?>" />


<!-- Table pour remonter un peu toute la page -->
<table border=0 width=100%><tr><td class=map align=left valign=top>

<h3><?php echo $strings[$lang]["coord"] ?></h3>
         <div id="minimap">

      N <input id="north" name="north" size="5" maxlength="5" value="<?php if (isset($_COOKIE['north'])) print($_COOKIE['north']); else print($bounds['north']);?>"/><br />

            W <input id="west" name="west" size="5" maxlength="5" value="<?php if (isset($_COOKIE['west'])) print($_COOKIE['west']); else print($bounds['west']);?>"/>
<img style="vertical-align: middle" src="images/site/minimap.png" alt="minimap"/>
            E <input id="east" name="east" size="5" maxlength="5" value="<?php if (isset($_COOKIE['east'])) print($_COOKIE['east']); else print($bounds['east']);?>"/><br/>
            S <input id="south" name="south" size="5" maxlength="5" value="<?php if (isset($_COOKIE['south'])) print($_COOKIE['south']); else print($bounds['south']);?>"/><br />

   <a href="#" 
   onclick="document.getElementById('north').value='<?echo $bounds['north']?>';
document.getElementById('west').value='<?echo $bounds['west']?>';
document.getElementById('east').value='<?echo $bounds['east']?>';
document.getElementById('south').value='<?echo $bounds['south']?>';"
> Centrer la carte sur la course</a><BR><BR>
</div>

<h3><?php echo $strings[$lang]["maximage"] ?></h3> 
      <?php echo $strings[$lang]["maxwidth"] ?> <input name="x"  size="4"  maxlength="4" value="<?php if (isset($_COOKIE['x'])) print($_COOKIE['x']); else print(800);?>"/> * 
      <?php echo $strings[$lang]["maxheight"] ?>  <input name="y"  size="4"  maxlength="4" value="<?php if (isset($_COOKIE['y'])) print($_COOKIE['y']); else print(600);?>"/><br/>

<h3><?php echo $strings[$lang]["proj"] ?> : </h3>
<?php echo $strings[$lang]["mercator"] ?>  <input type="radio" name="proj" value="mercator" <?php if ((($_COOKIE['proj'])=="mercator") || (!isset($_COOKIE['proj']))) echo  "checked=\"checked\"";?> />
<?php echo $strings[$lang]["lambert"] ?><input type="radio" name="proj" value="lambert" <?php if (($_COOKIE['proj'])=="lambert") echo "checked=\"checked\"";?>/><br/>
<br/>

<h3><?php echo $strings[$lang]["disp"] ?> : </h3>
<?php echo $strings[$lang]["none"] ?><input type="radio" name="text" value="none" <?php if (($_COOKIE['text'])=="none" || (!isset($_COOKIE['text']))) echo "checked=\"checked\"";?>/>
<?php echo $strings[$lang]["left"] ?> <input type="radio" name="text" value="left" <?php if (($_COOKIE['text'])=="left") echo "checked=\"checked\"";?>/>
<?php echo $strings[$lang]["right"] ?> <input type="radio" name="text" value="right" <?php if (($_COOKIE['text'])=="right") echo "checked=\"checked\"";?>/>

</td>
<!-- //Colonne 2 : les trajectoires et les noms des bateaux -->
<td class=map align=left valign=top>
<h3><?php echo $strings[$lang]["tracks"] ?> : </h3>

<input type="checkbox" name="tracks" <?php if (($_COOKIE['tracks'])=="on") echo "checked=\"checked\"";?> /><?php echo $strings[$lang]["disptracks"] ?>

<br/>



<h3><?php echo $strings[$lang]["chooseopp"] ?> : </h3>
<?//List of players, check boxes


$fullRacesObj->dispHtmlForm($strings, $lang, explode(",", $_COOKIE['list']));
// foreach ( $fullRacesObj->opponents as $opp)
// {
//   printf("%s <span style=\"background-color: #%s\">%s</span>", $opp->username, $opp->color,  $opp->boatname); 
//   printf("<input type=\"checkbox\" name=\"list[]\" value=\"%s\" ",  $opp->idusers   );
//   print_r(explode(",", $_COOKIE['list']), -1);
//   if ( in_array($opp->idusers, explode(",", $_COOKIE['list'], -1)  ) )
//     echo " checked=\"checked\" ";
//   echo ">";
//   echo "<br/>";
//   //if machin
//   //checked="checked"
// } 


?>
<BR>
<input type="checkbox" name="save" checked="checked" /><?php echo $strings[$lang]["save"] ?>  <br/>
</td></tr></table>

<input type="submit" value="<? echo $strings[$lang]["map"]?>"/>
      </form>
<?php
   }
else
{
  echo  $strings[$lang]["mustbeengaged"];
}
//TODO : list of users with checkboxes
//TODO : write into cookie at submission
include_once("includes/footer.inc");
?>
