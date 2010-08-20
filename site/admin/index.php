<?php
    $PAGETITLE = "VLM Management";
    include("htmlstart.php");

    function adminlistbox($list, $title, $style=null) {
        echo "<div class=\"adminbox\"";
        if (!is_null($style)) {
            echo "style=\"$style\"";
            }
        echo ">";
        echo "<h3>$title</h3>";
        echo "<ul>";
        foreach ($list as $page => $pagedef) {
            echo "<li>";
            echo "<a href=\"$page\">$pagedef</a>";
            echo "</li>";
        }
        echo "</ul></div>";
    }

    echo "<div style=\"display:inline;float:left\">";

    $tablepages = Array(
        "adminwizard.php" => "Admin Wizard (old admin interface)",
        );
    adminlistbox($tablepages, "Miscellaneous");


    $tablepages = Array(
        "races_instructions.php" => "Races instructions",
        "flags.php" => "Flags definitions (no uploading, see below)",
        "races.php" => "Races definitions (no uploading, see below)",
        "waypoints.php" => "[Experimental] Waypoints definitions",
        "races_waypoints.php" => "[Experimental] Races_waypoints definitions",
        "users.php" => "[Experimental] Users",
        "user_action.php" => "Browse User Actions",
        "players.php" => "[Experimental] Players",
        "players_pending.php" => "[Experimental] Players Pending",
        "playerstousers.php" => "[Experimental] Links between players & users",
        );
    adminlistbox($tablepages, "Table administration/browsing");

    $uploadpages = Array(
        "uploadracemap.php" => "Race map upload",
        "uploadflag.php" => "Flag upload",
        "importrace.php" => "Import race from another server",
        );

    adminlistbox($uploadpages, "Upload and import operations");

    echo "</div>";
    echo "<div style=\"display:inline;float:left;\">";

    $reportpages = Array(
        "user_agents.php"                   => "User_agent statistics",
        "possible_duplicates.php"           => "Duplicates by IP",
        "racemaps_without_race.report.php"  => "Racemaps with no corresponding race",
        "races_without_racemap.report.php"  => "Races with no corresponding racemap",
        "boats_with_more_than_one_owner.report.php" => "Boats with more than one owner",
        "boats_without_owner.report.php" => "Boats without owner",
        "boats_with_boatsitter_but_without_owner.report.php" => "Boats with boatsitter(s) but without owner",
        );

    adminlistbox($reportpages, "Reports");

    $strangepages = Array(
        "strange_results_for_unknown_races.php"    => "Results for unknown races.",
        "strange_engaged_in_unknown.php"    => "Engaged in unknown race.",
        "strange_unknown_flag.php"          => "Boat with Unknown flag.",
        "players_pending_for_too_much_time.fixreport.php" => "Players pending for too much time",
        );
    
    adminlistbox($strangepages, "Report & fix [Use with caution to fix inconsistencies]");
    echo "</div>";

    echo "<div class=\"adminbox\" id=\"adminlastopbox\">";
    echo "<h3>Last operations</h3>";
    htmlQuery("SELECT updated, user AS admin, host, operation, tab, rowkey, col AS field, LEFT(oldval, 30) AS oldval, LEFT(newval, 30) AS newval FROM admin_changelog ORDER BY updated DESC LIMIT 10;");
    echo "</div>";
    
    
    include("htmlend.php");
?>
