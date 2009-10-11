<?php
    $PAGETITLE = "VLM Management";
    include("htmlstart.php");

    function adminlistbox($list, $title) {
        echo "<div class=\"adminbox\">";
        echo "<h3>$title</h3>";
        echo "<ul>";
        foreach ($list as $page => $pagedef) {
            echo "<li>";
            echo "<a href=\"$page\">$pagedef</a>";
            echo "</li>";
        }
        echo "</ul></div>";
    }

    $tablepages = Array(
        "races_instructions.php" => "Races instructions",
        "flags.php" => "Flags definitions (no uploading, see below)",
        "races.php" => "Races definitions (no uploading, see below) [Experimental]",
        );
    adminlistbox($tablepages, "Table administration");


    $uploadpages = Array(
        "uploadracemap.php" => "Race map upload",
        "uploadflag.php" => "Flag upload",
        );

    adminlistbox($uploadpages, "Upload operations");

    $strangepages = Array(
        "strange_engaged_in_unknown.php" => "Engaged in unknown race.",
        "strange_unknown_flag.php" => "Boat with Unknown flag.",
        );

    adminlistbox($strangepages, "Strangeness reports [Use with caution]");

    
    include("htmlend.php");
?>
