<?php

// headers
$PAGETITLE = "Admin of TROPHIES";

include('adminheader.php');

/* TROPHIES TABLE */

$opts['tb'] = 'trophies';

// Name of field which is the unique key
$opts['key'] = 'idTrophies';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('idTrophies');

$opts['fdd']['idTrophies'] = array(
  'name'     => '+Id',
  'help'     => 'Unique id of the Trophies',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['name'] = array(
  'name'     => 'Trophy',
  'help'     => 'Short name of the trophy',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 50,
  'sort'     => true
);

$opts['fdd']['description'] = array(
  'name'     => 'Description',
  'help'     => 'Description of the Trophy',
  'select'   => 'T',
  'escape'   => true,
  'options'  => 'ACDPVF',
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['ControlPeriod'] = array(
  'name'     => 'ControlPeriod',
  'help'     => 'in seconds',
  'select'   => 'T',
  'escape'   => true,
  'options'  => 'LACDPVF',
  'maxlen'   => 12,
  'sort'     => true
);


// FIXME : datetime for LastRun


include('adminfooter.php');

?>
