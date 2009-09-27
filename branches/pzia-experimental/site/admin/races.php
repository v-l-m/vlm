<?php

// headers
$PAGETITLE = "Admin of RACE table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'races';

// Name of field which is the unique key
$opts['key'] = 'idraces';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-deptime');

/* Fields def. helpers */


//suboptimal, should not be done for each load of the page but only when allowing to change something
$dir = "../".DIRECTORY_POLARS ; 
$dh  = opendir($dir);
$select_list="";
while (false !== ($filename = readdir($dh))) {
    if ( !is_dir("$dir/$filename") and ($filename != ".") and ($filename != "..")) {
        //Taking only files
        $keypolar =  substr($filename, 0, -4);
        $list_polars[$keypolar] = substr($keypolar, 5); 
//        print $filename;
    }
}
asort($list_polars);
closedir($dh);

//suboptimal, should not be done for each load of the page but only when allowing to change something

$dir = "../".DIRECTORY_THEMES;
$dh  = opendir($dir);
$select_list="";
while (false !== ($filename = readdir($dh))) {
    if ( is_dir("$dir/$filename") and ($filename != ".") and ($filename != "..")) {
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


$opts['fdd']['idraces'] = array(
  'name'     => 'Idraces',
  'help'     => 'Unique id of the race',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['racename'] = array(
  'name'     => 'Name of the race',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Basic HTML is for now allowed but discouraged', 
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['started'] = array(
  'name'     => 'Started',
  'help'     => "Status of the race",
  'select'   => 'D',
  'values2'  => Array("-1" => "Finished", "0"=>"Not started", "1" => "Yes"), 
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['deptime'] = array(
  'name'     => 'Deptime',
  'select'   => 'T',
  'sql|LFVD' => 'FROM_UNIXTIME(deptime)',
  'maxlen'   => 20,
  'sort'     => true,
  'calendar' => $calendar_specifications,
);
$opts['fdd']['startlat'] = array(
  'name'     => 'Start lat.',
  'select'   => 'T',
  'sql|LFVD' => 'startlat/1000',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['startlong'] = array(
  'name'     => 'Start long.',
  'select'   => 'T',
  'maxlen'   => 11,
  'sql|LFVD' => 'startlong/1000',
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['boattype'] = array(
  'name'     => 'Boattype',
  'select'   => 'D',
  'values2'  => $list_polars,
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['closetime'] = array(
  'name'     => 'Closetime',
  'select'   => 'T',
  'sql|LFVD' => 'FROM_UNIXTIME(closetime)',
  'maxlen'   => 20,
  'calendar' => $calendar_specifications,
  'sort'     => true
);
$opts['fdd']['racetype'] = array(
  'name'     => 'Racetype',
  'select'   => 'D',
  'values2'  => Array('0' => 'One shot', '1' => 'Permanent'), 
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['firstpcttime'] = array(
  'name'     => 'Firstpcttime',
  'select'   => 'T',
  'mask'     => '%3.0f %%',
  'maxlen'   => 20,
  'sort'     => true
);
$opts['fdd']['depend_on'] = array(
  'name'     => 'Depend on',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['qualifying_races'] = array(
  'name'     => 'Qualifying races',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['idchallenge'] = array(
  'name'     => 'Idchallenge',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['coastpenalty'] = array(
  'name'     => 'Coastpenalty',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['bobegin'] = array(
  'name'     => 'Bobegin',
  'select'   => 'T',
  'maxlen'   => 20,
  'sql|LFVD' => 'IF (bobegin=0,\'\',FROM_UNIXTIME(bobegin))',
  'default'  => '0',
  'calendar' => $calendar_specifications,
  'sort'     => true
);
$opts['fdd']['boend'] = array(
  'name'     => 'Boend',
  'select'   => 'T',
  'maxlen'   => 20,
  'sql|LFVD' => 'IF (boend=0,\'\',FROM_UNIXTIME(boend))',
  'default'  => '0',
  'calendar' => $calendar_specifications,
  'sort'     => true
);
$opts['fdd']['maxboats'] = array(
  'name'     => 'Maxboats',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['theme'] = array(
  'name'     => 'Theme',
  'select'   => 'D',
  'maxlen'   => 30,
  'values'   => $list_themes,
  'sort'     => true
);


include('adminfooter.php');

?>