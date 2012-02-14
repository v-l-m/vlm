<?php

// headers
$PAGETITLE = "Admin of RACESTOGROUPS table";

include('adminheader.php');

/* PLAYERS TABLE */

$opts['tb'] = 'racestogroups';

// Name of field which is the unique key
$opts['key'] = 'idracestogroups';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-updated');

//Fields definitions

$opts['fdd']['idracestogroups'] = array(
  'name'     => '#Id',
  'help'     => 'Internal id',
  'select'   => 'T',
  'input'  => 'R',
  'options' => 'H',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);


$opts['fdd']['idraces'] = array(
  'name'     => 'Races ~id',
  'help'     => 'Which race is linked',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'races',
                    'column' => 'idraces',
                    'description' => Array(
                        'columns' => Array('racename', 'idraces'),
                        'divs' => Array(' ~'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['grouptag'] = array(
  'name'     => 'racesgroups tag',
  'help'     => 'Which group is linked',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'racesgroups',
                    'column' => 'grouptag',
                    'description' => Array(
                        'columns' => Array('groupname', 'grouptag'),
                        'divs' => Array(' *'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
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
