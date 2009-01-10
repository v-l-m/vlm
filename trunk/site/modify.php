<?php
include_once("includes/header.inc");
include_once("config.php");


$fullUsersObj = new fullUsers(getLoginId());
//Hello $username modify your account data : 

echo "<h2>".$strings[$lang]["choose"]."</h2>";
?>

<form action="myboat.php" name="modify" method="post">
<input type="hidden" name="lang" value="<?php echo $lang?>"/>
<input type="hidden" name="idusers" value="<?php echo $fullUsersObj->users->idusers?>" />
<input type="hidden" name="type" value="change" />
<p>
<?php
     echo $strings[$lang]["boatnamethat"];
     echo "<input type=\"text\" name=\"boatname\" size=\"45\" maxlength=\"45\" value=\"" . $fullUsersObj->users->boatname . "\"/>";

     // Si le masquage de position n'est pas prévu ou qu'il n'y a pas de crédit, hidepos = 0
     // Affichage de la checkbox uniquement dans les cas contraires
     if ( $fullUsersObj->users->hidepos != 0 ) {

         echo "<br />";

         echo "<input type=\"checkbox\" name=\"hidepos\" ";
         if ( $fullUsersObj->users->hidepos > 0 ) { 
              echo "checked=\"checked\""; 
         }
         echo " />";

         echo $strings[$lang]["hidepos"] . " (" . abs($fullUsersObj->users->hidepos) . " units.)"; 
         echo "<br />";

     }

        echo "<br />" . $strings[$lang]["useremail"];
        echo "<input type=\"text\" name=\"email\" size=\"50\" maxlength=\"60\" value=\"" . $fullUsersObj->users->email . "\" />";


     // EN PHP5 : on aurait scandir. Le site est sur un serveur PHP4.
     // give a list of country (taken in directory "pavillons")
  $dir = DIRECTORY_COUNTRY_FLAGS;
  $dh  = opendir($dir);
        $select_list="";
  while (false !== ($filename = readdir($dh))) {
        if ( ! is_dir($filename) && substr($filename,0,1) != "." ) {
            $files[] = basename($filename,".png");
        }
  }
  sort($files);
  foreach ($files as $filename) {
                  $select_list = $select_list . "<option value=\"". $filename . "\"";
      if ( $fullUsersObj->users->country == $filename ) $select_list = $select_list . " selected=\"selected\" ";
      $select_list = $select_list . ">". $filename ."</option>\n";
  }
        if ( $select_list != "" ) {
          echo "</p>\n<h1>".$strings[$lang]["choose_your_country"]."</h1>\n";
        echo "<select name=\"country\">\n" . $select_list . "</select>\n";
        }


?>
<br />
<br />
<br />
Notepad :<br />
<textarea name="blocnote" cols="60" rows="10"><?php echo $fullUsersObj->users->blocnote ?></textarea>

<!--
//setUserPref(htmlentities($_GET['boat']), "mapTools" , "none");
//setUserPref($fullUsersObj->users->idusers, "mapPrefOpponents" , implode(",", $list)   );
-->

<br />
<?php
     echo $strings[$lang]["color"];
    //display a table with all the colors
?>

<div> <?php /* style="background-color: #<?php echo $fullUsersObj->users->color?>; width: 50%" > */ ?>
<?php 
  // make an array with colors
  $colors = array();
  for ( $red=0 ; $red<=255; $red+=32) {
    $redhex=dechex($red);
    if ( $red < 16 ) $redhex = '0'.$redhex;

    for ( $green=0 ; $green<=255; $green+=64) {
      $greenhex=dechex($green);
      if ( $green < 16 ) $greenhex = '0'.$greenhex;

      for ( $blue=0 ; $blue<=255; $blue+=64) {
          $bluehex=dechex($blue);
    if ( $blue < 16 ) $bluehex = '0'.$bluehex;

        array_push ($colors, $redhex.$greenhex.$bluehex );
      }
    }
  }

  // report each color in a clickable table
  echo "<table border=\"0\" cellspacing=\"0\" id=\"grid\">\n";

  $color_num=0;
  foreach ($colors as $i)
  {
          if ( $color_num%16 == 0) {
      echo "<tr>\n";
    }
    echo "  <td style=\"background: #".$i."\"  onclick=\"document.forms[1].color.value=&quot;".$i."&quot;\">";
    echo "</td>\n";
    if ( $color_num%16 == 15)
    {
      echo "</tr>\n";
    }
    $color_num++;

    echo "";
  }
  echo "</table>\n";

        if ( substr($fullUsersObj->users->color,0,1) == "-" ) {
             $checked="checked ";
             $color=substr($fullUsersObj->users->color,1);
        } else {
             $checked=" ";
             $color=$fullUsersObj->users->color;
        }
        echo "<input type=\"text\" name=\"color\" size=\"6\" ";
        echo "value = \"" . $color . "\"";
        echo " onfocus=\"document.forms.modify.color.blur()\" />";

        echo "<input type=\"checkbox\" name=\"invisible\" " . $checked . " /> Invisible";
?>


</div>
<br/>
   <input type="submit"  value="<?php echo $strings[$lang]["change"]?>" />


</form>

<?php
include_once("includes/footer.inc");
?>
