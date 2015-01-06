<?php

// headers
$PAGETITLE = "Admin of PLAYERS table";

include('adminheader.php');

/* PLAYERS TABLE */

$opts['tb'] = 'players';

// Name of field which is the unique key
$opts['key'] = 'idplayers';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-updated');

//Fields definitions

$opts['fdd']['idplayers'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the player',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['email'] = array(
  'name'     => 'Email',
  'select'   => 'T',
  'help'     => 'Email of the player (unique id)', 
  'maxlen'   => 50,
  'sort'     => true
);
$opts['fdd']['playername'] = array(
  'name'     => 'Playername',
  'select'   => 'T',
  'help'     => 'Name of the player', 
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['permissions'] = array(
  'name'     => 'Permissions',
  'select'   => 'D',
  'help'     => 'Perms of the player', 
  'values2'  => array(0 => "Default", VLM_PLAYER_ADMIN => "Admin",),
  'sort'     => true
);
$opts['fdd']['updated'] = array(
  'name'     => 'Updated',
  'input'    => 'R',
//  'sql|LFVD' => 'FROM_UNIXTIME(updated)',
  'maxlen'   => 20,
  'sort'     => true,
  'help'=> 'Last change',
);

//$opts['triggers']['delete']['before'][0] = 'races.TBD.trigger.php';
$opts['triggers']['delete']['after'][0] = 'players.TAD.trigger.php';
//$opts['triggers']['insert']['pre'][0] = 'races.TPI.trigger.php';
//$opts['triggers']['insert']['after'][0] = 'races.TAI.trigger.php';
//$opts['triggers']['select']['pre'][0] = 'races.TPS.trigger.php';

include('adminfooter.php');

?>
