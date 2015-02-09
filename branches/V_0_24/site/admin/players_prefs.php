<?php

// headers
$PAGETITLE = "Admin of Players_prefs table [EXPERIMENTAL]";

include('adminheader.php');
require('playersPrefs.class.php');
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

$opts['fdd']['permissions'] = array(
  'name'     => 'Permissions',
  'help'     => nl2br("Permissions"),
  'select|FLDV'   => 'M',
  'select|ACP'   => 'C',
  'values2' => Array(
      VLM_ACL_BOATSIT => "Boatsitter",
      VLM_ACL_AUTH => "VLM Players",
      ),
  'sql' => 'MAKE_SET(`PMEtable0`.`permissions`, 1, 2)',
//  'maxlen'   => 11,
//  'default'  => '0',
  'sort'     => true
);


$opts['fdd']['updated'] = array(
  'name'     => 'Last change',
  'help'     => 'Date of last change',
  'input'   => 'R',
  'options'  => 'VL'
);

$opts['triggers']['update']['before'][0] = 'players_prefs.TBU.trigger.php';

//force basic pme class.
require_once('../externals/phpMyEdit/phpMyEdit.class.php');
$pmeinstance = new phpMyEdit($opts);

include('adminfooter.php');

?>
