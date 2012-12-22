<?php

// headers
$PAGETITLE = "Admin of Users_Trophies table";

include('adminheader.php');

/* Users_Trophies TABLE */

$opts['tb'] = 'users_Trophies';

// Name of field which is the unique key
$opts['key'] = 'idUsersTrophies';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('idUsersTrophies');

//Fields definitions

$opts['fdd']['idUsersTrophies'] = array(
  'name'     => '#Id',
  'help'     => 'Internal id',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
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

$opts['fdd']['idraces'] = array(
  'name'     => 'Race',
  'help'     => 'Which race',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'races',
                    'column' => 'idraces',
                    'filters' => 'started >= 0',
                    'description' => Array(
                        'columns' => Array('racename', 'idraces'),
                        'divs' => Array(' ~'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['RefTrophy'] = array(
  'name'     => 'user #id',
  'help'     => 'Which Trophy',
  'select'   => 'T',
  'values'   => Array(
                    'table' => 'trophies',
                    'column' => 'idTrophies',
//                    'filters' => 'started >= 0',
                    'description' => Array(
                        'columns' => Array('name', 'idTrophies'),
                        'divs' => Array(' +'),
                    ),
                ),    
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

//FIXME : DAtetime handling

$opts['fdd']['joindate'] = array(
  'name'     => 'Close time',
  'select'   => 'T',
//  'sql|LFVD' => 'FROM_UNIXTIME(closetime)',
  'maxlen'   => 20,
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'Close time',
  //FIXME: on peut mieux faire / on peut factoriser
  'help|PCA' => '<span id="PME_dhtml_fld_closetime_help" onClick="computeFromEpoc(\'PME_dhtml_fld_closetime\');">Click to check the EPOC value</span>',
//  'js'       => Array('required' => true, 'regexp' => '/^[0-9]+$/'),
  'sort'     => true
);


include('adminfooter.php');

?>
