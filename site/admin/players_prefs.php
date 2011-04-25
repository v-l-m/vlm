<?php

// headers
$PAGETITLE = "Admin of Players_prefs table [EXPERIMENTAL]";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'players_prefs';

// Name of field which is the unique key
$opts['key'] = 'idplayers_prefs';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-idplayers_prefs');

$opts['fdd']['idplayers_prefs'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the prefs',
  'select'   => 'T',
  'input' => 'R',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['idplayers'] = array(
  'name'     => '@IdPlayer',
  'select'   => 'T',
  'maxlen'   => 11,
  'input|C'  => 'R',
  'values'   => Array('table' => 'players',
                      'column' => 'idplayers',
                      'description' => Array(
                             'columns' => Array(0 => 'playername', 1 => 'idplayers'),
                             'divs'    => Array(0 => ' @',),
                         ),
                ),
//  'URL'      => PROFILE_PLAYER_URL.'$val',
  'sort'     => true
);

$opts['fdd']['pref_name'] = array(
  'name'     => 'PrefName',
  'select'   => 'D',
  'input|C'  => 'R',
  'values'  => explode(',', PLAYER_PREF_ALLOWED),
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
