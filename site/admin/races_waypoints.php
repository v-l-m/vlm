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
  'help'     => nl2br("Wp format (for future v0.14)
#define WP_TWO_BUOYS 0
#define WP_ONE_BUOY  1
#define WP_GATE_BUOY_MASK 0x000F
/* leave space for 0-15 types of gates using buoys
   next is bitmasks */
#define WP_DEFAULT              0
#define WP_ICE_GATE_N           (1 <<  4)
#define WP_ICE_GATE_S           (1 <<  5)
#define WP_ICE_GATE_E           (1 <<  6)
#define WP_ICE_GATE_W           (1 <<  7)
#define WP_GATE_KIND_MASK       0x00F0
/* allow crossing in one direction only */
#define WP_CROSS_CLOCKWISE      (1 <<  8)
#define WP_CROSS_ANTI_CLOCKWISE (1 <<  9)
/* for future releases */
#define WP_CROSS_ONCE           (1 << 10)
"),
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
