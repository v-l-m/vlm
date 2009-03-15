<?php
    include_once("includes/header.inc");
    include_once("config.php");
?>

    <h1<?php echo $strings[$lang]["faqtitle"]; ?></h1>

<?php
    for ($i =1; $i<20; $i++) {
?>

        <h3><?php echo $strings[$lang]["q".$i]; ?></h3>
        <p><?php echo $strings[$lang]["a".$i]; ?></p>

<?php
    }

    include_once("includes/footer.inc");

?>