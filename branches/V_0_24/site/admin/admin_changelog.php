<?php

// headers
$PAGETITLE = "Browsing of Admin_changelog table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'admin_changelog';

// Name of field which is the unique key
$opts['key'] = 'id';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-updated');

$opts['options'] = 'LVF';

$opts['fdd']['id'] = array(
  'name'     => 'id',
  'options'   => 'H',
);


$opts['fdd']['updated'] = array(
  'name'     => 'Time',
  'select'   => 'T',
//  'sql|LFVD' => 'FROM_UNIXTIME(updated)',
  'maxlen'   => 20,
  'sort'     => true
);

$opts['fdd']['user'] = array(
  'name'     => 'admin',
  'select'   => 'T',
  'maxlen'   => 20,
  'sort'     => true
);

$opts['fdd']['host'] = array(
  'name'     => 'IP',
  'select'   => 'T',
  'URL'      => 'http://whois.domaintools.com/$value',
  'URLtarget' => '_blank',
  'escape'   => true,
  'maxlen'   => 16,
  'sort'     => true
);

$opts['fdd']['operation'] = array(
  'name'     => 'operation',
  'select'   => 'T',
  'maxlen'   => 20,
  'sort'     => true
);
$opts['fdd']['tab'] = array(
  'name'     => 'table',
  'select'   => 'T',
  'maxlen'   => 20,
  'sort'     => true
);
$opts['fdd']['rowkey'] = array(
  'name'     => 'rowkey',
  'select'   => 'T',
  'maxlen'   => 20,
  'sort'     => true
);
$opts['fdd']['col'] = array(
  'name'     => 'field',
  'select'   => 'T',
  'maxlen'   => 20,
  'sort'     => true
);
$opts['fdd']['oldval'] = array(
  'name'     => 'oldval',
  'select'   => 'T',
  'sql|LF'      => 'left(oldval, 20)',
  'maxlen|V'   => 255,
  'maxlen|LF'   => 20,
  'sort'     => true
);
$opts['fdd']['newval'] = array(
  'name'     => 'newval',
  'select'   => 'T',
  'sql|LF'      => 'left(newval, 20)',
  'maxlen|V'   => 255,
  'maxlen|LF'   => 20,
  'sort'     => true
);

include('adminfooter.php');

?>
