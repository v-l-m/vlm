/**
 * $Id: unittest.c,v 1.15 2008/07/08 14:11:11 ylafon Exp $
 *
 * (c) 2008 by Yves Lafon
 *      See COPYING file for copying and redistribution conditions.
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; version 2 of the License.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 * Contact: <yves@raubacapeu.net>
 */

#include <stdio.h>
#include <math.h>
#include <sys/time.h>
#include <string.h>
#include <stdlib.h>

#include "defs.h"
#include "types.h"
#include "ortho.h"
#include "lines.h"
#include "winds.h"
#include "grib.h"
#include "context.h"
#include "polar.h"
#include "waypoint.h"
#include "gshhs.h"

vlmc_context *global_vlmc_context;

int main (int argc, char **argv) {
  double lat1,long1, lat2, long2, lat3, long3;
  double lata, longa, latb, longb;
  
  double lat_boat, long_boat;
  time_t current_time, previ_time, crossing_time;
  int i;
  wind_info wind_boat;
  waypoint fake_waypoint;

  global_vlmc_context = calloc(1, sizeof(vlmc_context));
  init_context_default(global_vlmc_context);

  lat1 = degToRad(54.793253356367);
  long1 = degToRad(-163.35368070813);
  lat2 = degToRad(54.789622936359);
  long2 = degToRad(-163.36603822435);

  init_coastline();
  latb = check_coast(lat1, long1, lat2, long2, &lata, &longa);
  if (latb >= INTER_MIN_LIMIT && latb <= INTER_MAX_LIMIT) {
	printf("Coast crossed %.4f, %.4f\n", radToDeg(lata), radToDeg(longa));
  } else {
	printf("Coast avoided\n");
  }
  latb = intersects(lat1, long1, lat2, long2, degToRad(54.8089), degToRad(-163.381), degToRad(54.7696), degToRad(-163.348), &lata, &longa);
  if (latb >= INTER_MIN_LIMIT && latb <= INTER_MAX_LIMIT) {
        printf("Intersection %.4f, %.4f\n", radToDeg(lata), radToDeg(longa));
  } else {
        printf("No intersection\n");
  }

  lat1  = degToRad(10);
  long1 = degToRad(10);
  
  lat2  = degToRad(11.1);
  long2 = degToRad(9);

  lat3  = degToRad(30);
  long3 = degToRad(-10);

  lata  = degToRad(10);
  longa = degToRad(9);

  latb  = degToRad(11.1);
  longb = degToRad(10);

  printf("Unit test: distance from Point 1 to Line A-B\n");
  printf("Point1: lat %.3f, long %.3f\n", radToDeg(lat1), radToDeg(long1));
  printf("Line A: lat %.3f, long %.3f\n", radToDeg(lata), radToDeg(longa));
  printf("Line B: lat %.3f, long %.3f\n", radToDeg(latb), radToDeg(longb));

  printf("Distance Point1 -> Point A: %.3f\n", ortho_distance(lat1, long1,
							      lata, longa));
  printf("Distance Point1 -> Point B: %.3f\n", ortho_distance(lat1, long1,
							      latb, longb));
  printf("Distance Point1 -> Line A-B: %.3f\n", 
	 distance_to_line(lat1, long1, lata, longa, latb, longb));

  printf("\nUnit test: distance from Point 2 to Line A-B\n");
  printf("Point2: lat %.3f, long %.3f\n", radToDeg(lat2), radToDeg(long2));
  printf("Line A: lat %.3f, long %.3f\n", radToDeg(lata), radToDeg(longa));
  printf("Line B: lat %.3f, long %.3f\n", radToDeg(latb), radToDeg(longb));

  printf("Distance Point2 -> Point A: %.3f\n", ortho_distance(lat2, long2,
							      lata, longa));
  printf("Distance Point2 -> Point B: %.3f\n", ortho_distance(lat2, long2,
							      latb, longb));
  printf("Distance Point2 -> Line A-B: %.3f\n", 
	 distance_to_line(lat2, long2, lata, longa, latb, longb));

  printf("\nUnit test: distance from Point 3 to Line A-B\n");
  printf("Point3: lat %.3f, long %.3f\n", radToDeg(lat3), radToDeg(long3));
  printf("Line A: lat %.3f, long %.3f\n", radToDeg(lata), radToDeg(longa));
  printf("Line B: lat %.3f, long %.3f\n", radToDeg(latb), radToDeg(longb));

  printf("Distance Point3 -> Point A: %.3f\n", ortho_distance(lat3, long3,
							      lata, longa));
  printf("Distance Point3 -> Point B: %.3f\n", ortho_distance(lat3, long3,
							      latb, longb));
  printf("Distance Point3 -> Line A-B: %.3f\n", 
	 distance_to_line(lat3, long3, lata, longa, latb, longb));

  init_polar();
  printf("\nWind test\n");
  set_grib_filename(global_vlmc_context, "../datas/latest.grb");
  init_grib();
  purge_gribs();

  time(&current_time);
  lat_boat     = degToRad(39.812);
  long_boat    = degToRad(8.43);
  for (i=0; i<4; current_time += 15*60, i++) {
    printf("Date: %s", ctime(&current_time));
    /* each 15mn */
    get_wind_info_latlong_UV(lat_boat, long_boat, 
			     current_time,
			     &wind_boat);
    printf("UV   Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
    get_wind_info_latlong_TWSA(lat_boat, long_boat, 
			       current_time,
			       &wind_boat);
    printf("TWSA Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
  }
  current_time -= i*15*60;
  printf("\nmerging with latest24\n");
  set_grib_filename(global_vlmc_context, "../datas/latest24.grb");
  merge_gribs(1);
  for (i=0; i<4; current_time += 15*60, i++) {
    printf("Date: %s", ctime(&current_time));
    /* each 15mn */
    get_wind_info_latlong_UV(lat_boat, long_boat, 
			     current_time,
			     &wind_boat);
    printf("UV   Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
    get_wind_info_latlong_TWSA(lat_boat, long_boat, 
			       current_time,
			       &wind_boat);
    printf("TWSA Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
  }  
  current_time -= i*15*60;
  printf("init grib done, doing interp in UV_mode + merge24\n");
  interpolate_and_merge_grib();
  printf("done\n");
  previ_time = get_max_prevision_time();
  printf("Max prevision time: %s", ctime(&previ_time));
  previ_time = get_min_prevision_time();
  printf("Min prevision time: %s", ctime(&previ_time));

  for (i=0; i<4; current_time += 15*60, i++) {
    printf("Date: %s", ctime(&current_time));
    /* each 15mn */
    get_wind_info_latlong_UV(lat_boat, long_boat, 
			     current_time,
			     &wind_boat);
    printf("UV   Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
    get_wind_info_latlong_TWSA(lat_boat, long_boat, 
			       current_time,
			       &wind_boat);
    printf("TWSA Wind at  lat: %.2f long: %.2f, speed %.1f angle %.1f\n",
	   radToDeg(lat_boat), radToDeg(long_boat),
	   wind_boat.speed, radToDeg(wind_boat.angle));
  }
  current_time -= i*15*60;

  printf("\nWaypoint crossing:\n");
  fake_waypoint.latitude1  = degToRad(40);
  fake_waypoint.longitude1 = degToRad(-47);
  fake_waypoint.latitude2  = degToRad(40);
  fake_waypoint.longitude2 = degToRad(-50);
  fake_waypoint.type = 0;
  if (check_waypoint_crossed(degToRad(39.8), degToRad(-47), 
			     (time_t)(current_time - 1000),
			     degToRad(40.3), degToRad(-47),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("First waypoint crossed at %ld (%ld) -> %s", crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Edge case 1 failed\n");
  }
  if (check_waypoint_crossed(degToRad(39.8), degToRad(-49), 
			     (time_t)(current_time - 1000),
			     degToRad(40.3), degToRad(-49),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("Second waypoint crossed at %ld (%ld) -> %s", crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Middle case 2 failed\n");
  }
  if (check_waypoint_crossed(degToRad(39.8), degToRad(-50), 
			     (time_t)(current_time - 1000),
			     degToRad(40.15), degToRad(-50),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("Third waypoint crossed at %ld (%ld) -> %s", crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Edge case 3 failed\n");
  }
  if (check_waypoint_crossed(degToRad(39.8), degToRad(-50.1), 
			     (time_t)(current_time - 1000),
			     degToRad(40.15), degToRad(-50.1),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("Fourth waypoint crossed (FAILED!) at %ld (%ld) -> %s", 
	   crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Edge case 4 succeeded\n");
  }
  if (check_waypoint_crossed(degToRad(39.5), degToRad(-49.5), 
			     (time_t)(current_time - 1000),
			     degToRad(40.5), degToRad(-50.5),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("Fifth waypoint crossed at %ld (%ld) -> %s", crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Edge case 5 failed\n");
  }
  fake_waypoint.latitude1  = degToRad(41);
  fake_waypoint.longitude1 = degToRad(-50);
  fake_waypoint.latitude2  = degToRad(40);
  fake_waypoint.longitude2 = degToRad(-50);
  fake_waypoint.type = 0;
  if (check_waypoint_crossed(degToRad(39.5), degToRad(-49.5), 
			     (time_t)(current_time - 1000),
			     degToRad(40.5), degToRad(-50.5),
			     current_time,
			     &fake_waypoint, &crossing_time)) {
    printf("Sixth waypoint crossed at %ld (%ld) -> %s", crossing_time, 
	   crossing_time - current_time + 1000, ctime(&crossing_time));
  } else {
    printf("Edge case 6 failed\n");
  }
  return 0;
}
