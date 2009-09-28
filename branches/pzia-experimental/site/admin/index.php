<?php
    $PAGETITLE = "VLM Management";
    include("htmlstart.php");

    $adminpages = Array(
        "races.php" => "Race definition",
        "uploadracemap.php" => "Race map upload",
        "uploadflag.php" => "Flag upload",
        );

    echo "<div class=\"adminbox\">";
    echo "<h3>Table administration</h3>";
    echo "<ul>";
    foreach ($adminpages as $page => $pagedef) {
        echo "<li>";
        echo "<a href=\"$page\">$pagedef</a>";
        echo "</li>";
        }
    echo "</ul></div>";
    
    include("htmlend.php");
?>