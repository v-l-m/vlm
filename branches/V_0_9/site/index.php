<?php
  
    include_once("includes/header.inc");

?>

    <span id="infobulle"></span>

<?php
    if (!isLoggedIn()) {
?>
    <div id="whatisvlmbox" class="basic">
      <h1><?php echo $strings[$lang]["homeofsailing"]; ?></h1>
      <p><?php echo $strings[$lang]["q1"]; ?></p> 
      <p><?php echo $strings[$lang]["a1"]; ?></p> 
    </div>
<?php
    } else {
        $users = new Users(getLoginId());
        if ( $users->engaged != 0 ) {
            include("abandon_race.php");
        }
    }
?>    

    <div id="oneoneonebox" class="basic">
      <h1 class="info"><?php echo $strings[$lang]["one-one-one"]; ?></h1>
    </div>

    <div id="raceslistbox" class="basic">
      <?php include "includes/raceslist.inc"; ?>
    </div>

    <div id="time" class="basic">
      <?php
          lastUpdate($strings, $lang);
      ?>
    </div>

    <div id="userstatsbox" class="basic">

<?php
    // Nombre d'inscrits sur VLM
    $querynbu = "SELECT count(*) FROM users where idusers >0";
    $resultnbu = mysql_db_query(DBNAME,$querynbu) or die("Query [$querynbu] failed \n");

    $row = mysql_fetch_array($resultnbu, MYSQL_NUM);
    printf( "<h4>" . $strings[$lang]["nbplayers"]. "</h4>", $row[0]);
?>
    </div>
    
    <div id="joinvlmbox"  class="basic">
      <h1 class="info">Rejoignez Virtual-Loup-De-Mer.Org, c'est gratuit !</h1>;
    </div>

<?php
    include_once("includes/footer.inc");
?>