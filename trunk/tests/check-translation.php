<?php

include "../site/includes/strings.inc";

$false_errors = Array(
    'fr' => Array(
      'images', 'faq', 'doc', 'forum', 'contact', 'version', 'position', 'degrees', 'taverne', 'tchat', 'loch', 'estime', 'mapsize',
      'proj', 'mercator', 'lambert', 'a12', 
      ),
    'it' => Array(
      'home', 'forum', 'taverne', 'tchat', 'sponsor', 'skipper', 'nautics', 'mapsize', 'degrees', 'a12', 'dateClassificationFormat', 
      ),
    'pt' => Array(
      'faq', 'forum', 'tchat', 'days', 'mapsize', 'mercator', 'degrees', 'a12', 'dateClassificationFormat'
      ),
    'es' => Array(
      'faq', 'tchat', 'skipper', 'nautics', 'knots', 'days', 'mapsize', 'mercator', 'tracks', 'degrees', 'a12', 'dateClassificationFormat',
      'loch',
      ),
    );

$langs= array_keys($strings);

foreach ($langs as $lg) {
    if ($lg == "en") {
        $base = "fr"; //swap for en & fr, main languages of VLM.
    } else {
        $base = "en";
    }
    if (!in_array($lg, array_keys($false_errors))) {
        //blank black list
        $false_errors[$lg] = Array();
        }
    echo "\n---STRINGS UNTRANSLATED FOR THE LANG : \"$lg\"---\n\n";
    $enkeys = array_keys($strings[$base]);
    foreach ($enkeys as $k) {
        if (!array_key_exists($k, $strings[$lg])) {
            echo "$lg : the KEY '$k' is MISSING : What is the translation of \"".$strings[$base][$k]."\" in $lg ?\n";
        } else if ($strings[$base][$k] == $strings[$lg][$k] and !in_array($k, $false_errors[$lg])) {
            echo "$lg : the KEY '$k' is UNTRANSLATED ($base==$lg): Is \"".$strings[$base][$k]."\" the same in $lg ?\n";
        }
    }
}
    