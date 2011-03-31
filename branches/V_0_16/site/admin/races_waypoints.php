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
$opts['sort_field'] = array('-idwaypoint');


$opts['fdd']['idraces'] = array(
  'name'     => '#IdRaces',
  'help'     => 'Unique id of the race',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['idwaypoint'] = array(
  'name'     => '#IdWaypoint',
  'help'     => 'Unique id of the waypoint (or gate)',
  'select'   => 'T',
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['wpformat'] = array(
  'name'     => 'Wpformat',
  'help'     => nl2br("Wp format"),
  'select|FLDV'   => 'M',
  'select|ACP'   => 'C',
  'values2' => Array(
      WP_ONE_BUOY => "WP_ONE_BUOY",
      WP_ICE_GATE_N => "WP_ICE_GATE_N",
      WP_ICE_GATE_S => "WP_ICE_GATE_S",
      WP_ICE_GATE_E => "WP_ICE_GATE_W (Unused)",
      WP_ICE_GATE_W => "WP_ICE_GATE_E (Unused)",
      WP_CROSS_CLOCKWISE => "WP_CROSS_CLOCKWISE",
      WP_CROSS_ANTI_CLOCKWISE => "WP_CROSS_ANTI_CLOCKWISE",
      WP_CROSS_ONCE => "WP_CROSS_ONCE",
      ),
  'sql' => 'MAKE_SET(`wpformat`, 1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 4096)',
//  'maxlen'   => 11,
//  'default'  => '0',
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

$opts['triggers']['update']['pre'][0] = 'waypoints.img.trigger.php';
$opts['triggers']['select']['pre'][0] = 'waypoints.img.trigger.php';
$opts['triggers']['delete']['pre'][0] = 'waypoints.img.trigger.php';
$opts['triggers']['update']['before'][0] = 'races_waypoints.TBU.trigger.php';

//force basic pme class.
require_once('../externals/phpMyEdit/phpMyEdit.class.php');
$pmeinstance = new phpMyEdit($opts);

include('adminfooter.php');

?>
