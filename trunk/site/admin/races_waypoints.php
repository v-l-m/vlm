<?php

// headers
$PAGETITLE = "Admin of Races_waypoints table [EXPERIMENTAL, Use in emergency only]";

include('adminheader.php');

/* RACE TABLE */

$opts['tb'] = 'races_waypoints';

// Name of field which is the unique key
// WARNING : this is true because of the 'filters' statement.
$opts['key'] = 'idwaypoint';
$opts['key_type'] = 'int';

$opts['filters'] = "idwaypoint NOT IN (SELECT fooalias.idwaypoint FROM races_waypoints AS fooalias GROUP BY idwaypoint HAVING count(*) > 1)";

// Sorting field(s)
$opts['sort_field'] = array('idwaypoint');


$opts['fdd']['idwaypoint'] = array(
  'name'     => '#IdWaypoint',
  'help'     => 'Unique id of the waypoint (or gate)',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['idraces'] = array(
  'name'     => '#IdRaces',
  'help'     => 'Unique id of the race',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['wporder'] = array(
  'name'     => '#Wporder',
  'help'     => 'Unique id of the waypoint for the given race',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['laisser_au'] = array(
  'name'     => 'Laisser au',
  'help'     => 'If 999 we define a gate, else we define a waypoint',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => 999,
  'sort'     => true
);

$opts['fdd']['wptype'] = array(
  'name'     => 'Wp Type',
  'select'   => 'T',
  'maxlen'   => 32,
  'default'  => 'classement',
  'help'     => 'Type of the Wp : Finish, classement, ...',
  'sort'     => true
);

include('adminfooter.php');

?>
