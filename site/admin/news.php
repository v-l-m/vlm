<?php

// headers
$PAGETITLE = "Browsing NEWS feed - Use with caution !";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'news';

// Name of field which is the unique key
$opts['key'] = 'idnews';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-published');

$opts['options'] = 'ACPVLF';

$opts['fdd']['idnews'] = array(
  'name'     => '#Id',
  'help'     => 'Internal id',
  'select'   => 'T',
  'input|LVCD'  => 'R',
  'input|AP' => '',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['media'] = array(
  'name'     => 'media',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 32,
  'sort'     => true
);

/*
$opts['fdd']['hashnews'] = array(
  'name'     => 'hashnews',
  'help'     => 'unique id from media, message',
  'select'   => 'T',
  'input'  => 'R',
  'sqlw|CA'     => 'md5(concat(summary, timetarget, media))',
  'escape'   => true,
  'maxlen'   => 32,
  'sort'     => true
);
*/

$opts['fdd']['summary'] = array(
  'name'     => 'title',
  'help'     => '(140 characters, for irc and twitter, mainly)',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 140,
  'sort'     => true
);

$opts['fdd']['longstory'] = array(
  'name'     => 'message',
  'help'     => 'Long story',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 140,
  'sort'     => true
);

$opts['fdd']['url'] = array(
  'name'     => 'url',
  'options'  => 'ACDVP',
  'help'     => 'Url associée à la news',
  'select'   => 'T',
  'escape'   => true,
  'maxlen'   => 250,
  'sort'     => true
);


$opts['fdd']['timetarget'] = array(
  'name'     => 'News date',
  'select'   => 'T',
  'sql|LVF'  => 'FROM_UNIXTIME(timetarget)',
  'sort'     => true
);

$opts['fdd']['published'] = array(
  'name'     => 'Published',
  'select'   => 'T',
  'sql|LVF'  => 'FROM_UNIXTIME(published)',
  'sort'     => true
);

include('adminfooter.php');

?>
