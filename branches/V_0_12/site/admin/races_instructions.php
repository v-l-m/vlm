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

/* Field definitions
   
Fields will be displayed left to right on the screen in the order in which they
appear in generated list. Here are some most used field options documented.

['name'] is the title used for column headings, etc.;
['maxlen'] maximum length to display add/edit/search input boxes
['trimlen'] maximum length of string content to display in row listing
['width'] is an optional display width specification for the column
          e.g.  ['width'] = '100px';
['mask'] a string that is used by sprintf() to format field output
['sort'] true or false; means the users may sort the display on this column
['strip_tags'] true or false; whether to strip tags from content
['nowrap'] true or false; whether this field should get a NOWRAP
['select'] T - text, N - numeric, D - drop-down, M - multiple selection
['options'] optional parameter to control whether a field is displayed
  L - list, F - filter, A - add, C - change, P - copy, D - delete, V - view
            Another flags are:
            R - indicates that a field is read only
            W - indicates that a field is a password field
            H - indicates that a field is to be hidden and marked as hidden
['URL'] is used to make a field 'clickable' in the display
        e.g.: 'mailto:$value', 'http://$value' or '$page?stuff';
['URLtarget']  HTML target link specification (for example: _blank)
['textarea']['rows'] and/or ['textarea']['cols']
  specifies a textarea is to be used to give multi-line input
  e.g. ['textarea']['rows'] = 5; ['textarea']['cols'] = 10
['values'] restricts user input to the specified constants,
           e.g. ['values'] = array('A','B','C') or ['values'] = range(1,99)
['values']['table'] and ['values']['column'] restricts user input
  to the values found in the specified column of another table
['values']['description'] = 'desc_column'
  The optional ['values']['description'] field allows the value(s) displayed
  to the user to be different to those in the ['values']['column'] field.
  This is useful for giving more meaning to column values. Multiple
  descriptions fields are also possible. Check documentation for this.
*/


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
