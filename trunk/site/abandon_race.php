<div id="abandon">
<?php
    $fullUsersObj = new fullUsers($users->idusers, $users);
    echo "<p>" . getLocalizedString("warning") . "</p>";
    $racesObj = $fullUsersObj->races;
    echo "<p>" . getLocalizedString("youengaged");
    echo " <b>" .   $racesObj->htmlRacenameLink($lang) .  " (" . $racesObj->htmlIdracesLink($lang)    .  ") " . "</b></p>";

    // The user may want to unsubscribe from this race
    echo htmlAbandonButton($fullUsersObj->users->idusers, $fullUsersObj->users->engaged);
?>
</div>
