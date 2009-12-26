<?php

// headers
$PAGETITLE = "Admin of RACES_INSTRUCTIONS table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'races_instructions';

// Name of field which is the unique key
$opts['key'] = 'autoid';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-idraces');

$opts['fdd']['autoid'] = array(
  'help'     => 'Unique id of the race',
  'select'   => 'T',
  'input'  => 'R',
  'options' => 'H',
//  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);


$opts['fdd']['idraces'] = array(
  'name'     => 'Id of Race',
  'help'     => 'Unique id of the race<br />0 if the message is for all races.',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '',
  'sort'     => true
);

$opts['fdd']['instructions'] = array(
  'name'     => 'Instructions',
  'help'     => 'Instructions or URL or ...',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['flag'] = array(
  'name'     => 'Types d\'instructions',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true,
  'help'     => "Faire la somme de : <ul><li> (1) => Pour rendre une IC Visible (en général - global flag)</li>
<li>(2) => Pour rendre une IC visible en mode console</li>
<li>(4) => Pour rendre une IC visible sur la racelist</li>
<li>(8) => Pour mettre une URL dont le texte sera 'Instructions de courses sur le forum' </li>
<li>(16) => Pour ne pas rendre visible dans la page des ics </li>
<ul><br /><a href=\"http://dev.virtual-loup-de-mer.org/vlm/wiki/phpcommon\">Voir la doc</a>"
);


include('adminfooter.php');

?>
