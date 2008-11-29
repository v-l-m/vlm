<?
//ini_set(arg_separator.output, "&amp;");
include_once("header.inc");

echo "
    <span id=\"infobulle\">
    </span>
     ";
// Le user peut-t'il s'engager dans les courses ou est il déjà engagé
/*
$display_submit = "false";
if ( isLoggedIn() ) {
     $users = new Users(getLoginId());
     if ( $users->engaged == 0 ) $display_submit = "true";
}
*/

echo "<h1 class=\"info\" align=\"center\">" . $strings[$lang]["one-one-one"] . "</h1>";
echo "<hr />";

include "raceslist.inc";


#echo "<HR>";
#echo "<H4>Grosse charge du serveur WEB VLM</H4>";
#echo "Au lieu d'utiliser virtual-loup-de-mer.org/map.img... ou mercator.img.. utilisez <B>map.</B>virtual-loup-de-mer.org/map.img... ou mercator.img... si vous demandez des carte sans passer par la page Mon Bateau.";
#echo "Le depart prevu pour 20h05 GMT semble amener les bateaux autour de Wight (<B>qui devra &ecirc;tre contourn&eacute;e par le nord, d'est en ouest, donc au pr&egrave;s</B>) au petit matin... Au final, on reste &agrave; 20h05 GMT, avec MAJ toutes les 5 minutes (&agrave; 2 et 7).";
#echo "<HR>";


echo "      <h1> ".$strings[$lang]["homeofsailing"]." </h1>\n";
printf("      <p> ".$strings[$lang]["q1"]." </p>\n"); 
printf("      <p> ".$strings[$lang]["a1"]." </p>"); 

lastUpdate($strings, $lang);
echo "<hr />";

    // Nombre d'inscrits sur VLM
    $querynbu = "SELECT count(*) FROM users where idusers >0";
    $resultnbu = mysql_db_query(DBNAME,$querynbu) or die("Query [$querynbu] failed \n");

    $row = mysql_fetch_array($resultnbu, MYSQL_NUM);
    printf( "<h4>" . $strings[$lang]["nbplayers"]. "</h4>", $row[0]);

    // Affichage des course
    echo "      <h4 class=\"babord\`'>".$strings[$lang]["warningnomap"]." </h4>\n";
    //include "races.php";
?>




  <!-- 
  	<?php printf("      <p> ".$strings[$lang]["atlantic_windinfo"]." </p>"); ?> 
  	<?php printf("      <p> ".$strings[$lang]["classification_dnf"]." </p>"); ?>
	<h3><?php   echo $strings[$lang]["howtohelp"];?></h3> 
	<p><?php   echo $strings[$lang]["howtohelptext"];?></p>
  -->


<?
echo "<hr />";
echo "<h1 class=\"info\" align=\"center\">Rejoignez Virtual-Loup-De-Mer.Org, c'est gratuit !</h1>";
echo "<hr />";

include_once("footer.inc");
?>
