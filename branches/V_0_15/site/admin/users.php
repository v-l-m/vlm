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
  'name'     => 'Boatpseudo',
  'help'     => 'Pseudo of the boat (old-username)',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['boatname'] = array(
  'name'     => 'Boatname',
  'help'     => 'Name of the boat (visible with mouse_over)',
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

$opts['fdd']['engaged'] = array(
  'name'     => 'Engaged',
  'help'     => 'Race the boat is engaged to',
  'select'   => 'T',
  'escape'   => true,
  'options'  => 'LACDPVF',
  'input'    => 'R',
  'maxlen'   => 12,
  'sort'     => true
);

$opts['fdd']['email'] = array(
  'name'     => 'eMail',
  'help'     => 'DEPRECATED - Private mail address',
  'select'   => 'T',
  'escape'   => true,
  'options'  => 'ACDPVF',
  'maxlen'   => 200,
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
  'select'   => 'D',
  'options'  => 'ACDPVF',
  'maxlen'   => 30,
  'values'   => $list_themes,
  'sort'     => true
);

$opts['fdd']['color'] = array(
  'name'     => 'Color',
  'help'     => 'Hidden if negative',
  'select'   => 'T',
  'options'  => 'ACDPVF',
  'maxlen'   => 8,
  'sort'     => true
);

$opts['fdd']['releasetime'] = array(
  'name'     => 'Release Time',
  'help'     => 'Time until boat is locked',
  'select'   => 'T',
  'options'  => 'CDV',
  'maxlen'   => 8,
  'sort'     => true
);

//RO fields

$opts['fdd']['boatheading'] = array(
  'name'     => 'Boat Heading',
  'help'     => 'Boat heading',
  'input'   => 'R',
  'options'  => 'V'
);

$opts['fdd']['pilotmode'] = array(
  'name'     => 'PIM',
  'help'     => 'PIlot Mode',
  'input'   => 'R',
  'options'  => 'V'
);

$opts['fdd']['pilotparameter'] = array(
  'name'     => 'PIP',
  'help'     => 'PIlot Parameter',
  'input'   => 'R',
  'options'  => 'V'
);

$opts['fdd']['lastchange'] = array(
  'name'     => 'Last change',
  'help'     => 'Date of last change',
  'input'   => 'R',
  'sql|LFVD' => 'IF (lastchange=0,\'\',FROM_UNIXTIME(lastchange))',
  'options'  => 'VL'
);

$opts['fdd']['nextwaypoint'] = array(
  'name'     => 'Next WP',
  'help'     => 'Number of next waypoint',
  'options'  => 'CVF'
);

$opts['fdd']['userdeptime'] = array(
  'name'     => 'User Deptime',
  'help'     => 'Time boat started racing',
  'input'   => 'R',
  'sql|LFVD' => 'IF (userdeptime=0,\'\',FROM_UNIXTIME(userdeptime))',
  'options'  => 'CV'
);

$opts['fdd']['mooringtime'] = array(
  'name'     => 'Mooring time',
  'help'     => 'Time boat is mooring',
  'input'   => 'R',
  'sql|LFVD' => 'IF (mooringtime=0,\'\',FROM_UNIXTIME(mooringtime))',
  'options'  => 'CV'
);

$opts['fdd']['loch'] = array(
  'name'     => 'Loch',
  'help'     => 'Loch since the race started',
  'input'   => 'R',
  'options'  => 'V'
);

$opts['fdd']['ipaddr'] = array(
  'name'     => 'IP Addr',
  'help'     => 'Last used IP address',
  'input'   => 'R',
  'options'  => 'V'
);

/* Doesn't work, it seems this field is not used.
$opts['fdd']['hidepos'] = array(
  'name'     => 'Pos. hidden',
  'select'   => 'D',
  'options'  => 'ACDPVF',
  'values2'  => Array(0 => 'Visible', 1 => 'Hidden'),
  'sort'     => true
);
*/


include('adminfooter.php');

?>
