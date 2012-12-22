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


$calendar_specifications = array(
     'ifFormat'    => '%Y-%m-%d %H:%M:%S', // defaults to the ['strftimemask']
     'firstDay'    => 1,          // 0 = Sunday, 1 = Monday
     'singleClick' => true,       // single or double click to close
     'weekNumbers' => true,       // Show week numbers
     'showsTime'   => true,      // Show time as well as date
     'timeFormat'  => '24',       // 12 or 24 hour clock
     'button'      => true,       // Display button (rather then clickable area)
     'label'       => '...',      // button label (used by phpMyEdit)
     );

//Fields definitions

$opts['fdd']['idUsersTrophies'] = array(
  'name'     => '#Id',
  'help'     => 'Internal id',
  'select'   => 'T',
  'input'  => 'R',
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
  'name'     => 'Trophy +Id',
  'help'     => 'Which Trophy ?',
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

$opts['fdd']['joindate'] = array(
  'name'     => 'Join Date',
  'select'   => 'T',
  'maxlen'   => 20,
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'Starting time',
  'sort'     => true
);

$opts['fdd']['quitdate'] = array(
  'name'     => 'Leave Date',
  'select'   => 'T',
  'maxlen'   => 20,
  'calendar' => $calendar_specifications,
  'help|LVFD'=> 'Leave time',
  'sort'     => true
);



include('adminfooter.php');

?>
