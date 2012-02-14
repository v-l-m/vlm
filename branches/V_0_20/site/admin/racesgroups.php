<?php

// headers
$PAGETITLE = "Admin of RACESGROUPS table";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'racesgroups';

// Name of field which is the unique key
$opts['key'] = 'idracesgroups';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-updated');

//Fields definitions

$opts['fdd']['idracesgroups'] = array(
  'name'     => '#Id',
  'help'     => 'Unique id of the group',
  'select'   => 'T',
  'input'  => 'R',
  'options' => 'H',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

/*
$opts['fdd']['raceminimap'] = array(
  'name'     => 'Minimap',
  'options'  => 'LDVF',
  //'select'   => 'T',
  'escape'   => 0,
  'sql'      => 'idraces', 
  'mask'     => "<img style=\"height:40px; \" src=\"/cache/minimaps/%s.png\" />",
  'URL'      => '/admin/uploadracemap.php?idnewrace=$key',
  'help'     => 'Click the minimapto upload the racemap',
  'input'    => 'R',
);
*/
$opts['fdd']['grouptag'] = array(
  'name'     => 'Unique name of the group',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Example : \'VOR2011\'. Do not use spaces, please', 
  'maxlen'   => 32,
  'sort'     => true
);

$opts['fdd']['groupname'] = array(
  'name'     => 'Name (shortname) of the group',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Example : \'VOR 2011\'.', 
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['grouptitle'] = array(
  'name'     => 'Title (longname) of the group',
  'select'   => 'T',
  'escape'   => false,
  'help'     => 'Example : Virtual Ocean Race 2011. Basic HTML is for now allowed but discouraged', 
  'maxlen'   => 255,
  'sort'     => true
);

$opts['fdd']['description'] = array(
  'name'     => 'Description',
  'help'     => 'Public description',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 250,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['admincomments'] = array(
  'name'     => 'Admin Comments',
  'help'     => 'Admin comments',
  'options'  => 'ACPDV',
  'select'   => 'T',
  'maxlen'   => 250,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
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

include('adminfooter.php');

?>
