<?php
    $PAGETITLE = "VLM Management";
    include("htmlstart.php");

    $tablepages = Array(
        "races.php" => "Races definitions",
        "races_instructions.php" => "Races instructions",
        );

    echo "<div class=\"adminbox\">";
    echo "<h3>Table administration</h3>";
    echo "<ul>";
    foreach ($tablepages as $page => $pagedef) {
        echo "<li>";
        echo "<a href=\"$page\">$pagedef</a>";
        echo "</li>";
        }
    echo "</ul></div>";

    $uploadpages = Array(
        "uploadracemap.php" => "Race map upload",
        "uploadflag.php" => "Flag upload",
        );

    echo "<div class=\"adminbox\">";
    echo "<h3>Upload tools</h3>";
    echo "<ul>";
    foreach ($uploadpages as $page => $pagedef) {
        echo "<li>";
        echo "<a href=\"$page\">$pagedef</a>";
        echo "</li>";
        }
    echo "</ul></div>";

    
    include("htmlend.php");
?>
