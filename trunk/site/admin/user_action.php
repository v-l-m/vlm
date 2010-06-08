<?php

// headers
$PAGETITLE = "Browsing of User_action table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'user_action';

// Name of field which is the unique key
$opts['key'] = 'idusers';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-time');

$opts['options'] = 'LF';

$opts['fdd']['time'] = array(
  'name'     => 'Time',
  'select'   => 'T',
//  'sql|LFVD' => 'FROM_UNIXTIME(time)',
  'maxlen'   => 20,
  'sort'     => true
);


$opts['fdd']['idusers'] = array(
  'name'     => '#IdUser',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);

$opts['fdd']['ipaddr'] = array(
  'name'     => 'IP',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 16,
  'sort'     => true
);

$opts['fdd']['fullipaddr'] = array(
  'name'     => 'FULLIP',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 256,
//  'options'  => 'FV',
  'sort'     => true
);

$opts['fdd']['idraces'] = array(
  'name'     => '#IdRaces',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);


$opts['fdd']['action'] = array(
  'name'     => 'Action',
  'select'   => 'T',
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['useragent'] = array(
  'name'     => 'Useragent',
  'sql'     => "concat('<span title=\"', useragent, '\">(user agent)</span>')",
  'options'   => 'LFU',
  'escape'   => false,
  'sort'     => false
);

$opts['fdd']['actionserver'] = array(
  'name'     => 'Action Server',
  'select'   => 'T',
  'maxlen'   => 32,
  'sort'     => true
);

include('adminfooter.php');

?>
