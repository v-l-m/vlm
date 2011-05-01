<?php

// headers
$PAGETITLE = "Admin of PLAYERSTOUSERS table";

include('adminheader.php');

/* PLAYERS TABLE */

$opts['tb'] = 'playerstousers';

// Name of field which is the unique key
$opts['key'] = 'idplayerstousers';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-updated');

//Fields definitions

$opts['fdd']['idplayerstousers'] = array(
  'name'     => '#Id',
  'help'     => 'Internal id',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);


$opts['fdd']['idplayers'] = array(
  'name'     => 'playername @id',
  'help'     => 'Which player is linked',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'players',
                    'column' => 'idplayers',
                    'description' => Array(
                        'columns' => Array('playername', 'idplayers'),
                        'divs' => Array(' @'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['idusers'] = array(
  'name'     => 'user #id',
  'help'     => 'Which user is linked',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'users',
                    'column' => 'idusers',
                    'description' => Array(
                        'columns' => Array('username', 'idusers'),
                        'divs' => Array(' #'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['linktype'] = array(
  'name'     => 'linktype',
  'select'   => 'D',
  'help'     => 'Type of link between player & user', 
  'values2'  => Array(1 => "Owner", 2 => "Boatsitter"),
  'default'   => 0,
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
//$opts['triggers']['delete']['after'][0] = 'races.TAD.trigger.php';
//$opts['triggers']['insert']['pre'][0] = 'races.TPI.trigger.php';
//$opts['triggers']['insert']['after'][0] = 'races.TAI.trigger.php';
//$opts['triggers']['select']['pre'][0] = 'races.TPS.trigger.php';

include('adminfooter.php');

?>
