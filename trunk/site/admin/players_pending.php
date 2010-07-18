<?php

// headers
$PAGETITLE = "Admin of PLAYERS_PENDING table";

include('adminheader.php');

/* PLAYERS TABLE */

$opts['tb'] = 'players_pending';

// Name of field which is the unique key
$opts['key'] = 'idplayers_pending';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-idplayers_pending');

//Fields definitions

$opts['fdd']['idplayers_pending'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the request',
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

$opts['fdd']['seed'] = array(
  'name'     => 'Seed',
  'select'   => 'T',
  'help'     => 'Seed for the request', 
  'maxlen'   => 11,
  'sort'     => false
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
//$opts['triggers']['delete']['after'][0] = 'races.TAD.trigger.php';
//$opts['triggers']['insert']['pre'][0] = 'races.TPI.trigger.php';
//$opts['triggers']['insert']['after'][0] = 'races.TAI.trigger.php';
//$opts['triggers']['select']['pre'][0] = 'races.TPS.trigger.php';

include('adminfooter.php');

?>
