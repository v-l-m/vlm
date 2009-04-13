<?php

include "../site/includes/strings.inc";

$false_errors = Array(
    'fr' => Array(
      'images', 'faq', 'doc', 'forum', 'contact', 'version', 'position', 'degrees'
      ),
    );

$langs= array_keys($strings);
foreach ($langs as $lg) {
    if ($lg == "en") continue;
    if (!in_array($lg, array_keys($false_errors))) {
        $false_errors[$lg] = Array();
        }
    echo "\n---STRINGS UNTRANSLATED FOR THE LANG : \"$lg\"---\n\n";
    $enkeys = array_keys($strings["en"]);
    foreach ($enkeys as $k) {
        if (!array_key_exists($k, $strings[$lg])) {
            echo "$lg : KEY '$k' UNTRANSLATED (missing) : What is the translation of \"".$strings["en"][$k]."\" in $lg ?\n";
        } else if ($strings["en"][$k] == $strings[$lg][$k] and !in_array($k, $false_errors[$lg])) {
            echo "$lg : KEY '$k' UNTRANSLATED (en==$lg): Is \"".$strings["en"][$k]."\" the same in $lg ?\n";
        }
    }
}
    
