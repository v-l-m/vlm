<?php

// headers
$PAGETITLE = "Admin of USERS table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'users';

// Name of field which is the unique key
$opts['key'] = 'idusers';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('idusers');

/* Fields def. helpers */

//suboptimal, should not be done for each load of the page but only when allowing to change something

$dir = "../".DIRECTORY_THEMES;
$dh  = opendir($dir);
$select_list="";
while (false !== ($filename = readdir($dh))) {
    if ( is_dir("$dir/$filename") and (substr($filename, 0, 1) != ".") and ($filename != "..")) {
        //Taking only directories
        $list_themes[$filename] = $filename;
    }
}
$list_themes[''] = $filename;
asort($list_themes);
closedir($dh);

$calendar_specifications = array(
     'ifFormat'    => '%s', // defaults to the ['strftimemask']
     'firstDay'    => 1,          // 0 = Sunday, 1 = Monday
     'singleClick' => true,       // single or double click to close
     'weekNumbers' => true,       // Show week numbers
     'showsTime'   => true,      // Show time as well as date
     'timeFormat'  => '24',       // 12 or 24 hour clock
     'button'      => true,       // Display button (rather then clickable area)
     'label'       => '...',      // button label (used by phpMyEdit)
     );


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


$opts['fdd']['idusers'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the player',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['username'] = array(
  'name'     => 'Name of the user',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['password'] = array(
  'name'     => 'Password of the user',
  'options'  => 'ACP',
  'select'   => 'T',
  'escape'   => true,
  'options'  => 'ACDPVF',
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['class'] = array(
  'name'     => 'User type',
  'select'   => 'D',
  'values'  => Array('standard', 'admin'), 
  'maxlen'   => 32,
  'sort'     => true
);

$opts['fdd']['country'] = array(
  'name'     => 'Country/Flag',
  'help'     => 'Input the name of the flag<br />Do we need a drop down here ?',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 64,
  'sort'     => true
);


$opts['fdd']['blocnote'] = array(
  'name'     => 'Notes of the user.',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 250,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);

$opts['fdd']['theme'] = array(
  'name'     => 'Theme',
  'options'  => 'ACPDV',
  'select'   => 'D',
  'options'  => 'ACDPVF',
  'maxlen'   => 30,
  'values'   => $list_themes,
  'sort'     => true
);

include('adminfooter.php');

?>
