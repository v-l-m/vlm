<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("playersPrefs.class.php");
    include_once("config.php");

    if (!isPlayerLoggedIn()) die("Not logged");
    $player = getLoggedPlayerObject();
        
    $pp = new playersPrefsHtml(getPlayerId());
    
    foreach ($playersPrefsList as $pkey) {
        $pp->checkCgiPref($pkey);
    }

    if ($pp->error_status || $pp->playerclass->error_status) {
        echo "<div class=\"error\">";
        print nl2br($pp->error_string);
        print nl2br($pp->playerclass->error_string);    
        echo "</div>";
    }

    
    echo "<div>";
    echo "<form name=\"prefs\" method=\"post\">";
    echo "<table>";
    echo "<tr>";
    echo "<td colspan=\"3\">";
    echo "<input name=\"prefvalidate\" type=\"submit\" value=\"".getLocalizedString("Save")."\" />";
    echo "<input name=\"prefcancel\" type=\"submit\" value=\"".getLocalizedString("Cancel")."\" />";
    echo "</td>";
    echo "</tr>";

    foreach (playersPrefsGroups() as $g => $pg) {
        echo "<tr>";
        echo "<td colspan=\"2\">";
        echo "<h1>".getLocalizedString("prefsgroup_$g")."</h1>";
        echo "</td>";
        echo "<td>".getLocalizedString("Permissions")."</td>";
        echo "</tr>";

        foreach ($pg as $pkey) {
            echo "<tr class=\"prefsetter\">";
            echo "<td>".getLocalizedString("pref_$pkey")."</td>";
            echo "<td>".$pp->getform($pkey)."</td>";
            echo "<td>".$pp->permissions($pkey)."</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "</form>";
    echo "</div>";    

?>
