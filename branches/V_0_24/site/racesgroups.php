<?php
    include_once("includes/header.inc");
    include_once("racesgroups.class.php");
    include_once("config.php");

    $grouptag = get_cgi_var('grouptag', null);


    
    if (!is_null($grouptag)) {
        $rgo = new racesgroups($grouptag);
        $racelist = $rgo->getRaces();
        print $rgo->htmlSummary();
        print htmlRacesList($racelist);
    } else {
    
        print "NO RACE GROUPS DEFINED";
        
    }

    include_once("includes/footer.inc");
?>
