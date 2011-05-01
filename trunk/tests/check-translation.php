<?php

include "../site/includes/strings.inc";

$false_errors = Array(
    'fr' => Array(
      'images', 'faq', 'doc', 'forum', 'contact', 'version', 'position', 'degrees', 'taverne', 'tchat', 'loch', 'estime', 'mapsize',
      'proj', 'mercator', 'lambert', 'a12', 
      'vbvmgengaged', 'Mobiles', 'action',
      'pref_contact_taverne', 'pref_contact_fmv', 'pref_contact_revatua', 'pref_contact_twitter', 'pref_contact_identica',
      'pref_contact_facebook', 'pref_contact_msn', 'pref_contact_jabber', 'Restriction', 'pilototohelp1',

      ),
    'it' => Array(
      'home', 'forum', 'taverne', 'tchat', 'sponsor', 'skipper', 'nautics', 'mapsize', 'degrees', 'a12', 'dateClassificationFormat', 
      'vbvmgengaged', 'no', 'pilototohelp1',
      ),
    'pt' => Array(
      'faq', 'forum', 'tchat', 'days', 'mapsize', 'mercator', 'degrees', 'a12', 'dateClassificationFormat',
      'vbvmgengaged',
      ),
    'es' => Array(
      'faq', 'tchat', 'skipper', 'nautics', 'knots', 'days', 'mapsize', 'mercator', 'tracks', 'degrees', 'a12', 'dateClassificationFormat',
      'loch',
      'vbvmgengaged', 'no', 'Boatsitter',
      ),
    'en' => Array(
      'images', 'faq', 'doc', 'forum', 'tchat', 'taverne', 'a12', 'contact', 'version', 'position', 'loch', 'estime', 'mapsize', 'proj', 'mercator', 'lambert', 'degrees',
      'vbvmgengaged', 'Mobiles', 'action',
      'pref_contact_taverne', 'pref_contact_fmv', 'pref_contact_revatua', 'pref_contact_twitter', 'pref_contact_identica', 'pref_contact_facebook', 'pref_contact_msn', 'pref_contact_jabber', 'Restriction', 'pilototohelp1',

      ),
     'de' => Array(
       'home', 'faq', 'email', 'forum', 'version', 'vbvmgengaged',
      ),
    );

$langs= array_keys($strings);

foreach ($langs as $lg) {
    $missing = Array();
    $untranslated = Array();
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
            $missing[] = $k;
        } else if ($strings[$base][$k] == $strings[$lg][$k] and !in_array($k, $false_errors[$lg])) {
            echo "$lg : the KEY '$k' is UNTRANSLATED ($base==$lg): Is \"".$strings[$base][$k]."\" the same in $lg ?\n";
            $untranslated[] = $k;
        }
    }
    echo "\n---Summary :";
    echo "\nMissing : '";
    echo implode("', '", $missing);
    echo "'\nUntranslated : '";
    echo implode("', '", $untranslated);
    echo "\n\nCODE :\n";
    foreach ($missing as $k) echo "\"$k\" => \"".$strings['en'][$k]."\",\n";
    foreach ($untranslated as $k) echo "\"$k\" => \"".$strings['en'][$k]."\",\n";
    echo "'\n\n";
}
    
