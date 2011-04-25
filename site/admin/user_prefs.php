<?php

// headers
$PAGETITLE = "Admin of User_prefs table [EXPERIMENTAL]";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'user_prefs';

// Name of field which is the unique key
$opts['key'] = 'idusers_prefs';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-idusers_prefs');

$opts['fdd']['idusers_prefs'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the prefs',
  'select'   => 'T',
  'input' => 'R',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['idusers'] = array(
  'name'     => '#IdUser',
  'select'   => 'D',
  'input|C'  => 'R',
  'maxlen'   => 11,
  'values'   => Array('table' => 'users',
                      'column' => 'idusers',
                      'description' => Array(
                             'columns' => Array(0 => 'username', 1 => 'idusers'),
                             'divs'    => Array(0 => ' #',),
                         ),
                ),  
  'sort'     => true
);

$opts['fdd']['pref_name'] = array(
  'name'     => 'PrefName',
  'select'   => 'D',
  'input|C'  => 'R',
  'values'  => explode(',', USER_PREF_ALLOWED),
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['pref_value'] = array(
  'name'     => 'Value',
  'select'   => 'T',
  'maxlen'   => 255,
  'sort'     => false
);

$opts['fdd']['updated'] = array(
  'name'     => 'Last change',
  'help'     => 'Date of last change',
  'input'   => 'R',
  'options'  => 'VL'
);

include('adminfooter.php');

?>
