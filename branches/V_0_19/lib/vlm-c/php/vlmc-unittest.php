<?php

# $Id: vlmc-unittest.php,v 1.2 2008/05/30 14:07:33 ylafon Exp $
#
# (c) 2008 by paparazzia adapted from unittest.c by ylafon
#      See COPYING file for copying and redistribution conditions.
#
#      This program is free software you can redistribute it and/or modify
#      it under the terms of the GNU General Public License as published by
#      the Free Software Foundation version 2 of the License.
#
#      This program is distributed in the hope that it will be useful,
#      but WITHOUT ANY WARRANTY without even the implied warranty of
#      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#      GNU General Public License for more details.
#
# Contact: <yves@raubacapeu.net>

include("vlmc.php");

init_context_default();

$lat1  = deg2rad(10);
$long1 = deg2rad(10);

$lat2  = deg2rad(11.1);
$long2 = deg2rad(9);

$lat3  = deg2rad(30);
$long3 = deg2rad(-10);

$lata  = deg2rad(10);
$longa = deg2rad(9);

$latb  = deg2rad(11.1);
$longb = deg2rad(10);

print("Unit test: distance from Point 1 to Line A-B\n");

printf("Point1: lat %.3f, long %.3f\n", rad2deg($lat1), rad2deg($long1));
printf("Line A: lat %.3f, long %.3f\n", rad2deg($lata), rad2deg($longa));
printf("Line B: lat %.3f, long %.3f\n", rad2deg($latb), rad2deg($longb));

printf("Distance Point1 -> Point A: %.3f\n", ortho_distance($lat1, $long1, $lata, $longa));
printf("Distance Point1 -> Point B: %.3f\n", ortho_distance($lat1, $long1, $latb, $longb));
printf("Distance Point1 -> Line A-B: %.3f\n", distance_to_line($lat1, $long1, $lata, $longa, $latb, $longb));

printf("\nUnit test: distance from Point 2 to Line A-B\n");
printf("Point2: lat %.3f, long %.3f\n",rad2deg($lat2), rad2deg($long2));
printf("Line A: lat %.3f, long %.3f\n", rad2deg($lata), rad2deg($longa));
printf("Line B: lat %.3f, long %.3f\n", rad2deg($latb), rad2deg($longb));

printf("Distance Point2 -> Point A: %.3f\n", ortho_distance($lat2, $long2, $lata, $longa));
printf("Distance Point2 -> Point B: %.3f\n", ortho_distance($lat2, $long2, $latb, $longb));
printf("Distance Point2 -> Line A-B: %.3f\n", distance_to_line($lat2, $long2, $lata, $longa, $latb, $longb));

printf("\nUnit test: distance from Point 3 to Line A-B\n");
printf("Point3: lat %.3f, long %.3f\n", rad2deg($lat3), rad2deg($long3));
printf("Line A: lat %.3f, long %.3f\n", rad2deg($lata), rad2deg($longa));
printf("Line B: lat %.3f, long %.3f\n", rad2deg($latb), rad2deg($longb));

printf("Distance Point3 -> Point A: %.3f\n", ortho_distance($lat3, $long3, $lata, $longa));
printf("Distance Point3 -> Point B: %.3f\n", ortho_distance($lat3, $long3, $latb, $longb));
printf("Distance Point3 -> Line A-B: %.3f\n", distance_to_line($lat3, $long3, $lata, $longa, $latb, $longb));

printf("\nWind test\n");
init_grib();

# commented out for now, but it seems to work
# init_coastline()

$current_time = time();
print($current_time);

$lat_boat     = deg2rad(39.812);
$long_boat    = deg2rad(8.43);

$wind_boat = new wind_info();

if (in_vlm_compat_mode()) {
  printf("\nVLM_COMPAT mode\n");
} else {
  printf("\nnormal mode\n");
}
for($i =0; $i < 4 ; $i++ ) {

    printf("Date: %s GMT\n", gmdate("Y-m-d:H:i:s", $current_time));
    # each 15mn
    get_wind_info_latlong_UV($lat_boat, $long_boat, $current_time + (15 * 60 * $i), $wind_boat);
    printf("UV   Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n", rad2deg($lat_boat),
          rad2deg($long_boat), $wind_boat->speed, rad2deg($wind_boat->angle));
    get_wind_info_latlong_TWSA($lat_boat, $long_boat, $current_time + (15 * 60 * $i), $wind_boat);
    printf("TWSA Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n", rad2deg($lat_boat), rad2deg($long_boat), $wind_boat->speed, rad2deg($wind_boat->angle) );
}

printf("\nWaypoint check\n");
$crossing_time = new longp();
$fake_waypoint = new waypoint();
$fake_waypoint->latitude1 = deg2rad(40);
$fake_waypoint->longitude1 = deg2rad(-47);
$fake_waypoint->latitude2 = deg2rad(40);
$fake_waypoint->longitude2 = deg2rad(-50);
$fake_waypoint->type = 0;

if ( check_waypoint_crossed( deg2rad(39.8), deg2rad(-47), $current_time - 1000,
           deg2rad(40.3), deg2rad(-47), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("First waypoint crossed at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Edge case 1 failed\n");
}

if ( check_waypoint_crossed( deg2rad(39.8), deg2rad(-49), $current_time - 1000,
           deg2rad(40.3), deg2rad(-49), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("Second waypoint crossed at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Middle case 2 failed\n");
}

if ( check_waypoint_crossed( deg2rad(39.8), deg2rad(-50), $current_time - 1000,
           deg2rad(40.15), deg2rad(-50), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("Third waypoint crossed at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Edge case 3 failed\n");
}

if ( check_waypoint_crossed( deg2rad(39.8), deg2rad(-50.1), $current_time -1000,
           deg2rad(40.15), deg2rad(-50.1), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("Fourth waypoint crossed (FAILED) at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Edge case 4 succeeded\n");
}

if ( check_waypoint_crossed( deg2rad(39.5), deg2rad(-49.5), $current_time -1000,
           deg2rad(40.5), deg2rad(-50.5), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("Fifth waypoint crossed at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Edge case 5 failed\n");
}

$fake_waypoint->latitude1 = deg2rad(41);
$fake_waypoint->longitude1 = deg2rad(-50);
$fake_waypoint->latitude2 = deg2rad(40);
$fake_waypoint->longitude2 = deg2rad(-50);

if ( check_waypoint_crossed( deg2rad(39.5), deg2rad(-49.5), $current_time -1000,
           deg2rad(40.5), deg2rad(-50.5), $current_time,
                             $fake_waypoint, $crossing_time)) {
  printf("Sixth waypoint crossed at %ld (%ld) -> %s\n", longp_value($crossing_time), longp_value($crossing_time) - $current_time + 1000, gmdate("Y-m-d:G:i:s", longp_value($crossing_time)));
} else {
  printf("Edge case 6 failed\n");
}

?>
